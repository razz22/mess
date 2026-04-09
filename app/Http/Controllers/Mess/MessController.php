<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\MessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $memberships = MessMember::with('mess')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        return view('mess.list', compact('memberships'));
    }

    public function create()
    {
        $user = Auth::user();
        $activeMess = $user->getActiveMess();
        if ($activeMess && $user->isBasicMemberOf($activeMess->id)) {
            return redirect()->route('mess.index')
                ->with('error', 'Members cannot create a mess.');
        }
        if ($user->ownedMesses()->count() >= 2) {
            return redirect()->route('mess.index')
                ->with('error', 'You can only create up to 2 messes.');
        }

        return view('mess.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $activeMess = $user->getActiveMess();
        if ($activeMess && $user->isBasicMemberOf($activeMess->id)) {
            return redirect()->route('mess.index')
                ->with('error', 'Members cannot create a mess.');
        }
        if ($user->ownedMesses()->count() >= 2) {
            return redirect()->route('mess.index')
                ->with('error', 'You can only create up to 2 messes.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address'     => 'nullable|string|max:500',
            'avatar'      => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('mess-avatars', 'public');
        }

        $mess = Mess::create([
            'owner_id'    => $user->id,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'address'     => $validated['address'] ?? null,
            'avatar'      => $avatarPath,
            'max_members' => 20,
        ]);

        // Add owner as member
        MessMember::create([
            'mess_id'   => $mess->id,
            'user_id'   => $user->id,
            'role'      => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Create default settings
        MessSetting::create([
            'mess_id'         => $mess->id,
            'breakfast_close' => '09:00:00',
            'lunch_close'     => '14:00:00',
            'dinner_close'    => '21:00:00',
            'monthly_rate'    => 0,
            'allow_meal_off'  => true,
            'auto_meal_on'    => true,
        ]);

        // Create default expense categories
        $defaultCategories = [
            ['name' => 'Food / Groceries', 'icon' => 'ti-basket', 'color' => 'success'],
            ['name' => 'Electricity',       'icon' => 'ti-bolt',   'color' => 'warning'],
            ['name' => 'Gas',               'icon' => 'ti-flame',  'color' => 'danger'],
            ['name' => 'Cook Bill',         'icon' => 'ti-chef-hat','color' => 'info'],
            ['name' => 'Service Charge',    'icon' => 'ti-settings','color' => 'secondary'],
            ['name' => 'Internet',          'icon' => 'ti-wifi',   'color' => 'primary'],
            ['name' => 'Water',             'icon' => 'ti-droplet','color' => 'info'],
            ['name' => 'Others',            'icon' => 'ti-coins',  'color' => 'dark'],
        ];

        foreach ($defaultCategories as $cat) {
            ExpenseCategory::create(array_merge($cat, [
                'mess_id'    => $mess->id,
                'is_default' => true,
            ]));
        }

        // Create default meal types
        $defaultMealTypes = [
            ['name' => 'Breakfast', 'close_time' => '09:00:00', 'sort_order' => 1],
            ['name' => 'Lunch',     'close_time' => '14:00:00', 'sort_order' => 2],
            ['name' => 'Dinner',    'close_time' => '21:00:00', 'sort_order' => 3],
        ];
        foreach ($defaultMealTypes as $mt) {
            \App\Models\MessMealType::create(array_merge($mt, ['mess_id' => $mess->id]));
        }

        session(['active_mess_id' => $mess->id]);

        return redirect()->route('mess.dashboard', $mess->id)
            ->with('success', 'Mess created successfully!');
    }

    public function show(Mess $mess)
    {
        $this->authorizeMember($mess);
        session(['active_mess_id' => $mess->id]);

        $member = Auth::user()->getMembershipIn($mess->id);
        $currentManager = $mess->currentManager()->with('user')->first();
        $today = now()->toDateString();

        $todayMeals = $mess->mealSchedules()
            ->where('date', $today)
            ->with('attendances')
            ->get();

        $todayRoutine = $mess->marketRoutines()
            ->where('start_date', $today)
            ->with('assignedTo')
            ->first();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyExpenses = $mess->expenses()
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $monthlyDeposits = $mess->deposits()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');

        $totalMembers = $mess->activeMembers()->count();

        $pendingReports = $mess->memberReports()->where('status', 'pending')->count();

        $mySummary = \App\Models\MemberMonthlySummary::where([
            'mess_id' => $mess->id,
            'user_id' => Auth::id(),
            'month'   => $currentMonth,
            'year'    => $currentYear,
        ])->first();

        return view('mess.dashboard', compact(
            'mess', 'member', 'currentManager', 'todayMeals',
            'todayRoutine', 'monthlyExpenses', 'monthlyDeposits',
            'totalMembers', 'pendingReports', 'mySummary'
        ));
    }

    public function edit(Mess $mess)
    {
        $this->authorizeOwner($mess);
        $settings = $mess->settings ?? new MessSetting(['mess_id' => $mess->id]);
        return view('mess.settings', compact('mess', 'settings'));
    }

    public function update(Request $request, Mess $mess)
    {
        $this->authorizeOwner($mess);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address'     => 'nullable|string|max:500',
            'avatar'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($mess->avatar) Storage::disk('public')->delete($mess->avatar);
            $validated['avatar'] = $request->file('avatar')->store('mess-avatars', 'public');
        }

        $mess->update($validated);

        // Update settings
        $mess->settings()->updateOrCreate(['mess_id' => $mess->id], [
            'breakfast_close' => $request->breakfast_close ?? '09:00:00',
            'lunch_close'     => $request->lunch_close ?? '14:00:00',
            'dinner_close'    => $request->dinner_close ?? '21:00:00',
            'monthly_rate'    => 0,
            'meal_cost_mode'  => in_array($request->meal_cost_mode, ['monthly', 'daily']) ? $request->meal_cost_mode : 'monthly',
            'allow_meal_off'  => $request->boolean('allow_meal_off'),
            'auto_meal_on'    => $request->boolean('auto_meal_on'),
        ]);

        return redirect()->route('mess.settings', $mess->id)
            ->with('success', 'Mess settings updated!');
    }

    public function destroy(Mess $mess)
    {
        $this->authorizeOwner($mess);
        $mess->delete();
        return redirect()->route('mess.index')->with('success', 'Mess deleted.');
    }

    public function select(Request $request)
    {
        $messId = $request->mess_id;
        $user = Auth::user();
        $member = $user->getMembershipIn($messId);

        if (!$member) {
            return redirect()->route('mess.index')->with('error', 'Access denied.');
        }

        session(['active_mess_id' => $messId]);
        return redirect()->route('mess.dashboard', $messId);
    }

    public function joinByCode(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);
        $mess = Mess::where('invite_code', strtoupper($request->invite_code))
            ->where('status', 'active')
            ->first();

        if (!$mess) {
            return back()->with('error', 'Invalid invite code.');
        }

        $user = Auth::user();
        $existing = $user->getMembershipIn($mess->id);
        if ($existing) {
            return redirect()->route('mess.dashboard', $mess->id)->with('info', 'Already a member.');
        }

        $maxMembers = $mess->getEffectiveMaxMembers();
        if ($mess->getMemberCount() >= $maxMembers) {
            return back()->with('error', 'This mess has reached its member limit.');
        }

        MessMember::create([
            'mess_id'   => $mess->id,
            'user_id'   => $user->id,
            'role'      => 'member',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        session(['active_mess_id' => $mess->id]);
        return redirect()->route('mess.dashboard', $mess->id)->with('success', 'Joined mess successfully!');
    }

    private function authorizeMember(Mess $mess): void
    {
        $user = Auth::user();
        if (!$user->getMembershipIn($mess->id)) {
            abort(403, 'You are not a member of this mess.');
        }
    }

    private function authorizeOwner(Mess $mess): void
    {
        if ($mess->owner_id !== Auth::id()) {
            abort(403, 'Only the owner can do this.');
        }
    }
}
