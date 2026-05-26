	
@if(!Route::is(['pos','pos-2','pos-3','pos-4','pos-5']))
<div class="header">
    <div class="main-header">
        <!-- Logo -->
        <div class="header-left active">
            <a href="{{url('index')}}" class="logo logo-normal">
                <img src="{{URL::asset('build/img/logo.svg')}}" alt="Img">
            </a>
            <a href="{{url('index')}}" class="logo logo-white">
                <img src="{{URL::asset('build/img/logo-white.svg')}}" alt="Img">
            </a>
            <a href="{{url('index')}}" class="logo-small">
                <img src="{{URL::asset('build/img/logo-small.png')}}" alt="Img">
            </a>
        </div>
        <!-- /Logo -->
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <!-- Header Menu -->
        <ul class="nav user-menu">

           <!-- Search -->
					<li class="nav-item nav-searchinputs">
						<div class="top-nav-search">
							<a href="javascript:void(0);" class="responsive-search">
								<i class="fa fa-search"></i>
							</a>
							<div class="dropdown">
								<div class="searchinputs input-group" id="appSearchWrap">
									<input type="text" id="appSearchInput" placeholder="Search menu…" autocomplete="off">
									<div class="search-addon">
										<span><i class="ti ti-search"></i></span>
									</div>
									<span class="input-group-text">
										<kbd class="d-flex align-items-center"><img src="{{URL::asset('build/img/icons/command.svg')}}" alt="img" class="me-1">K</kbd>
									</span>
								</div>
								<div id="appSearchDropdown" style="display:none;position:absolute;top:calc(100% + 6px);left:0;width:420px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.13);border:1px solid #e9ecef;z-index:9999;overflow:hidden">
									<!-- Default: quick nav grid -->
									<div id="appSearchDefault">
										<div style="padding:12px 14px 6px;font-size:11px;font-weight:700;color:#6c757d;letter-spacing:.06em;text-transform:uppercase">Quick Navigation</div>
										<div id="appQuickGrid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;padding:0 12px 12px"></div>
										<div style="border-top:1px solid #f0f0f0;padding:8px 14px;font-size:11px;color:#adb5bd;display:flex;align-items:center;gap:6px">
											<kbd style="font-size:10px;padding:1px 5px;border-radius:4px;background:#f5f5f5;border:1px solid #ddd">Ctrl</kbd>+<kbd style="font-size:10px;padding:1px 5px;border-radius:4px;background:#f5f5f5;border:1px solid #ddd">K</kbd>
											<span>to open anywhere &nbsp;·&nbsp;</span>
											<kbd style="font-size:10px;padding:1px 5px;border-radius:4px;background:#f5f5f5;border:1px solid #ddd">Esc</kbd> <span>to close</span>
										</div>
									</div>
									<!-- Search results -->
									<div id="appSearchResults" style="display:none;max-height:320px;overflow-y:auto"></div>
									<!-- Empty state -->
									<div id="appSearchEmpty" style="display:none;padding:24px;text-align:center;color:#adb5bd">
										<i class="ti ti-mood-sad" style="font-size:28px;display:block;margin-bottom:6px"></i>
										<span style="font-size:13px">No results for this search</span>
									</div>
								</div>
							</div>
						</div>
					</li>
					<!-- /Search -->
