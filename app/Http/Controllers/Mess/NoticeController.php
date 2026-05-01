<?php

namespace App\Http\Controllers\Mess;

use App\Events\NoticePublished;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\MessNotice;
use App\Models\MessNoticeRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    use AuthorizesMessAccess;

    public function index(Mess $mess)
    {
        $member    = $this->requireMember($mess);
        $canManage = $member && $member->canManage();
        $userId    = Auth::id();

        $filter = request('filter', 'all');

        $query = MessNotice::where('mess_id', $mess->id)->with('author');
        if (! $canManage) {
            $query->published();
        } elseif ($filter === 'published') {
            $query->where('status', 'published');
        } elseif ($filter === 'draft') {
            $query->where('status', 'draft');
        }

        $allNotices = $query->orderByDesc('published_at')->orderByDesc('created_at')->get();

        $perPage  = 10;
        $page     = request('page', 1);
        $total    = $allNotices->count();
        $notices  = new \Illuminate\Pagination\LengthAwarePaginator(
            $allNotices->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $readIds = MessNoticeRead::where('user_id', $userId)
            ->whereIn('notice_id', $allNotices->pluck('id'))
            ->pluck('notice_id')
            ->toArray();

        $baseQuery      = MessNotice::where('mess_id', $mess->id);
        $totalCount     = (clone $baseQuery)->count();
        $publishedCount = (clone $baseQuery)->where('status', 'published')->count();
        $draftCount     = (clone $baseQuery)->where('status', 'draft')->count();

        return view('mess.notices', compact('mess', 'member', 'canManage', 'notices', 'readIds', 'totalCount', 'publishedCount', 'draftCount'));
    }

    public function show(Mess $mess, MessNotice $notice)
    {
        $member = $this->requireMember($mess);
        abort_if((int) $notice->mess_id !== (int) $mess->id, 403);

        if ($notice->status !== 'published' && ! ($member && $member->canManage())) {
            abort(403, 'This notice is not published yet.');
        }

        MessNoticeRead::firstOrCreate(
            ['notice_id' => $notice->id, 'user_id' => Auth::id()],
            ['read_at'   => now()]
        );

        $canManage = $member && $member->canManage();
        $baseQ     = MessNotice::where('mess_id', $mess->id);
        if (! $canManage) $baseQ->published();

        $prev = (clone $baseQ)->where('published_at', '<', $notice->published_at ?? $notice->created_at)
                    ->orderByDesc('published_at')->orderByDesc('created_at')->first();
        $next = (clone $baseQ)->where('published_at', '>', $notice->published_at ?? $notice->created_at)
                    ->orderBy('published_at')->orderBy('created_at')->first();

        return view('mess.notice-show', compact('mess', 'member', 'notice', 'prev', 'next'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->requireManager($mess);

        $data = $request->validate([
            'title'  => 'required|string|max:255',
            'body'   => 'required|string|max:65000',
            'status' => 'required|in:draft,published',
        ]);

        $notice = MessNotice::create([
            'mess_id'      => $mess->id,
            'created_by'   => Auth::id(),
            'title'        => $data['title'],
            'body'         => $data['body'],
            'status'       => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        if ($notice->status === 'published') {
            try { NoticePublished::dispatch($notice->load('author')); } catch (\Throwable) {}
        }

        return redirect()->route('mess.notices.index', $mess->id)
            ->with('success', $data['status'] === 'published'
                ? __('Notice published successfully.')
                : __('Notice saved as draft.'));
    }

    public function update(Request $request, Mess $mess, MessNotice $notice)
    {
        $this->requireManager($mess);
        abort_if((int) $notice->mess_id !== (int) $mess->id, 403);

        $data = $request->validate([
            'title'  => 'required|string|max:255',
            'body'   => 'required|string|max:65000',
            'status' => 'required|in:draft,published',
        ]);

        $wasPublished     = $notice->status === 'published';
        $becomesPublished = $data['status'] === 'published';

        $notice->update([
            'title'        => $data['title'],
            'body'         => $data['body'],
            'status'       => $data['status'],
            'published_at' => (! $wasPublished && $becomesPublished) ? now() : $notice->published_at,
        ]);

        if (! $wasPublished && $becomesPublished) {
            try { NoticePublished::dispatch($notice->fresh()->load('author')); } catch (\Throwable) {}
        }

        return redirect()->route('mess.notices.index', $mess->id)
            ->with('success', __('Notice updated.'));
    }

    public function destroy(Mess $mess, MessNotice $notice)
    {
        $this->requireManager($mess);
        abort_if((int) $notice->mess_id !== (int) $mess->id, 403);
        $notice->delete();

        return back()->with('success', __('Notice deleted.'));
    }

    /** AJAX — latest published notices for the bell dropdown */
    public function latest(Mess $mess)
    {
        if (!Auth::check()) {
            return response()->json(['notices' => [], 'unread' => 0]);
        }
        $member = Auth::user()->getMembershipIn($mess->id);
        if (!$member) {
            return response()->json(['notices' => [], 'unread' => 0]);
        }
        $userId = Auth::id();

        $notices = MessNotice::where('mess_id', $mess->id)
            ->published()
            ->with('author')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get()
            ->map(function ($n) use ($userId, $mess) {
                return [
                    'id'           => $n->id,
                    'title'        => $n->title,
                    'body_preview' => Str::limit(strip_tags($n->body), 90),
                    'published_at' => $n->published_at->diffForHumans(),
                    'author'       => $n->author->name,
                    'is_read'      => $n->reads()->where('user_id', $userId)->exists(),
                    'url'          => route('mess.notices.show', [$mess->id, $n->id]),
                ];
            });

        $unread = $notices->where('is_read', false)->count();

        return response()->json(['notices' => $notices, 'unread' => $unread]);
    }

    /** AJAX — mark single notice read */
    public function markRead(Mess $mess, MessNotice $notice)
    {
        $this->requireMember($mess);
        abort_if((int) $notice->mess_id !== (int) $mess->id, 403);

        MessNoticeRead::firstOrCreate(
            ['notice_id' => $notice->id, 'user_id' => Auth::id()],
            ['read_at'   => now()]
        );

        return response()->json(['ok' => true]);
    }

    /** AJAX — mark all notices read */
    public function markAllRead(Mess $mess)
    {
        if (!Auth::check() || !Auth::user()->getMembershipIn($mess->id)) {
            return response()->json(['ok' => false]);
        }
        $userId = Auth::id();

        MessNotice::where('mess_id', $mess->id)
            ->published()
            ->whereNotIn('id', function ($q) use ($userId) {
                $q->select('notice_id')->from('mess_notice_reads')->where('user_id', $userId);
            })
            ->pluck('id')
            ->each(function ($noticeId) use ($userId) {
                MessNoticeRead::firstOrCreate(
                    ['notice_id' => $noticeId, 'user_id' => $userId],
                    ['read_at'   => now()]
                );
            });

        return response()->json(['ok' => true]);
    }
}
