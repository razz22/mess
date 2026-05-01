<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\SupportToken;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    use AuthorizesMessAccess;

    public function index(Mess $mess)
    {
        $member = $this->requireMember($mess);
        $user   = Auth::user();

        $tokens = SupportToken::where('mess_id', $mess->id)
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->each(fn($t) => $t->status === 'open' && $t->isExpired() && $t->update(['status' => 'expired']));

        $todayCount = SupportToken::where('mess_id', $mess->id)
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return view('mess.support.index', compact('mess', 'member', 'tokens', 'todayCount'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->requireMember($mess);
        $user = Auth::user();

        $todayCount = SupportToken::where('mess_id', $mess->id)
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 2) {
            return back()->withErrors(['subject' => __('You have reached the daily limit of 2 support tokens.')]);
        }

        $request->validate(['subject' => 'required|string|max:255']);

        SupportToken::create([
            'mess_id'    => $mess->id,
            'user_id'    => $user->id,
            'token'      => strtoupper(Str::random(8)),
            'subject'    => $request->subject,
            'expires_at' => now()->addHour(),
        ]);

        return redirect()->route('mess.support.index', $mess)->with('success', __('Support token created. You can now send up to 2 messages before it expires.'));
    }

    public function show(Mess $mess, SupportToken $supportToken)
    {
        $member = $this->requireMember($mess);
        $user   = Auth::user();

        abort_if($supportToken->mess_id !== $mess->id || $supportToken->user_id !== $user->id, 403);

        if ($supportToken->status === 'open' && $supportToken->isExpired()) {
            $supportToken->update(['status' => 'expired']);
            $supportToken->refresh();
        }

        // Mark admin messages as read
        SupportMessage::where('support_token_id', $supportToken->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $supportToken->messages()->with('sender')->orderBy('created_at')->get();
        $token    = $supportToken;

        return view('mess.support.show', compact('mess', 'member', 'token', 'messages'));
    }

    public function message(Request $request, Mess $mess, SupportToken $supportToken)
    {
        $this->requireMember($mess);
        $user = Auth::user();

        abort_if($supportToken->mess_id !== $mess->id || $supportToken->user_id !== $user->id, 403);

        if ($supportToken->status === 'open' && $supportToken->isExpired()) {
            $supportToken->update(['status' => 'expired']);
        }

        if (!$supportToken->userCanMessage()) {
            return back()->withErrors(['message' => __('This token is expired or you have used all your messages.')]);
        }

        $request->validate([
            'message'   => 'required|string|max:2000',
            'image'     => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file      = $request->file('image');
            $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/support'), $filename);
            $imagePath = 'uploads/support/' . $filename;
        }

        SupportMessage::create([
            'support_token_id' => $supportToken->id,
            'sender_id'        => $user->id,
            'sender_type'      => 'user',
            'message'          => $request->message,
            'image_path'       => $imagePath,
        ]);

        $supportToken->increment('user_message_count');

        return redirect()->route('mess.support.show', [$mess, $supportToken])->with('success', __('Message sent successfully.'));
    }
}
