<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportToken;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $query = SupportToken::with(['mess', 'user', 'messages'])
            ->withCount(['messages as unread_count' => fn($q) => $q->where('sender_type', 'user')->where('is_read', false)])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
                  ->orWhereHas('mess', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
                  ->orWhere('token', 'like', '%'.$request->search.'%')
                  ->orWhere('subject', 'like', '%'.$request->search.'%');
        }

        $tokens      = $query->paginate(20)->withQueryString();
        $totalUnread = SupportMessage::where('sender_type', 'user')->where('is_read', false)->count();

        return view('admin.support.index', compact('tokens', 'totalUnread'));
    }

    public function show(SupportToken $token)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        // Auto-expire
        if ($token->status === 'open' && $token->isExpired()) {
            $token->update(['status' => 'expired']);
            $token->refresh();
        }

        // Mark user messages as read
        SupportMessage::where('support_token_id', $token->id)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $token->messages()->with('sender')->orderBy('created_at')->get();
        $token->load(['mess', 'user']);

        $role = $this->getUserRole($token);

        return view('admin.support.show', compact('token', 'messages', 'role'));
    }

    public function reply(Request $request, SupportToken $token)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        if ($token->status === 'open' && $token->isExpired()) {
            $token->update(['status' => 'expired']);
        }

        if ($token->status === 'closed') {
            return back()->withErrors(['message' => __('This ticket is closed.')]);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'image'   => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file      = $request->file('image');
            $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/support'), $filename);
            $imagePath = 'uploads/support/' . $filename;
        }

        SupportMessage::create([
            'support_token_id' => $token->id,
            'sender_id'        => Auth::id(),
            'sender_type'      => 'admin',
            'message'          => $request->message,
            'image_path'       => $imagePath,
        ]);

        return redirect()->route('admin.support.show', $token)->with('success', __('Reply sent.'));
    }

    public function close(SupportToken $token)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $token->update(['status' => 'closed']);
        return back()->with('success', __('Ticket closed.'));
    }

    private function getUserRole(SupportToken $token): string
    {
        $member = \App\Models\MessMember::where('mess_id', $token->mess_id)
            ->where('user_id', $token->user_id)
            ->first();

        if (!$member) return 'Owner';
        return ucfirst($member->role ?? 'Member');
    }
}
