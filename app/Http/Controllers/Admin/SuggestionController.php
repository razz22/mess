<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminMessageSent;
use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        // Get all messes that have at least one message, ordered by latest message
        $threads = Mess::whereHas('adminMessages')
            ->with(['owner', 'adminMessages' => fn($q) => $q->latest()->limit(1)])
            ->withCount(['adminMessages as unread_count' => fn($q) => $q->where('is_read', false)->where('sender_role', 'owner')])
            ->get()
            ->sortByDesc(fn($m) => optional($m->adminMessages->first())->created_at)
            ->values();

        $selectedMess = null;
        $messages     = collect();

        if ($request->mess_id) {
            $selectedMess = Mess::with('owner')->find($request->mess_id);
            if ($selectedMess) {
                // Mark incoming owner messages as read
                AdminMessage::where('mess_id', $selectedMess->id)
                    ->where('sender_role', 'owner')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = AdminMessage::where('mess_id', $selectedMess->id)
                    ->orderBy('created_at')
                    ->get();
            }
        }

        $totalUnread = AdminMessage::where('sender_role', 'owner')->where('is_read', false)->count();

        return view('admin.suggestions', compact('threads', 'selectedMess', 'messages', 'totalUnread'));
    }

    public function reply(Request $request, Mess $mess)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $request->validate(['message' => 'required|string|max:5000']);

        $msg = AdminMessage::create([
            'mess_id'     => $mess->id,
            'owner_id'    => $mess->owner_id,
            'sender_role' => 'admin',
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        try { AdminMessageSent::dispatch($msg->load('mess', 'owner')); } catch (\Throwable) {}

        if ($request->ajax()) return response()->json(['ok' => true]);
        return back()->with('success', __('Message sent.'));
    }

    public function block(Request $request, User $user)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $request->validate([
            'block_reason' => 'nullable|string|max:500',
            'block_type'   => 'required|in:temporary,permanent',
            'blocked_until'=> 'required_if:block_type,temporary|nullable|date|after:now',
        ]);

        $user->update([
            'is_blocked'    => true,
            'blocked_at'    => now(),
            'block_reason'  => $request->block_reason,
            'blocked_until' => $request->block_type === 'temporary' ? $request->blocked_until : null,
        ]);

        return back()->with('success', __('User has been blocked.'));
    }

    public function unblock(User $user)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $user->update([
            'is_blocked'    => false,
            'blocked_at'    => null,
            'block_reason'  => null,
            'blocked_until' => null,
        ]);

        return back()->with('success', __('User has been unblocked.'));
    }
}
