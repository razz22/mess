<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\ManagerVote;
use App\Models\MemberReward;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    use AuthorizesMessAccess;
    public function index(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $rewards = MemberReward::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with(['user', 'awardedBy'])
            ->orderByDesc('points')
            ->get();

        // Leaderboard
        $leaderboard = MemberReward::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->selectRaw('user_id, SUM(points) as total_points')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_points')
            ->get();

        // Manager rewards based on votes
        $currentRotation = $mess->currentManager;
        $managerRating   = $currentRotation ? $currentRotation->getAverageRating() : 0;

        $members = $mess->activeMembers()->with('user')->get();
        $member  = Auth::user()->getMembershipIn($mess->id);

        return view('mess.rewards', compact(
            'mess', 'rewards', 'leaderboard', 'members', 'member',
            'month', 'year', 'currentRotation', 'managerRating'
        ));
    }

    public function store(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'type'             => 'required|in:manager_reward,member_reward,best_market',
            'points'           => 'required|numeric|min:0',
            'reason'           => 'nullable|string|max:500',
            'gift_description' => 'nullable|string|max:255',
            'month'            => 'required|integer|min:1|max:12',
            'year'             => 'required|integer|min:2020|max:2100',
        ]);

        MemberReward::create([
            'mess_id'          => $mess->id,
            'user_id'          => $request->user_id,
            'month'            => $request->month,
            'year'             => $request->year,
            'type'             => $request->type,
            'points'           => $request->points,
            'reason'           => $request->reason,
            'gift_description' => $request->gift_description,
            'awarded_by'       => Auth::id(),
        ]);

        return back()->with('success', 'Reward awarded!');
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }
}
