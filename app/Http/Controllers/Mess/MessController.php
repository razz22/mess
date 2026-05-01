<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\ExpenseCategory;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\MessSetting;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessController extends Controller
{
    use AuthorizesMessAccess;
    public function index()
    {
        $user = Auth::user();

        // Super admin sees every mess on the platform
        if ($user->is_super_admin) {
            $memberships = collect(); // no real memberships
            $allMesses   = Mess::with('owner')->withCount('activeMembers')->latest()->get();
            return view('mess.list', compact('memberships', 'allMesses'));
        }

        $memberships = MessMember::with('mess')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        // Pure members (no owner/manager role anywhere) go straight to their mess dashboard
        $hasOwnerOrManager = $memberships->whereIn('role', ['owner', 'manager', 'author'])->isNotEmpty();
        if (!$hasOwnerOrManager && $memberships->isNotEmpty()) {
            $activeMess = $user->getActiveMess();
            if (!$activeMess) {
                $activeMess = $memberships->first()->mess;
                session(['active_mess_id' => $activeMess->id]);
            }
            return redirect()->route('mess.dashboard', $activeMess->id);
        }

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
        $maxMesses = $user->max_messes ?? 2;
        if ($user->ownedMesses()->count() >= $maxMesses) {
            return redirect()->route('mess.index')
                ->with('error', "You can only create up to {$maxMesses} messes.");
        }

        $sysSettings = \App\Models\SystemSetting::instance();
        return view('mess.create', compact('sysSettings'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $activeMess = $user->getActiveMess();
        if ($activeMess && $user->isBasicMemberOf($activeMess->id)) {
            return redirect()->route('mess.index')
                ->with('error', 'Members cannot create a mess.');
        }
        $sysSettings = SystemSetting::instance();
        $maxMesses   = $user->max_messes ?? $sysSettings->default_max_messes;
        if ($user->ownedMesses()->count() >= $maxMesses) {
            return redirect()->route('mess.index')
                ->with('error', "You can only create up to {$maxMesses} messes.");
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address'     => 'nullable|string|max:500',
            'avatar'      => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        try {
            if (!empty($validated['avatar'])
                && $validated['avatar'] instanceof \Illuminate\Http\UploadedFile
                && $validated['avatar']->getRealPath() !== false
                && $validated['avatar']->getRealPath() !== '') {
                $avatarPath = $validated['avatar']->store('mess-avatars', 'public');
            }
        } catch (\Throwable $e) {
            // Avatar upload failed — proceed without avatar
        }

        $mess = Mess::create([
            'owner_id'    => $user->id,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'address'     => $validated['address'] ?? null,
            'avatar'      => $avatarPath,
            'max_members' => $sysSettings->default_max_members,
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
        $currentManagers = $mess->currentManagers()->with('user')->get();
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

        // Today's meal routine (all types)
        $todayWeekNo    = \App\Models\MessMealRoutine::weekNoForDate(now());
        $todayDayOfWeek = (int) now()->format('w');
        $allRoutines = \App\Models\MessMealRoutine::where('mess_id', $mess->id)->get();
        $todayMealRoutines = $allRoutines->where('week_no', $todayWeekNo)->where('day_of_week', $todayDayOfWeek)->values();
        // Full grid keyed by [meal_type][week_no][day_of_week]
        $routineGrid = [];
        foreach ($allRoutines as $r) {
            $routineGrid[$r->meal_type][$r->week_no][$r->day_of_week] = $r->items;
        }
        $routineMealTypes = $allRoutines->pluck('meal_type')->unique()->values();
        // All configured meal types for this mess
        $messMealTypes = \App\Models\MessMealType::where('mess_id', $mess->id)->orderBy('id')->pluck('name');

        // Calendar days for the routine modal (current month, padded Sun–Sat)
        $routineMonthStart = now()->startOfMonth();
        $routineCalStart   = $routineMonthStart->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $routineCalEnd     = now()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $routineCalDays    = [];
        $cur = $routineCalStart->copy();
        while ($cur <= $routineCalEnd) { $routineCalDays[] = $cur->copy(); $cur->addDay(); }

        // Chart data for dashboard: current month summaries + expense categories
        $dashMembers = $mess->activeMembers()->with('user')->get();
        $dashSummaries = \App\Models\MemberMonthlySummary::where([
            'mess_id' => $mess->id, 'month' => $currentMonth, 'year' => $currentYear,
        ])->get()->keyBy('user_id');
        $dashChartMemberNames = $dashMembers->map(fn($m) => \Str::limit($m->user->name, 10))->values();
        $dashChartMealDays    = $dashMembers->map(fn($m) => $dashSummaries[$m->user_id]?->total_meal_days ?? 0)->values();
        $dashChartPayables    = $dashMembers->map(fn($m) => $dashSummaries[$m->user_id]?->total_payable ?? 0)->values();
        $dashExpenseCats = \App\Models\Expense::where('mess_id', $mess->id)
            ->whereMonth('expense_date', $currentMonth)->whereYear('expense_date', $currentYear)
            ->where('is_market_expense', false)->with('category')->get()
            ->groupBy('category_id')
            ->map(fn($g) => ['name' => $g->first()->category?->name ?? 'Uncategorized', 'total' => $g->sum('amount')])
            ->values();
        $dashChartCatNames  = $dashExpenseCats->pluck('name');
        $dashChartCatTotals = $dashExpenseCats->pluck('total');

        // Pending show causes for the authenticated member (awaiting their reply)
        $myMessMember = $member; // already loaded
        $pendingShowCauses = $myMessMember && $myMessMember->id
            ? \App\Models\MessShowCause::where('mess_id', $mess->id)
                ->where('member_id', $myMessMember->id)
                ->where('status', 'pending')
                ->with('issuedBy')
                ->latest('issued_at')
                ->get()
            : collect();

        // Replied show causes for manager/owner (awaiting their final reply)
        $repliedShowCauses = collect();
        if ($myMessMember && in_array($myMessMember->role, ['owner', 'manager'])) {
            $repliedShowCauses = \App\Models\MessShowCause::where('mess_id', $mess->id)
                ->where('status', 'replied')
                ->with(['member.user'])
                ->latest('replied_at')
                ->get();
        }

        $announcements = \App\Models\Announcement::active()->forMess($mess->id)->latest()->get();
        $customSub     = \App\Models\CustomSubscription::active()->forMess($mess->id)->first();

        // Notices published within the last 24 hours for the marquee ticker
        $recentNotices = \App\Models\MessNotice::where('mess_id', $mess->id)
            ->where('status', 'published')
            ->where('published_at', '>=', now()->subHours(24))
            ->orderByDesc('published_at')
            ->get();

        return view('mess.dashboard', compact(
            'mess', 'member', 'currentManagers', 'todayMeals',
            'todayRoutine', 'monthlyExpenses', 'monthlyDeposits',
            'totalMembers', 'pendingReports', 'mySummary',
            'pendingShowCauses', 'repliedShowCauses',
            'todayMealRoutines', 'routineGrid', 'routineMealTypes', 'messMealTypes',
            'todayWeekNo', 'todayDayOfWeek',
            'routineMonthStart', 'routineCalDays',
            'dashChartMemberNames', 'dashChartMealDays', 'dashChartPayables',
            'dashChartCatNames', 'dashChartCatTotals',
            'announcements', 'customSub', 'recentNotices'
        ));
    }

    public function edit(Mess $mess)
    {
        $this->authorizeOwnerOrManager($mess);
        $settings = $mess->settings ?? new MessSetting(['mess_id' => $mess->id]);
        $contacts = \App\Models\MessContact::where('mess_id', $mess->id)->orderBy('label')->orderBy('name')->get();
        return view('mess.settings', compact('mess', 'settings', 'contacts'));
    }

    public function update(Request $request, Mess $mess)
    {
        $this->authorizeOwnerOrManager($mess);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string|max:1000',
            'address'              => 'nullable|string|max:500',
            'avatar'               => 'nullable|image|max:2048',
            'leave_notice_months'  => 'nullable|integer|min:1|max:6',
        ]);

        if (!empty($validated['avatar']) && $validated['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            if ($mess->avatar) Storage::disk('public')->delete($mess->avatar);
            $validated['avatar'] = $validated['avatar']->store('mess-avatars', 'public');
        } else {
            unset($validated['avatar']); // don't overwrite existing avatar if none uploaded
        }

        if (isset($validated['leave_notice_months'])) {
            $mess->leave_notice_months = $validated['leave_notice_months'];
        }
        unset($validated['leave_notice_months']);
        $mess->update($validated);

        // Update settings
        $mess->settings()->updateOrCreate(['mess_id' => $mess->id], [
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
        if ($user->is_super_admin) return;
        if (!$user->getMembershipIn($mess->id)) {
            abort(redirect()->route('mess.index')->with('error', 'You are not a member of this mess.'));
        }
    }

    private function authorizeOwner(Mess $mess): void
    {
        $user = Auth::user();
        if ($user->is_super_admin) return;
        if ($mess->owner_id !== $user->id) {
            abort(redirect()->route('mess.index')->with('error', 'Only the owner can do this.'));
        }
    }

    private function authorizeOwnerOrManager(Mess $mess): void
    {
        $user   = Auth::user();
        $member = $user->getMembershipIn($mess->id);

        if ($user->is_super_admin) return;

        if (! $member || ! in_array($member->role, ['owner', 'manager'])) {
            abort(redirect()->route('mess.index')->with('error', 'Only the mess owner or manager can access settings.'));
        }
    }
}