@php
$_messId = session('active_mess_id') ?? (auth()->check() ? optional(auth()->user()->messMembers()->first())->mess_id : null);
@endphp
<script>
(function(){
const mid = {{ $_messId ? (int)$_messId : 'null' }};
const R = {
    'mess.index':              '/mess',
    'mess.create':             '/mess/create',
    'mess.join':               '/mess/join',
    'mess.dashboard':          mid ? `/mess/${mid}` : null,
    'mess.settings':           mid ? `/mess/${mid}/settings` : null,
    'mess.members':            mid ? `/mess/${mid}/members` : null,
    'mess.meals':              mid ? `/mess/${mid}/meals` : null,
    'mess.meal-routine':       mid ? `/mess/${mid}/meal-routine` : null,
    'mess.meal-items':         mid ? `/mess/${mid}/meal-items` : null,
    'mess.market':             mid ? `/mess/${mid}/market` : null,
    'mess.expenses':           mid ? `/mess/${mid}/expenses` : null,
    'mess.deposits':           mid ? `/mess/${mid}/deposits` : null,
    'mess.manager':            mid ? `/mess/${mid}/manager` : null,
    'mess.report.monthly':     mid ? `/mess/${mid}/report/monthly` : null,
    'mess.report.members':     mid ? `/mess/${mid}/report/members` : null,
    'mess.rewards':            mid ? `/mess/${mid}/rewards` : null,
    'mess.rent.index':         mid ? `/mess/${mid}/rent` : null,
    'mess.leave.index':        mid ? `/mess/${mid}/leave` : null,
    'mess.leave.my':           mid ? `/mess/${mid}/leave/my` : null,
    'mess.show-causes.index':  mid ? `/mess/${mid}/show-causes` : null,
    'mess.tenant-forms.index': mid ? `/mess/${mid}/tenant-forms` : null,
};

// color per item: [bg, icon-color]
const MENU = [
    { label:'Dashboard',        icon:'ti-dashboard',          route:'mess.dashboard',          color:['#e8f4fd','#1976d2'], tags:['home','overview'] },
    { label:'Members',          icon:'ti-users',              route:'mess.members',            color:['#e8f5e9','#388e3c'], tags:['people','team'] },
    { label:'Meal Attendance',  icon:'ti-calendar-check',     route:'mess.meals',              color:['#fff3e0','#f57c00'], tags:['meal','food','lunch','dinner','attendance'] },
    { label:'Meal Routine',     icon:'ti-calendar-week',      route:'mess.meal-routine',       color:['#fce4ec','#c2185b'], tags:['menu','weekly','schedule','routine'] },
    { label:'Meal Items',       icon:'ti-bowl-spoon',         route:'mess.meal-items',         color:['#f3e5f5','#7b1fa2'], tags:['food list','items'] },
    { label:'Market',           icon:'ti-shopping-cart',      route:'mess.market',             color:['#e8f5e9','#2e7d32'], tags:['market','shopping','bazar','assign'] },
    { label:'Expenses',         icon:'ti-receipt',            route:'mess.expenses',           color:['#fbe9e7','#bf360c'], tags:['expense','cost','spending'] },
    { label:'Deposits',         icon:'ti-piggy-bank',         route:'mess.deposits',           color:['#e1f5fe','#0277bd'], tags:['deposit','money','fund','payment'] },
    { label:'Monthly Report',   icon:'ti-chart-bar',          route:'mess.report.monthly',     color:['#e8eaf6','#3949ab'], tags:['report','monthly','summary','bill'] },
    { label:'Member Reports',   icon:'ti-clipboard-list',     route:'mess.report.members',     color:['#e0f2f1','#00695c'], tags:['individual','statement'] },
    { label:'Rewards',          icon:'ti-trophy',             route:'mess.rewards',            color:['#fff8e1','#f9a825'], tags:['reward','point','badge','leaderboard'] },
    { label:'Manager Rotation', icon:'ti-refresh',            route:'mess.manager',            color:['#fce4ec','#ad1457'], tags:['manager','rotation','duty'] },
    { label:'House Rent',       icon:'ti-home',               route:'mess.rent.index',         color:['#e8f5e9','#388e3c'], tags:['rent','house','invoice'] },
    { label:'Leave Requests',   icon:'ti-beach',              route:'mess.leave.index',        color:['#e0f7fa','#00838f'], tags:['leave','absent','vacation'] },
    { label:'My Leave',         icon:'ti-user-off',           route:'mess.leave.my',           color:['#f3e5f5','#6a1b9a'], tags:['my leave','personal'] },
    { label:'Show Causes',      icon:'ti-alert-triangle',     route:'mess.show-causes.index',  color:['#fff3e0','#e65100'], tags:['notice','warning'] },
    { label:'Tenant Forms',     icon:'ti-forms',              route:'mess.tenant-forms.index', color:['#e8eaf6','#283593'], tags:['tenant','contract','agreement'] },
    { label:'Settings',         icon:'ti-settings',           route:'mess.settings',           color:['#f5f5f5','#546e7a'], tags:['config','edit mess'] },
    { label:'My Messes',        icon:'ti-building-community', route:'mess.index',              color:['#e8f4fd','#1565c0'], tags:['home','mess','list'] },
    { label:'Create Mess',      icon:'ti-plus-circle',        route:'mess.create',             color:['#e8f5e9','#1b5e20'], tags:['add','new','create'] },
    { label:'Join a Mess',      icon:'ti-door-enter',         route:'mess.join',               color:['#fff3e0','#bf360c'], tags:['join','code','invite'] },
].map(m => ({...m, url: R[m.route]})).filter(m => m.url);

// Build quick-nav grid (first 8)
const grid = document.getElementById('appQuickGrid');
MENU.slice(0,8).forEach(m => {
    grid.insertAdjacentHTML('beforeend', `
        <a href="${m.url}" style="display:flex;flex-direction:column;align-items:center;gap:5px;padding:10px 6px;border-radius:10px;background:${m.color[0]};text-decoration:none;transition:transform .15s,box-shadow .15s"
           onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
           onmouseout="this.style.transform='';this.style.boxShadow=''">
            <span style="width:36px;height:36px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,.08)">
                <i class="ti ${m.icon}" style="font-size:16px;color:${m.color[1]}"></i>
            </span>
            <span style="font-size:10px;font-weight:600;color:#444;text-align:center;line-height:1.2">${m.label}</span>
        </a>`);
});

const input    = document.getElementById('appSearchInput');
const dropdown = document.getElementById('appSearchDropdown');
const results  = document.getElementById('appSearchResults');
const defBlock = document.getElementById('appSearchDefault');
const emptyEl  = document.getElementById('appSearchEmpty');

let focusIdx = -1;

function show(){ dropdown.style.display='block'; }
function hide(){ dropdown.style.display='none'; focusIdx=-1; }

function renderResults(matched, q) {
    const hl = (str) => str.replace(new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi'), '<mark style="background:#fff3cd;padding:0 1px;border-radius:2px">$1</mark>');
    return `<div style="padding:8px 12px 4px;font-size:11px;font-weight:700;color:#6c757d;letter-spacing:.06em;text-transform:uppercase">
                Results <span style="font-weight:400;color:#adb5bd">(${matched.length})</span>
            </div>` +
        matched.map((m,i) => `
        <a href="${m.url}" class="app-sr-item" data-idx="${i}" style="display:flex;align-items:center;gap:10px;padding:9px 14px;text-decoration:none;color:#212529;transition:background .1s"
           onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''">
            <span style="width:34px;height:34px;border-radius:8px;background:${m.color[0]};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ${m.icon}" style="font-size:16px;color:${m.color[1]}"></i>
            </span>
            <span style="flex:1;min-width:0">
                <span style="font-size:13px;font-weight:600;display:block">${hl(m.label)}</span>
                <span style="font-size:11px;color:#adb5bd">${m.url}</span>
            </span>
            <i class="ti ti-arrow-right" style="color:#ced4da;font-size:14px"></i>
        </a>`).join('') +
        '<div style="height:6px"></div>';
}

input.addEventListener('focus', show);
input.addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    focusIdx = -1;
    if(!q){
        defBlock.style.display=''; results.style.display='none'; results.innerHTML=''; emptyEl.style.display='none';
        return;
    }
    defBlock.style.display='none';
    const matched = MENU.filter(m => m.label.toLowerCase().includes(q) || m.tags.some(t => t.includes(q)));
    if(!matched.length){
        results.style.display='none'; results.innerHTML=''; emptyEl.style.display='block';
    } else {
        emptyEl.style.display='none';
        results.style.display='block';
        results.innerHTML = renderResults(matched, q);
    }
});

input.addEventListener('keydown', function(e){
    const items = results.querySelectorAll('.app-sr-item');
    if(e.key==='ArrowDown'){ e.preventDefault(); focusIdx=Math.min(focusIdx+1,items.length-1); items[focusIdx]?.focus(); }
    if(e.key==='ArrowUp'){ e.preventDefault(); focusIdx=Math.max(focusIdx-1,-1); if(focusIdx===-1) input.focus(); else items[focusIdx]?.focus(); }
    if(e.key==='Escape') hide();
});

document.addEventListener('click', function(e){
    if(!document.getElementById('appSearchWrap').contains(e.target) && !dropdown.contains(e.target)) hide();
});
document.addEventListener('keydown', function(e){
    if((e.ctrlKey||e.metaKey) && e.key==='k'){ e.preventDefault(); input.focus(); show(); }
});
})();
</script>


            <!-- Select Store -->
            <!-- <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store"
                    data-bs-toggle="dropdown">
                    <span class="user-info">
                        <span class="user-letter">
                            <img src="{{URL::asset('build/img/store/store-01.png')}}" alt="Store Logo" class="img-fluid">
                        </span>
                        <span class="user-detail">
                            <span class="user-name">Freshmart</span>
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{URL::asset('build/img/store/store-01.png')}}" alt="Store Logo" class="img-fluid">Freshmart
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{URL::asset('build/img/store/store-02.png')}}" alt="Store Logo" class="img-fluid">Grocery Apex
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{URL::asset('build/img/store/store-03.png')}}" alt="Store Logo" class="img-fluid">Grocery Bevy
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{URL::asset('build/img/store/store-04.png')}}" alt="Store Logo" class="img-fluid">Grocery Eden
                    </a>
                </div>
            </li> -->
            <!-- /Select Store -->

            <!-- <li class="nav-item dropdown link-nav">
                <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    <i class="ti ti-circle-plus me-1"></i>Add New
                </a>
                <div class="dropdown-menu dropdown-xl dropdown-menu-center">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <a href="{{url('category-list')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-brand-codepen"></i>
                                </span>
                                <p>Category</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('add-product')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-square-plus"></i>
                                </span>
                                <p>Product</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('category-list')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-shopping-bag"></i>
                                </span>
                                <p>Purchase</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('online-orders')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-shopping-cart"></i>
                                </span>
                                <p>Sale</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('expense-list')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-file-text"></i>
                                </span>
                                <p>Expense</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('quotation-list')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-device-floppy"></i>
                                </span>
                                <p>Quotation</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('sales-returns')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-copy"></i>
                                </span>
                                <p>Return</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('users')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-user"></i>
                                </span>
                                <p>User</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('customers')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-users"></i>
                                </span>
                                <p>Customer</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('sales-report')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-shield"></i>
                                </span>
                                <p>Biller</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('suppliers')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-user-check"></i>
                                </span>
                                <p>Supplier</p>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('stock-transfer')}}" class="link-item">
                                <span class="link-icon">
                                    <i class="ti ti-truck"></i>
                                </span>
                                <p>Transfer</p>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            
            <li class="nav-item pos-nav">
                <a href="{{url('pos')}}" class="btn btn-dark btn-md d-inline-flex align-items-center">
                    <i class="ti ti-device-laptop me-1"></i>POS
                </a>
            </li> -->

            <!-- Home Link -->
            <li class="nav-item nav-item-box me-2">
                <a href="{{ route('landing') }}" title="{{ __('Home') }}" class="d-flex align-items-center gap-1 px-1" style="font-size:12px;font-weight:600;color:inherit;text-decoration:none;">
                    <i class="ti ti-home" style="font-size:18px;"></i>
                    <span class="d-none d-lg-inline">{{ __('Home') }}</span>
                </a>
            </li>
            <!-- /Home Link -->

            <!-- Language Switcher -->
            <li class="nav-item nav-item-box dropdown">
                @php $hl = app()->getLocale(); @endphp
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 px-2" href="#"
                   data-bs-toggle="dropdown" title="{{ __('Language') }}" style="font-size:12px;font-weight:600">
                    <img src="{{ URL::asset('build/img/flags/'.($hl==='bn'?'bd':'us').'.png') }}"
                         alt="" height="14" style="border-radius:2px">
                    <span class="d-none d-lg-inline">{{ $hl==='bn'?'বাং':'EN' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1" style="min-width:120px;border-radius:8px;font-size:13px">
                    <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $hl==='en'?'active':'' }}"
                           href="{{ route('lang.switch','en') }}">
                        <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="" height="13" style="border-radius:2px"> English
                        @if($hl==='en')<i class="ti ti-check ms-auto text-success" style="font-size:12px"></i>@endif
                    </a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $hl==='bn'?'active':'' }}"
                           href="{{ route('lang.switch','bn') }}">
                        <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="" height="13" style="border-radius:2px"> বাংলা
                        @if($hl==='bn')<i class="ti ti-check ms-auto text-success" style="font-size:12px"></i>@endif
                    </a></li>
                </ul>
            </li>
            <!-- /Language Switcher -->

            <li class="nav-item nav-item-box">
                <a href="javascript:void(0);" id="btnFullscreen">
                    <i class="ti ti-maximize"></i>
                </a>
            </li>
            <!-- <li class="nav-item nav-item-box">
                <a href="{{url('email')}}">
                    <i class="ti ti-mail"></i>
                    <span class="badge rounded-pill">1</span>
                </a>
            </li> -->
            <!-- Notices Bell -->
            @auth
            @if(session('active_mess_id'))
            <li class="nav-item dropdown nav-item-box">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                    <i class="ti ti-speakerphone"></i>
                    <span class="badge rounded-pill bg-danger" id="notice-bell-badge" style="display:none;">0</span>
                </a>
                <div class="dropdown-menu notifications" style="min-width:380px;">
                    <div class="topnav-dropdown-header d-flex justify-content-between align-items-center">
                        <h5 class="notification-title mb-0">{{ __('Notices') }}</h5>
                        <a href="javascript:void(0)" id="notice-mark-all-btn" class="clear-noti">{{ __('Mark all as read') }}</a>
                    </div>
                    <div class="noti-content">
                        <ul class="notification-list" id="notice-bell-list">
                            <li class="text-center p-3 text-muted small">{{ __('Loading...') }}</li>
                        </ul>
                    </div>
                    <div class="topnav-dropdown-footer">
                        <a href="{{ route('mess.notices.index', session('active_mess_id')) }}" class="btn btn-primary btn-md w-100">{{ __('View All Notices') }}</a>
                    </div>
                </div>
            </li>
            @endif
            @endauth
            <!-- /Notices Bell -->

            <li class="nav-item nav-item-box">
                @auth
                @if(session('active_mess_id'))
                <a href="{{ route('mess.settings', session('active_mess_id')) }}" title="{{ __('Mess Settings') }}">
                    <i class="ti ti-settings"></i>
                </a>
                @elseif(Auth::user()->is_super_admin)
                <a href="{{ route('admin.settings') }}" title="{{ __('System Settings') }}">
                    <i class="ti ti-settings"></i>
                </a>
                @else
                <a href="{{ route('mess.index') }}" title="{{ __('My Messes') }}">
                    <i class="ti ti-settings"></i>
                </a>
                @endif
                @else
                <a href="{{ url('/') }}"><i class="ti ti-settings"></i></a>
                @endauth
            </li>
            <li class="nav-item dropdown has-arrow main-drop profile-nav">
                <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info p-0">
                        <span class="user-letter">
                            @auth
                            <img src="{{ Auth::user()->avatar_url }}" alt="Img" class="img-fluid rounded-circle" style="width:34px;height:34px;object-fit:cover">
                            @else
                            <img src="{{URL::asset('build/img/profiles/avator1.jpg')}}" alt="Img" class="img-fluid">
                            @endauth
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profileset d-flex align-items-center">
                        <span class="user-img me-2">
                            @auth
                            <img src="{{ Auth::user()->avatar_url }}" alt="Img" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                            @else
                            <img src="{{URL::asset('build/img/profiles/avator1.jpg')}}" alt="Img">
                            @endauth
                        </span>
                        <div>
                            @auth
                            <h6 class="fw-medium">{{ Auth::user()->name }}</h6>
                            <p>{{ Auth::user()->email }}</p>
                            @else
                            <h6 class="fw-medium">{{ __('Guest') }}</h6>
                            @endauth
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile') }}"><i class="ti ti-user-circle me-2"></i>{{ __('My Profile') }}</a>
                    <hr class="my-2">
                    <a class="dropdown-item logout pb-0" href="{{ route('signout') }}"><i class="ti ti-logout me-2"></i>{{ __('Logout') }}</a>
                </div>
            </li>
        </ul>
        <!-- /Header Menu -->

        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('profile') }}">{{ __('My Profile') }}</a>
                <a class="dropdown-item" href="{{ route('signout') }}">{{ __('Logout') }}</a>
            </div>
        </div>
        <!-- /Mobile Menu -->
    </div>
