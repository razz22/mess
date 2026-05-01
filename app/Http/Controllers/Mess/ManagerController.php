<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\ManagerNomination;
use App\Models\ManagerNominationVote;
use App\Models\ManagerRotation;
use App\Models\ManagerVote;
use App\Models\Mess;
use App\Models\MessMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    use AuthorizesMessAccess;
    public function index(Mess $mess)
    {
        $this->authorizeMember($mess);

        $filterMonth = request('month', now()->month);
        $filterYear  = request('year', now()->year);

        $rotationsQuery = ManagerRotation::where('mess_id', $mess->id)
            ->with(['user', 'votes.voter'])
            ->orderByDesc('year')->orderByDesc('month');

        $rotationsQuery->where('month', $filterMonth)->where('year', $filterYear);

        $rotations = $rotationsQuery->paginate(12)->withQueryString();

        $currentRotations = ManagerRotation::where('mess_id', $mess->id)
            ->where('is_current', true)
            ->with(['user', 'votes'])
            ->get();

        $members  = $mess->activeMembers()->with('user')->get();
        $member   = Auth::user()->getMembershipIn($mess->id);

        // Which current rotations the user has already voted on
        $votedRotationIds = ManagerVote::where('voter_id', Auth::id())
            ->whereIn('rotation_id', $currentRotations->pluck('id'))
            ->pluck('rotation_id')
            ->toArray();

        // Nominations — filterable by month/year
        $nomMonth = request('nom_month', now()->month);
        $nomYear  = request('nom_year',  now()->year);

        $nominations = ManagerNomination::where('mess_id', $mess->id)
            ->where('month', $nomMonth)
            ->where('year',  $nomYear)
            ->with(['nominee', 'nominator', 'votes.voter'])
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        $myNominationVoteIds = ManagerNominationVote::where('voter_id', Auth::id())
            ->whereIn('nomination_id', $nominations->pluck('id'))
            ->pluck('nomination_id')
            ->toArray();

        $alreadyNominated = $nominations->pluck('user_id')->toArray();

        return view('mess.manager', compact(
            'mess', 'rotations', 'currentRotations', 'members', 'member', 'votedRotationIds',
            'filterMonth', 'filterYear', 'nominations', 'myNominationVoteIds', 'alreadyNominated',
            'nomMonth', 'nomYear'
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

        // Check this member isn't already a manager for this month
        $already = ManagerRotation::where('mess_id', $mess->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($already) {
            return back()->with('error', 'This member is already assigned as manager for that month.');
        }

        // Mark past-month rotations for this user as no longer current
        ManagerRotation::where('mess_id', $mess->id)
            ->where('user_id', $request->user_id)
            ->where('is_current', true)
            ->where(function ($q) use ($request) {
                $q->where('year', '<', $request->year)
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('year', $request->year)
                         ->where('month', '<', $request->month);
                  });
            })
            ->update(['is_current' => false]);

        // Create a new rotation entry (multiple allowed per month)
        ManagerRotation::create([
            'mess_id'    => $mess->id,
            'user_id'    => $request->user_id,
            'month'      => $request->month,
            'year'       => $request->year,
            'is_current' => true,
            'started_at' => now(),
        ]);

        // Grant manager role without touching other managers
        MessMember::where('mess_id', $mess->id)
            ->where('user_id', $request->user_id)
            ->whereNotIn('role', ['owner'])
            ->update(['role' => 'manager']);

        return back()->with('success', 'Manager assigned for ' . date('F Y', mktime(0,0,0,$request->month,1,$request->year)) . '.');
    }

    public function remove(Request $request, Mess $mess, ManagerRotation $rotation)
    {
        $this->authorizeOwner($mess);

        if ((int) $rotation->mess_id !== (int) $mess->id) abort(403);

        // Revoke manager role only if no other current-or-future rotation exists for this user
        $otherActive = ManagerRotation::where('mess_id', $mess->id)
            ->where('user_id', $rotation->user_id)
            ->where('is_current', true)
            ->where('id', '!=', $rotation->id)
            ->where(function ($q) {
                $q->where('year', '>', now()->year)
                  ->orWhere(function ($q2) {
                      $q2->where('year', now()->year)
                         ->where('month', '>=', now()->month);
                  });
            })
            ->exists();

        $rotation->delete();

        if (!$otherActive) {
            MessMember::where('mess_id', $mess->id)
                ->where('user_id', $rotation->user_id)
                ->where('role', 'manager')
                ->update(['role' => 'member']);
        }

        return back()->with('success', 'Manager assignment removed successfully.');
    }

    public function vote(Request $request, Mess $mess, ManagerRotation $rotation)
    {
        $this->authorizeMember($mess);

        if ((int) $rotation->mess_id !== (int) $mess->id) abort(403);

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

    public function nominate(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2020|max:2100',
        ]);

        $exists = ManagerNomination::where('mess_id', $mess->id)
            ->where('user_id', $request->user_id)
            ->where('month', $request->month)->where('year', $request->year)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This member is already nominated for that month.');
        }

        ManagerNomination::create([
            'mess_id'      => $mess->id,
            'user_id'      => $request->user_id,
            'nominated_by' => Auth::id(),
            'month'        => $request->month,
            'year'         => $request->year,
        ]);

        return back()->with('success', 'Nomination submitted.');
    }

    public function voteNomination(Request $request, Mess $mess, ManagerNomination $nomination)
    {
        $this->authorizeMember($mess);

        if ((int) $nomination->mess_id !== (int) $mess->id) abort(403);

        if ($nomination->user_id === Auth::id()) {
            return back()->with('error', 'You cannot vote for yourself.');
        }

        $already = ManagerNominationVote::where('nomination_id', $nomination->id)
            ->where('voter_id', Auth::id())->exists();

        if ($already) {
            return back()->with('error', 'You have already voted for this nomination.');
        }

        ManagerNominationVote::create([
            'nomination_id' => $nomination->id,
            'voter_id'      => Auth::id(),
            'mess_id'       => $mess->id,
        ]);

        return back()->with('success', 'Vote cast!');
    }

    public function removeNomination(Request $request, Mess $mess, ManagerNomination $nomination)
    {
        if ((int) $nomination->mess_id !== (int) $mess->id) abort(403);

        $isOwner = $mess->owner_id === Auth::id() || optional(Auth::user()->getMembershipIn($mess->id))->role === 'owner';
        $isSelf  = $nomination->user_id === Auth::id() || $nomination->nominated_by === Auth::id();

        if (!$isOwner && !$isSelf) abort(403);

        $nomination->delete();

        return back()->with('success', 'Nomination removed.');
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
