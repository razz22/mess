{{-- Super Admin Navigation --}}
@auth
@if(Auth::user()->is_super_admin)
<li class="submenu-open">
    <h6 class="submenu-hdr" style="color:#dc3545;"><i class="ti ti-shield-check me-1"></i>{{ __('Super Admin') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-layout-dashboard fs-16 me-2"></i><span>{{ __('Admin Dashboard') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.users*') ? 'active' : '' }}">
            <a href="{{ route('admin.users') }}"><i class="ti ti-users fs-16 me-2"></i><span>{{ __('All Users') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.messes*') || Request::routeIs('admin.mess.*') ? 'active' : '' }}">
            <a href="{{ route('admin.messes') }}"><i class="ti ti-building-community fs-16 me-2"></i><span>{{ __('All Messes') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.upgrades*') || Request::routeIs('admin.upgrade.*') ? 'active' : '' }}">
            <a href="{{ route('admin.upgrades') }}" class="d-flex align-items-center gap-1">
                <i class="ti ti-rocket fs-16 me-2"></i><span>{{ __('Upgrades') }}</span>
                @php $pendingUpgrades = \App\Models\MessUpgrade::where('status','pending')->count(); @endphp
                @if($pendingUpgrades > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:10px;">{{ $pendingUpgrades }}</span>
                @endif
            </a>
        </li>
        <li class="{{ Request::routeIs('admin.plans*') ? 'active' : '' }}">
            <a href="{{ route('admin.plans') }}"><i class="ti ti-packages fs-16 me-2"></i><span>{{ __('Subscription Plans') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.settings') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}"><i class="ti ti-adjustments fs-16 me-2"></i><span>{{ __('System Settings') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.support.*') ? 'active' : '' }}">
            <a href="{{ route('admin.support.index') }}" class="d-flex align-items-center gap-1">
                <i class="ti ti-headset fs-16 me-2"></i><span>{{ __('Support Tickets') }}</span>
                @php $adminSupportUnread = \App\Models\SupportMessage::where('sender_type','user')->where('is_read',false)->count(); @endphp
                <span class="badge bg-danger ms-auto {{ $adminSupportUnread > 0 ? '' : 'd-none' }}" style="font-size:10px;">{{ $adminSupportUnread ?: '' }}</span>
            </a>
        </li>
        <li class="{{ Request::routeIs('admin.announcements.*') ? 'active' : '' }}">
            <a href="{{ route('admin.announcements.index') }}"><i class="ti ti-speakerphone fs-16 me-2"></i><span>{{ __('Announcements') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('admin.custom-subscriptions.*') ? 'active' : '' }}">
            <a href="{{ route('admin.custom-subscriptions.index') }}"><i class="ti ti-star fs-16 me-2"></i><span>{{ __('Custom Subscriptions') }}</span></a>
        </li>
    </ul>
</li>
@endif
@endauth

{{-- Mess Navigation --}}
@auth
@php
    $navUser = Auth::user();
    $navCanSeeList = $navUser->is_super_admin
        || $navUser->ownedMesses()->exists()
        || \App\Models\MessMember::where('user_id', $navUser->id)->where('is_active', true)->whereIn('role', ['owner','manager','author'])->exists();
    if (!isset($sidebarActiveMess)) {
        $sidebarActiveMess = $navUser->getActiveMess();
    }
    $sma       = $sidebarActiveMess ?? null;
    $isMember  = $sma ? $navUser->isBasicMemberOf($sma->id) : false;
    $isManager = $sma ? $navUser->isManagerOf($sma->id) : false;
    $isOwner   = $sma ? $navUser->isOwnerOf($sma->id) : false;
    $myMember  = $sma ? \App\Models\MessMember::where('mess_id', $sma->id)->where('user_id', $navUser->id)->first() : null;
@endphp

@if($navCanSeeList)
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Mess') }}</h6>
    <ul>
        <li class="{{ Request::is('mess') ? 'active' : '' }}">
            <a href="{{ route('mess.index') }}"><i class="ti ti-building-community fs-16 me-2"></i><span>{{ __('My Messes') }}</span></a>
        </li>
    </ul>
</li>
@endif

@if($sma)

<li class="py-1 px-2">
    <div class="d-flex align-items-center gap-2 px-2 py-1 rounded" style="background:rgba(0,0,0,0.05)">
        <i class="ti ti-door-enter text-primary"></i>
        <span class="small fw-semibold text-truncate">{{ $sma->name }}</span>
        @if($isMember && !$isManager)
        <span class="badge bg-secondary ms-auto" style="font-size:9px;">{{ __('Member') }}</span>
        @endif
    </div>
</li>

{{-- Overview --}}
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Overview') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.dashboard') ? 'active' : '' }}">
            <a href="{{ route('mess.dashboard', $sma->id) }}"><i class="ti ti-layout-dashboard fs-16 me-2"></i><span>{{ __('Dashboard') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.notices.*') ? 'active' : '' }}">
            <a href="{{ route('mess.notices.index', $sma->id) }}" class="d-flex align-items-center gap-1">
                <i class="ti ti-speakerphone fs-16 me-2"></i><span>{{ __('Notices') }}</span>
                <span class="badge bg-primary ms-auto d-none" id="sidebar-notice-badge" style="font-size:10px;"></span>
            </a>
        </li>
        <li class="{{ Request::routeIs('mess.rules') ? 'active' : '' }}">
            <a href="{{ route('mess.rules', $sma->id) }}"><i class="ti ti-list-check fs-16 me-2"></i><span>{{ __('Mess Rules') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.manager') ? 'active' : '' }}">
            <a href="{{ route('mess.manager', $sma->id) }}"><i class="ti ti-crown fs-16 me-2"></i><span>{{ __('Manager') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.rewards') ? 'active' : '' }}">
            <a href="{{ route('mess.rewards', $sma->id) }}"><i class="ti ti-trophy fs-16 me-2"></i><span>{{ __('Rewards') }}</span></a>
        </li>
    </ul>
</li>

{{-- Daily Activity --}}
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Daily Activity') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.meals') ? 'active' : '' }}">
            <a href="{{ route('mess.meals', $sma->id) }}"><i class="ti ti-tools-kitchen-2 fs-16 me-2"></i><span>{{ __('Meal Attendance') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.meal-routine') ? 'active' : '' }}">
            <a href="{{ route('mess.meal-routine', $sma->id) }}"><i class="ti ti-calendar-event fs-16 me-2"></i><span>{{ __('Meal Routine') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.market', 'mess.market.list') ? 'active' : '' }}">
            <a href="{{ route('mess.market', $sma->id) }}"><i class="ti ti-shopping-cart fs-16 me-2"></i><span>{{ __('Market Routine') }}</span></a>
        </li>
    </ul>
</li>

{{-- Finance (managers only) --}}
@if($isManager)
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Finance') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.expenses') ? 'active' : '' }}">
            <a href="{{ route('mess.expenses', $sma->id) }}"><i class="ti ti-coins fs-16 me-2"></i><span>{{ __('Expenses') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.deposits') ? 'active' : '' }}">
            <a href="{{ route('mess.deposits', $sma->id) }}"><i class="ti ti-cash fs-16 me-2"></i><span>{{ __('Deposits') }}</span></a>
        </li>
        @if($isOwner)
        <li class="{{ Request::routeIs('mess.rent.*') ? 'active' : '' }}">
            <a href="{{ route('mess.rent.index', $sma->id) }}"><i class="ti ti-home-dollar fs-16 me-2"></i><span>{{ __('House Rent') }}</span></a>
        </li>
        @endif
    </ul>
</li>
@endif

{{-- Members (managers only) --}}
@if($isManager)
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Members') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.members') ? 'active' : '' }}">
            <a href="{{ route('mess.members', $sma->id) }}"><i class="ti ti-users fs-16 me-2"></i><span>{{ __('All Members') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.leave.index') ? 'active' : '' }}">
            <a href="{{ route('mess.leave.index', $sma->id) }}"><i class="ti ti-clipboard-list fs-16 me-2"></i><span>{{ __('Leave Requests') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.show-causes.*') ? 'active' : '' }}">
            <a href="{{ route('mess.show-causes.index', $sma->id) }}"><i class="ti ti-file-alert fs-16 me-2"></i><span>{{ __('Show Cause') }}</span></a>
        </li>
    </ul>
</li>
@endif

{{-- My Account --}}
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('My Account') }}</h6>
    <ul>
        @if($myMember)
        <li class="{{ Request::routeIs('mess.members.profile') && Request::segment(4) == $myMember->id ? 'active' : '' }}">
            <a href="{{ route('mess.members.profile', [$sma->id, $myMember->id]) }}"><i class="ti ti-user-circle fs-16 me-2"></i><span>{{ __('My Profile') }}</span></a>
        </li>
        @endif
        <li class="{{ Request::routeIs('mess.leave.my') ? 'active' : '' }}">
            <a href="{{ route('mess.leave.my', $sma->id) }}"><i class="ti ti-logout fs-16 me-2"></i><span>{{ __('My Leave') }}</span></a>
        </li>
    </ul>
</li>

{{-- Reports --}}
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Reports') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.report.monthly') ? 'active' : '' }}">
            <a href="{{ route('mess.report.monthly', $sma->id) }}"><i class="ti ti-report-analytics fs-16 me-2"></i><span>{{ __('Monthly Report') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.report.members') ? 'active' : '' }}">
            <a href="{{ route('mess.report.members', $sma->id) }}"><i class="ti ti-flag fs-16 me-2"></i><span>{{ __('Member Reports') }}</span></a>
        </li>
    </ul>
</li>

{{-- Settings (managers only) --}}
@if($isManager)
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Settings') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.settings') ? 'active' : '' }}">
            <a href="{{ route('mess.settings', $sma->id) }}"><i class="ti ti-settings fs-16 me-2"></i><span>{{ __('Mess Settings') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.upgrade') ? 'active' : '' }}">
            <a href="{{ route('mess.upgrade', $sma->id) }}"><i class="ti ti-rocket fs-16 me-2"></i><span>{{ __('Upgrade Plan') }}</span></a>
        </li>
        <li class="{{ Request::routeIs('mess.upgrade.history') ? 'active' : '' }}">
            <a href="{{ route('mess.upgrade.history', $sma->id) }}"><i class="ti ti-history fs-16 me-2"></i><span>{{ __('Upgrade History') }}</span></a>
        </li>
    </ul>
</li>
@endif

{{-- Support Center (all mess users) --}}
<li class="submenu-open">
    <h6 class="submenu-hdr">{{ __('Support') }}</h6>
    <ul>
        <li class="{{ Request::routeIs('mess.support.*') ? 'active' : '' }}">
            <a href="{{ route('mess.support.index', $sma->id) }}" class="d-flex align-items-center gap-1">
                <i class="ti ti-headset fs-16 me-2"></i><span>{{ __('Support Center') }}</span>
                @php
                    $supportUnread = \App\Models\SupportMessage::whereHas('supportToken', fn($q) => $q->where('mess_id', $sma->id)->where('user_id', auth()->id()))
                        ->where('sender_type','admin')->where('is_read',false)->count();
                @endphp
                @if($supportUnread > 0)
                <span class="badge bg-primary ms-auto" style="font-size:10px;">{{ $supportUnread }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>

@endif {{-- end $sma --}}
@endauth
