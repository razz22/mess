<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\Expense;
use App\Models\Mess;
use App\Models\MemberDeposit;
use App\Models\MessMember;
use App\Models\MessSubscription;
use App\Models\MessUpgrade;
use App\Models\RentPayment;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function dashboard()
    {
        $stats = [
            'total_users'         => User::where('is_super_admin', false)->count(),
            'active_users'        => User::where('is_super_admin', false)->where('is_active', true)->count(),
            'total_messes'        => Mess::count(),
            'total_members'       => MessMember::where('is_active', true)->count(),
            'total_rent_collected'=> RentPayment::whereIn('payment_type', ['rent', 'penalty', 'adjustment'])->sum('amount'),
            'total_advance_held'  => AdvancePayment::selectRaw(
                'SUM(CASE WHEN transaction_type = "received" THEN amount ELSE -amount END) as net'
            )->value('net') ?? 0,
            'new_users_this_month' => User::where('is_super_admin', false)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'new_messes_this_month' => Mess::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Monthly user registrations (last 6 months)
        $userGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userGrowth[] = [
                'month' => $date->format('M Y'),
                'count' => User::where('is_super_admin', false)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        $recentUsers  = User::where('is_super_admin', false)
            ->with('messMembers')
            ->latest()
            ->limit(8)
            ->get();

        $recentMesses = Mess::with('owner')
            ->withCount('activeMembers')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'userGrowth', 'recentUsers', 'recentMesses'
        ));
    }

    // -------------------------------------------------------------------------
    // User Management
    // -------------------------------------------------------------------------

    public function users(Request $request)
    {
        $query = User::where('is_super_admin', false)
            ->withCount('messMembers')
            ->with('ownedMesses');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active');
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'is_active'=> 'boolean',
        ]);

        $data['password']   = Hash::make($data['password']);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['is_super_admin'] = false;

        $user = User::create($data);

        return back()->with('success', "User {$user->name} created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'is_active'=> 'nullable|boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $data['is_active'] = $request->boolean('is_active', $user->is_active);
        $user->update($data);

        return back()->with('success', "User {$user->name} updated.");
    }

    public function toggleActive(User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'Cannot deactivate a super admin.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$user->name} has been {$status}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "Password reset for {$user->name}.");
    }

    // -------------------------------------------------------------------------
    // Mess Management
    // -------------------------------------------------------------------------

    public function messes(Request $request)
    {
        $query = Mess::with('owner')
            ->withCount('activeMembers');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('owner', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $messes = $query->latest()->paginate(20)->withQueryString();
        $allUsers = User::where('is_active', true)->where('is_super_admin', false)->orderBy('name')->get();

        return view('admin.messes', compact('messes', 'allUsers'));
    }

    public function storeMess(Request $request)
    {
        $data = $request->validate([
            'owner_id'    => 'required|exists:users,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address'     => 'nullable|string|max:500',
        ]);

        $owner = User::findOrFail($data['owner_id']);

        $sysSettings = SystemSetting::instance();

        $mess = Mess::create([
            'owner_id'    => $owner->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'address'     => $data['address'] ?? null,
            'max_members' => $sysSettings->default_max_members,
        ]);

        // Auto-add owner as member with owner role
        MessMember::create([
            'mess_id'   => $mess->id,
            'user_id'   => $owner->id,
            'role'      => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Mess \"{$mess->name}\" created for {$owner->name}.");
    }

    public function destroyMess(Mess $mess)
    {
        if ($mess->avatar) {
            Storage::disk('public')->delete($mess->avatar);
        }

        $mess->delete();

        return back()->with('success', "Mess \"{$mess->name}\" deleted.");
    }

    // -------------------------------------------------------------------------
    // Mess Detail — manage members of any mess
    // -------------------------------------------------------------------------

    public function showMess(Mess $mess)
    {
        $mess->load(['owner', 'members.user']);
        $members     = $mess->members()->where('is_active', true)->with('user')->orderBy('role')->get();
        $allUsers    = User::where('is_active', true)->where('is_super_admin', false)->orderBy('name')->get();

        $monthPayments = RentPayment::where('mess_id', $mess->id)
            ->where('month', now()->month)->where('year', now()->year)
            ->with('member.user')->get();

        $paidByMember = $members->mapWithKeys(fn($m) => [
            $m->id => $monthPayments->where('member_id', $m->id)
                ->whereIn('payment_type', ['rent', 'penalty', 'adjustment'])->sum('amount')
                - $monthPayments->where('member_id', $m->id)
                    ->where('payment_type', 'discount')->sum('amount'),
        ]);

        return view('admin.mess-detail', compact(
            'mess', 'members', 'allUsers', 'paidByMember'
        ));
    }

    public function addMemberToMess(Request $request, Mess $mess)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|in:member,author,manager',
        ]);

        $existing = MessMember::where('mess_id', $mess->id)
            ->where('user_id', $data['user_id'])->first();

        if ($existing) {
            $existing->update(['is_active' => true, 'role' => $data['role']]);
        } else {
            MessMember::create([
                'mess_id'   => $mess->id,
                'user_id'   => $data['user_id'],
                'role'      => $data['role'],
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $user = User::find($data['user_id']);
        return back()->with('success', "{$user->name} added to {$mess->name}.");
    }

    public function updateMemberRole(Request $request, Mess $mess, MessMember $member)
    {
        abort_if($member->mess_id !== $mess->id, 403);

        $data = $request->validate(['role' => 'required|in:member,author,manager,owner']);

        // Only one owner allowed — demote old owner if promoting new one
        if ($data['role'] === 'owner') {
            MessMember::where('mess_id', $mess->id)->where('role', 'owner')
                ->update(['role' => 'member']);
            $mess->update(['owner_id' => $member->user_id]);
        }

        $member->update(['role' => $data['role']]);
        return back()->with('success', 'Role updated.');
    }

    public function removeMemberFromMess(Mess $mess, MessMember $member)
    {
        abort_if($member->mess_id !== $mess->id, 403);

        if ($member->role === 'owner') {
            return back()->with('error', 'Cannot remove the owner. Transfer ownership first.');
        }

        $member->update(['is_active' => false]);
        return back()->with('success', 'Member removed from mess.');
    }

    // -------------------------------------------------------------------------
    // Upgrade Management
    // -------------------------------------------------------------------------

    public function upgrades(Request $request)
    {
        $query = MessUpgrade::with(['mess', 'user', 'reviewer'])->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $upgrades = $query->paginate(20)->withQueryString();
        $pendingCount = MessUpgrade::where('status', 'pending')->count();

        return view('admin.upgrades', compact('upgrades', 'pendingCount'));
    }

    public function approveUpgrade(Request $request, MessUpgrade $upgrade)
    {
        if ($upgrade->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $upgrade->load('plan', 'mess');
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        \DB::transaction(function () use ($upgrade, $request) {
            $upgrade->mess->update(['max_members' => $upgrade->requested_limit]);

            $upgrade->update([
                'status'      => 'approved',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            if ($upgrade->plan_id && $upgrade->plan) {
                // Cancel any existing active subscription for this mess
                MessSubscription::where('mess_id', $upgrade->mess_id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);

                MessSubscription::create([
                    'mess_id'     => $upgrade->mess_id,
                    'plan_id'     => $upgrade->plan_id,
                    'user_id'     => $upgrade->user_id,
                    'plan'        => $upgrade->plan->name,
                    'max_members' => $upgrade->requested_limit,
                    'amount'      => $upgrade->amount,
                    'starts_at'   => now(),
                    'expires_at'  => now()->addMonths($upgrade->plan->duration_months),
                    'status'      => 'active',
                    'notes'       => "Plan: {$upgrade->plan->name} | Tx: {$upgrade->transaction_id}",
                ]);
            }
        });

        return back()->with('success', "Upgrade approved. {$upgrade->mess->name} limit set to {$upgrade->requested_limit} members.");
    }

    public function rejectUpgrade(Request $request, MessUpgrade $upgrade)
    {
        if ($upgrade->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $upgrade->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Upgrade request rejected.");
    }

    // Super admin direct limit change (no payment required)
    public function setMessLimit(Request $request, Mess $mess)
    {
        $request->validate([
            'max_members' => 'required|integer|min:1|max:1000',
        ]);

        $old = $mess->max_members;
        $mess->update(['max_members' => $request->max_members]);

        // Log as an admin-granted upgrade
        MessUpgrade::create([
            'mess_id'         => $mess->id,
            'user_id'         => $mess->owner_id,
            'current_limit'   => $old,
            'requested_limit' => $request->max_members,
            'amount'          => 0,
            'status'          => 'approved',
            'admin_notes'     => 'Directly set by super admin.',
            'reviewed_by'     => Auth::id(),
            'reviewed_at'     => now(),
        ]);

        return back()->with('success', "Member limit updated to {$request->max_members}.");
    }

    // Super admin set per-user mess creation limit
    public function setUserMessLimit(Request $request, User $user)
    {
        $request->validate(['max_messes' => 'required|integer|min:1|max:20']);
        $user->update(['max_messes' => $request->max_messes]);
        return back()->with('success', "{$user->name} can now create up to {$request->max_messes} messes.");
    }

    // -------------------------------------------------------------------------
    // System Settings
    // -------------------------------------------------------------------------

    public function settings()
    {
        $settings = SystemSetting::instance();
        return view('admin.settings', compact('settings'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->orderBy('price')->get();
        return view('admin.plans', compact('plans'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'default_max_members'  => 'required|integer|min:1|max:1000',
            'default_max_messes'   => 'required|integer|min:1|max:50',
            'google_client_id'     => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:255',
            'google_login_enabled' => 'nullable|boolean',
        ]);

        $data['google_login_enabled'] = $request->boolean('google_login_enabled');

        // Don't overwrite the existing secret if the field was left blank
        if (empty($data['google_client_secret'])) {
            unset($data['google_client_secret']);
        }

        SystemSetting::instance()->update($data);

        return back()->with('success', 'System settings updated.');
    }

    // ---- Subscription Plan CRUD ----

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string|max:1000',
            'max_members'     => 'required|integer|min:1|max:1000',
            'price'           => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1|max:24',
            'sort_order'      => 'nullable|integer|min:0|max:255',
        ]);

        $data['is_active']   = true;
        $data['is_featured'] = false;
        $data['sort_order'] ??= 0;

        SubscriptionPlan::create($data);

        return back()->with('success', "Subscription plan \"{$data['name']}\" created.");
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'description'     => 'nullable|string|max:1000',
            'max_members'     => 'required|integer|min:1|max:1000',
            'price'           => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1|max:24',
            'sort_order'      => 'nullable|integer|min:0|max:255',
            'is_active'       => 'nullable|boolean',
            'is_featured'     => 'nullable|boolean',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] ??= 0;

        // Only one plan can be featured at a time
        if ($data['is_featured']) {
            SubscriptionPlan::where('id', '!=', $plan->id)->update(['is_featured' => false]);
        }

        $plan->update($data);

        return back()->with('success', "Plan \"{$plan->name}\" updated.");
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        $plan->delete();
        return back()->with('success', "Plan deleted.");
    }

    // -------------------------------------------------------------------------
    // Impersonation
    // -------------------------------------------------------------------------

    public function impersonate(User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'Cannot impersonate another super admin.');
        }

        session(['impersonating_admin_id' => Auth::id()]);
        Auth::loginUsingId($user->id);

        return redirect()->route('mess.index')
            ->with('info', "You are now viewing as {$user->name}.");
    }

    public function exitImpersonation()
    {
        $adminId = session('impersonating_admin_id');
        if (!$adminId) {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('impersonating_admin_id');
        Auth::loginUsingId($adminId);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Returned to super admin account.');
    }
}
