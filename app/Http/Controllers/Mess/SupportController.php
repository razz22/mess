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

    public function show(Mess $mess, SupportToken $token)
    {
        $member = $this->requireMember($mess);
        $user   = Auth::user();

        abort_if($token->mess_id !== $mess->id || $token->user_id !== $user->id, 403);

        if ($token->status === 'open' && $token->isExpired()) {
            $token->update(['status' => 'expired']);
            $token->refresh();
        }

        // Mark admin messages as read
        SupportMessage::where('support_token_id', $token->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $token->messages()->with('sender')->orderBy('created_at')->get();

        return view('mess.support.show', compact('mess', 'member', 'token', 'messages'));
    }

    public function message(Request $request, Mess $mess, SupportToken $token)
    {
        $this->requireMember($mess);
        $user = Auth::user();

        abort_if($token->mess_id !== $mess->id || $token->user_id !== $user->id, 403);

        if ($token->status === 'open' && $token->isExpired()) {
            $token->update(['status' => 'expired']);
        }

        if (!$token->userCanMessage()) {
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
            'support_token_id' => $token->id,
            'sender_id'        => $user->id,
            'sender_type'      => 'user',
            'message'          => $request->message,
            'image_path'       => $imagePath,
        ]);

        $token->increment('user_message_count');

        return redirect()->route('mess.support.show', [$mess, $token])->with('success', __('Message sent successfully.'));
    }
}
