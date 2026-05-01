<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\MessUpgrade;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
    use AuthorizesMessAccess;
    // Owner or manager: view upgrade page (plan cards only)
    public function index(Mess $mess)
    {
        $this->authorizeOwnerOrManager($mess);

        $pendingUpgrade = MessUpgrade::where('mess_id', $mess->id)
            ->where('status', 'pending')
            ->with('plan')
            ->first();
        $plans     = SubscriptionPlan::active()->get();
        $activeSub = \App\Models\MessSubscription::where('mess_id', $mess->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        $customSub   = \App\Models\CustomSubscription::active()->forMess($mess->id)->first();
        $bkashNumber = SystemSetting::instance()->bkash_number;

        return view('mess.upgrade', compact('mess', 'pendingUpgrade', 'plans', 'activeSub', 'customSub', 'bkashNumber'));
    }

    // Owner or manager: upgrade history DataTable page
    public function history(Mess $mess)
    {
        $this->authorizeOwnerOrManager($mess);

        $upgrades = MessUpgrade::where('mess_id', $mess->id)
            ->with(['reviewer', 'plan'])
            ->orderByDesc('created_at')
            ->get();

        return view('mess.upgrade-history', compact('mess', 'upgrades'));
    }

    // Owner or manager: submit upgrade request with bKash payment
    public function store(Request $request, Mess $mess)
    {
        $this->authorizeOwnerOrManager($mess);

        $request->validate([
            'plan_id'         => 'nullable|exists:subscription_plans,id',
            'requested_limit' => 'required|integer|min:1|max:1000',
            'amount'          => 'required|numeric|min:0',
            'bkash_number'    => 'required|string|max:20',
            'transaction_id'  => 'required|string|max:100',
        ]);

        // Prevent duplicate pending request
        $pending = MessUpgrade::where('mess_id', $mess->id)->where('status', 'pending')->exists();
        if ($pending) {
            return back()->with('error', 'You already have a pending upgrade request. Please wait for it to be reviewed.');
        }

        MessUpgrade::create([
            'mess_id'         => $mess->id,
            'plan_id'         => $request->plan_id ?: null,
            'user_id'         => Auth::id(),
            'current_limit'   => $mess->max_members,
            'requested_limit' => $request->requested_limit,
            'amount'          => $request->amount,
            'bkash_number'    => $request->bkash_number,
            'transaction_id'  => $request->transaction_id,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Upgrade request submitted. Admin will review and approve shortly.');
    }

    private function authorizeOwnerOrManager(Mess $mess): void
    {
        $user   = Auth::user();
        $member = $user->getMembershipIn($mess->id);

        if ($user->is_super_admin) return;

        if (!$member || !in_array($member->role, ['owner', 'manager'])) {
            abort(403, 'Only the mess owner or manager can request upgrades.');
        }
    }
}
