<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\MessShowCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowCauseController extends Controller
{
    /** Manager: list all show causes for this mess */
    public function index(Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $causes = MessShowCause::where('mess_id', $mess->id)
            ->with(['member.user', 'issuedBy'])
            ->latest('issued_at')
            ->get();

        $members = $mess->activeMembers()->with('user')->get();

        return view('mess.show-causes', compact('mess', 'causes', 'members'));
    }

    /** Manager: issue a new show cause letter */
    public function store(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'member_id' => 'required|exists:mess_members,id',
            'subject'   => 'required|string|max:200',
            'body'      => 'required|string|max:5000',
        ]);

        $member = MessMember::findOrFail($request->member_id);
        if ($member->mess_id !== $mess->id) abort(403);
        if ($member->role === 'owner') {
            return back()->with('error', 'Cannot issue a show cause to the owner.');
        }

        MessShowCause::create([
            'mess_id'   => $mess->id,
            'member_id' => $member->id,
            'issued_by' => Auth::id(),
            'subject'   => $request->subject,
            'body'      => $request->body,
            'status'    => 'pending',
            'issued_at' => now(),
        ]);

        return back()->with('success', 'Show cause letter issued to ' . $member->user->name . '.');
    }

    /** View a single show cause (manager or the concerned member) */
    public function show(Mess $mess, MessShowCause $cause)
    {
        if ($cause->mess_id !== $mess->id) abort(403);

        $authUser = Auth::user();
        $isManager = $authUser->isManagerOf($mess->id);
        $isConcernedMember = $cause->member->user_id === $authUser->id;

        if (!$isManager && !$isConcernedMember) abort(403);

        return view('mess.show-cause-detail', compact('mess', 'cause', 'isManager', 'isConcernedMember'));
    }

    /** Member: submit their reply */
    public function memberReply(Request $request, Mess $mess, MessShowCause $cause)
    {
        if ($cause->mess_id !== $mess->id) abort(403);
        if ($cause->member->user_id !== Auth::id()) abort(403);
        if ($cause->status !== 'pending') {
            return back()->with('error', 'You have already replied or this letter is closed.');
        }

        $request->validate(['member_reply' => 'required|string|max:5000']);

        $cause->update([
            'member_reply' => $request->member_reply,
            'status'       => 'replied',
            'replied_at'   => now(),
        ]);

        return back()->with('success', 'Your reply has been submitted.');
    }

    /** Manager/Owner: submit final reply and close */
    public function finalReply(Request $request, Mess $mess, MessShowCause $cause)
    {
        if ($cause->mess_id !== $mess->id) abort(403);
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ($cause->status !== 'replied') {
            return back()->with('error', 'Member has not replied yet, or letter is already closed.');
        }

        $request->validate(['final_reply' => 'required|string|max:5000']);

        $cause->update([
            'final_reply'      => $request->final_reply,
            'status'           => 'closed',
            'final_replied_at' => now(),
        ]);

        return back()->with('success', 'Final reply submitted. Show cause closed.');
    }

    /** Manager: delete a show cause */
    public function destroy(Mess $mess, MessShowCause $cause)
    {
        if ($cause->mess_id !== $mess->id) abort(403);
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $cause->delete();
        return back()->with('success', 'Show cause deleted.');
    }
}