</div>
@endif

    	
    @if(Route::is(['pos','pos-2','pos-3','pos-4','pos-5']))
  
<!-- Header -->
<div class="header pos-header">
			
    <!-- Logo -->
     <div class="header-left active">
        <a href="{{url('index')}}" class="logo logo-normal">
            <img src="{{URL::asset('build/img/logo.svg')}}"  alt="Img">
        </a>
        <a href="{{url('index')}}" class="logo logo-white">
            <img src="{{URL::asset('build/img/logo-white.svg')}}"  alt="Img">
        </a>
        <a href="{{url('index')}}" class="logo-small">
            <img src="{{URL::asset('build/img/logo-small.png')}}"  alt="Img">
        </a>
    </div>
    <!-- /Logo -->
    
    <a id="mobile_btn" class="mobile_btn d-none" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
    
    <!-- Header Menu -->
    <ul class="nav user-menu">

        <!-- Search -->
        <li class="nav-item time-nav">
            <span class="bg-teal text-white d-inline-flex align-items-center"><img src="{{URL::asset('build/img/icons/clock-icon.svg')}}" alt="img" class="me-2">09:25:32</span>
        </li>
        <!-- /Search -->
        
        <li class="nav-item pos-nav">
            <a href="{{url('index')}}" class="btn btn-purple btn-md d-inline-flex align-items-center">
                <i class="ti ti-world me-1"></i>Dashboard
            </a>
        </li>

        <!-- Select Store -->
        <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store"
                data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-letter">
                        <img src="{{URL::asset('build/img/store/store-01.png')}}" alt="Store Logo" class="img-fluid">
                    </span>
                    <span class="user-detail">
                        <span class="user-name">Freshmart</span>
                    </span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{URL::asset('build/img/store/store-01.png')}}" alt="Store Logo" class="img-fluid">Freshmart
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{URL::asset('build/img/store/store-02.png')}}" alt="Store Logo" class="img-fluid">Grocery Apex
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{URL::asset('build/img/store/store-03.png')}}" alt="Store Logo" class="img-fluid">Grocery Bevy
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <img src="{{URL::asset('build/img/store/store-04.png')}}" alt="Store Logo" class="img-fluid">Grocery Eden
                </a>
            </div>
        </li>
        <!-- /Select Store -->
        
        <li class="nav-item nav-item-box">
            <a href="#" data-bs-toggle="modal" data-bs-target="#calculator" class="bg-orange border-orange text-white"><i class="ti ti-calculator"></i></a>
        </li>
        <li class="nav-item nav-item-box">
            <a href="javascript:void(0);" id="btnFullscreen" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Maximize" >
                <i class="ti ti-maximize"></i>
            </a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Cash Register">
            <a href="#" data-bs-toggle="modal" data-bs-target="#cash-register"><i class="ti ti-cash"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Print Last Reciept">
            <a href="#"><i class="ti ti-printer"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Today’s Sale">
            <a href="#" data-bs-toggle="modal" data-bs-target="#today-sale"><i class="ti ti-progress"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Today’s Profit">
            <a href="#" data-bs-toggle="modal" data-bs-target="#today-profit"><i class="ti ti-chart-infographic"></i></a>
        </li>
        <li class="nav-item nav-item-box" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="POS Settings">
            <a href="{{url('pos-settings')}}"><i class="ti ti-settings"></i></a>
        </li>
        <li class="nav-item dropdown has-arrow main-drop profile-nav">
            <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                <span class="user-info p-0">
                    <span class="user-letter">
                        <img src="{{URL::asset('build/img/profiles/avator1.jpg')}}" alt="Img" class="img-fluid">
                    </span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profilename">
                    <div class="profileset">
                        @php
                            $_u    = Auth::user();
                            $_mem  = $_u ? $_u->messMembers()->where('is_active', true)->orderByDesc('id')->first() : null;
                            $_role = $_u?->is_super_admin ? 'Super Admin'
                                   : ($_mem ? ucfirst($_mem->role) : 'Member');
                        @endphp
                        <span class="user-img">
                            @if($_u?->avatar)
                                <img src="{{ asset('storage/'.$_u->avatar) }}" alt="{{ $_u->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                            @else
                                <img src="{{URL::asset('build/img/profiles/avator1.jpg')}}" alt="Img">
                            @endif
                            <span class="status online"></span>
                        </span>
                        <div class="profilesets">
                            <h6>{{ $_u?->name ?? 'User' }}</h6>
                            <h5>{{ $_role }}</h5>
                        </div>
                    </div>
                    <hr class="m-0">
                    <a class="dropdown-item" href="{{url('profile')}}"><i class="me-2" data-feather="user"></i>My
                        Profile</a>
                    <a class="dropdown-item" href="{{url('general-settings')}}"><i class="me-2" data-feather="settings"></i>Settings</a>
                    <hr class="m-0">
                    <a class="dropdown-item logout pb-0" href="{{url('signin')}}"><img src="{{URL::asset('build/img/icons/log-out.svg')}}" class="me-2" alt="img">Logout</a>
                </div>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->
    
    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{url('profile')}}">My Profile</a>
            <a class="dropdown-item" href="{{url('general-settings')}}">Settings</a>
            <a class="dropdown-item" href="{{url('signin')}}">Logout</a>
        </div>
    </div>
    <!-- /Mobile Menu -->
</div>
<!-- Header -->
    @endif