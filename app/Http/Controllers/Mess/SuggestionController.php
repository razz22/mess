<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Events\AdminMessageSent;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\AdminMessage;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    use AuthorizesMessAccess;

    public function index(Mess $mess)
    {
        $this->requireOwner($mess);

        $user = Auth::user();

        // Auto-expire temporary blocks
        if ($user->is_blocked && $user->blocked_until && now()->gt($user->blocked_until)) {
            $user->update(['is_blocked' => false, 'blocked_at' => null, 'block_reason' => null, 'blocked_until' => null]);
            $user->refresh();
        }

        // Mark admin messages as read
        AdminMessage::where('mess_id', $mess->id)
            ->where('sender_role', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = AdminMessage::where('mess_id', $mess->id)
            ->orderBy('created_at')
            ->get();

        $member = Auth::user()->getMembershipIn($mess->id);
        return view('mess.suggestions', compact('mess', 'member', 'messages', 'user'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->requireOwner($mess);

        $user = Auth::user();

        // Auto-expire block
        if ($user->is_blocked && $user->blocked_until && now()->gt($user->blocked_until)) {
            $user->update(['is_blocked' => false, 'blocked_at' => null, 'block_reason' => null, 'blocked_until' => null]);
            $user->refresh();
        }

        if ($user->is_blocked) {
            if ($request->ajax()) return response()->json(['error' => __('You are currently blocked and cannot send messages.')], 403);
            return back()->withErrors(['message' => __('You are currently blocked and cannot send messages.')]);
        }

        $request->validate(['message' => 'required|string|max:5000']);

        $msg = AdminMessage::create([
            'mess_id'     => $mess->id,
            'owner_id'    => $user->id,
            'sender_role' => 'owner',
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        try { AdminMessageSent::dispatch($msg->load('mess', 'owner')); } catch (\Throwable) {}

        if ($request->ajax()) return response()->json(['ok' => true]);
        return back()->with('success', __('Message sent to admin.'));
    }
}
