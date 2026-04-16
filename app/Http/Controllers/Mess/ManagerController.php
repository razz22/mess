<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\ManagerRotation;
use App\Models\ManagerVote;
use App\Models\Mess;
use App\Models\MessMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function index(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $rotations = ManagerRotation::where('mess_id', $mess->id)
            ->with(['user', 'votes.voter'])
            ->orderByDesc('year')->orderByDesc('month')
            ->paginate(12);

        $currentRotation = ManagerRotation::where('mess_id', $mess->id)
            ->where('is_current', true)
            ->with(['user', 'votes'])
            ->first();

        $members  = $mess->activeMembers()->with('user')->get();
        $member   = Auth::user()->getMembershipIn($mess->id);

        // Check if current user has voted for current rotation
        $hasVoted = false;
        if ($currentRotation) {
            $hasVoted = ManagerVote::where('rotation_id', $currentRotation->id)
                ->where('voter_id', Auth::id())
                ->exists();
        }

        return view('mess.manager', compact(
            'mess', 'rotations', 'currentRotation', 'members', 'member', 'hasVoted'
        ));
    }

    public function assign(Request $request, Mess $mess)
    {
        $this->authorizeOwner($mess);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2020|max:2100',
        ]);

        // End current rotation
        ManagerRotation::where('mess_id', $mess->id)
            ->where('is_current', true)
            ->update(['is_current' => false, 'ended_at' => now()]);

        // Reset current manager role
        MessMember::where('mess_id', $mess->id)
            ->where('role', 'manager')
            ->update(['role' => 'member']);

        // Create new rotation
        $rotation = ManagerRotation::updateOrCreate(
            ['mess_id' => $mess->id, 'month' => $request->month, 'year' => $request->year],
            [
                'user_id'    => $request->user_id,
                'is_current' => true,
                'started_at' => now(),
                'ended_at'   => null,
            ]
        );

        // Set new manager role
        MessMember::where('mess_id', $mess->id)
            ->where('user_id', $request->user_id)
            ->update(['role' => 'manager']);

        return back()->with('success', 'Manager assigned for ' . date('F Y', mktime(0,0,0,$request->month,1,$request->year)) . '.');
    }

    public function vote(Request $request, Mess $mess, ManagerRotation $rotation)
    {
        $this->authorizeMember($mess);

        if ($rotation->mess_id !== $mess->id) abort(403);

        // Can't vote for yourself
        if ($rotation->user_id === Auth::id()) {
            return back()->with('error', 'You cannot vote for yourself.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        ManagerVote::updateOrCreate(
            ['rotation_id' => $rotation->id, 'voter_id' => Auth::id()],
            [
                'mess_id' => $mess->id,
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Vote submitted!');
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }

    private function authorizeOwner(Mess $mess): void
    {
        $isOwner = $mess->owner_id === Auth::id();
        if (! $isOwner) {
            $member  = Auth::user()->getMembershipIn($mess->id);
            $isOwner = $member && $member->role === 'owner';
        }
        if (! $isOwner) abort(403);
    }
}
