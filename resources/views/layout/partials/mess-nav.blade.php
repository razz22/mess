{{-- Mess Management Navigation --}}
<li class="submenu-open">
        <h6 class="submenu-hdr">Mess Management</h6>
        <ul>
                <li class="{{ Request::is('mess') ? 'active' : '' }}">
                        <a href="{{ route('mess.index') }}"><i class="ti ti-building-community fs-16 me-2"></i><span>My Messes</span></a>
                </li>
                @auth
                @php
                        if (!isset($sidebarActiveMess)) {
                                $sidebarActiveMess = Auth::user()->getActiveMess();
                        }
                        $sma        = $sidebarActiveMess ?? null;
                        $isMember   = $sma ? Auth::user()->isBasicMemberOf($sma->id) : false;
                        $isManager  = $sma ? Auth::user()->isManagerOf($sma->id) : false;
                @endphp
                @if($sma)
                <li class="py-1">
                        <div class="d-flex align-items-center gap-2 px-2 py-1 rounded" style="background:rgba(0,0,0,0.05)">
                                <i class="ti ti-door-enter text-primary"></i>
                                <span class="small fw-semibold text-truncate">{{ $sma->name }}</span>
                                @if($isMember)
                                <span class="badge bg-secondary ms-auto" style="font-size:9px;">Member</span>
                                @endif
                        </div>
                </li>

                {{-- Dashboard — everyone --}}
                <li class="{{ Request::routeIs('mess.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('mess.dashboard', $sma->id) }}"><i class="ti ti-layout-dashboard fs-16 me-2"></i><span>Dashboard</span></a>
                </li>

                {{-- Meal Attendance — everyone --}}
                <li class="{{ Request::routeIs('mess.meals') ? 'active' : '' }}">
                        <a href="{{ route('mess.meals', $sma->id) }}"><i class="ti ti-tools-kitchen-2 fs-16 me-2"></i><span>Meal Attendance</span></a>
                </li>

                {{-- Meal Items Kanban — managers only --}}
                @if($isManager)
                <li class="{{ Request::routeIs('mess.meal-items') ? 'active' : '' }}">
                        <a href="{{ route('mess.meal-items', $sma->id) }}"><i class="ti ti-layout-kanban fs-16 me-2"></i><span>Meal Items</span></a>
                </li>
                @endif

                {{-- Market Routine — everyone --}}
                <li class="{{ Request::routeIs('mess.market', 'mess.market.list') ? 'active' : '' }}">
                        <a href="{{ route('mess.market', $sma->id) }}"><i class="ti ti-shopping-cart fs-16 me-2"></i><span>Market Routine</span></a>
                </li>

                {{-- Expenses — managers only --}}
                @if($isManager)
                <li class="{{ Request::routeIs('mess.expenses') ? 'active' : '' }}">
                        <a href="{{ route('mess.expenses', $sma->id) }}"><i class="ti ti-coins fs-16 me-2"></i><span>Expenses</span></a>
                </li>
                @endif

                {{-- Deposits — managers only --}}
                @if($isManager)
                <li class="{{ Request::routeIs('mess.deposits') ? 'active' : '' }}">
                        <a href="{{ route('mess.deposits', $sma->id) }}"><i class="ti ti-cash fs-16 me-2"></i><span>Deposits</span></a>
                </li>
                @endif

                {{-- Members — managers only --}}
                @if($isManager)
                <li class="{{ Request::routeIs('mess.members') ? 'active' : '' }}">
                        <a href="{{ route('mess.members', $sma->id) }}"><i class="ti ti-users fs-16 me-2"></i><span>Members</span></a>
                </li>
                @endif

                {{-- Manager Rotation — everyone (members can vote) --}}
                <li class="{{ Request::routeIs('mess.manager') ? 'active' : '' }}">
                        <a href="{{ route('mess.manager', $sma->id) }}"><i class="ti ti-crown fs-16 me-2"></i><span>Manager</span></a>
                </li>

                {{-- Monthly Report — everyone --}}
                <li class="{{ Request::routeIs('mess.report.monthly') ? 'active' : '' }}">
                        <a href="{{ route('mess.report.monthly', $sma->id) }}"><i class="ti ti-report-analytics fs-16 me-2"></i><span>Monthly Report</span></a>
                </li>

                {{-- Rewards — everyone --}}
                <li class="{{ Request::routeIs('mess.rewards') ? 'active' : '' }}">
                        <a href="{{ route('mess.rewards', $sma->id) }}"><i class="ti ti-trophy fs-16 me-2"></i><span>Rewards</span></a>
                </li>

                {{-- Member Reports — everyone --}}
                <li class="{{ Request::routeIs('mess.report.members') ? 'active' : '' }}">
                        <a href="{{ route('mess.report.members', $sma->id) }}"><i class="ti ti-flag fs-16 me-2"></i><span>Member Reports</span></a>
                </li>

                {{-- Settings — owner only --}}
                @if(Auth::user()->isOwnerOf($sma->id))
                <li class="{{ Request::routeIs('mess.settings') ? 'active' : '' }}">
                        <a href="{{ route('mess.settings', $sma->id) }}"><i class="ti ti-settings fs-16 me-2"></i><span>Mess Settings</span></a>
                </li>
                @endif

                @endif
                @endauth
        </ul>
</li>
