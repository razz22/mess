<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="@yield('meta-description', 'MessManager — Smart Mess Management System')">
<title>@yield('title', 'MessManager') — MessManager</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('/build/img/favicon.png') }}">
<link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/dist/tabler-icons.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<style>
@font-face {
    font-family: "tabler-icons";
    font-style: normal;
    font-weight: 400;
    src: url("{{ URL::asset('build/plugins/tabler-icons/fonts/tabler-icons.woff2') }}") format("woff2"),
         url("{{ URL::asset('build/plugins/tabler-icons/fonts/tabler-icons.woff') }}") format("woff");
}
:root {
  --orange:      #FE9F43;
  --orange-dark: #e8892e;
  --orange-glow: rgba(254,159,67,.25);
  --navy:        #141432;
  --navy2:       #1b1b4b;
  --purple:      #353570;
  --green:       #3EB780;
  --text:        #212B36;
  --muted:       #6b7280;
  --border:      #E6EAED;
  --white:       #ffffff;
  --light:       #f9fafb;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); background: var(--white); overflow-x: hidden; }
a { text-decoration: none; }
img { max-width: 100%; }

.section-label { font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--orange); }
.section-title { font-size: clamp(1.8rem, 3.5vw, 2.6rem); font-weight: 800; color: var(--navy); line-height: 1.2; }
.section-sub   { font-size: 1rem; color: var(--muted); line-height: 1.7; max-width: 560px; margin: 0 auto; }

.btn-orange {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 13px 28px; border-radius: 10px;
  background: var(--orange); color: var(--white);
  font-weight: 700; font-size: 15px; border: none;
  box-shadow: 0 6px 20px var(--orange-glow);
  transition: all .25s; cursor: pointer;
}
.btn-orange:hover { background: var(--orange-dark); color: var(--white); transform: translateY(-2px); }
.btn-outline-orange {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 12px 26px; border-radius: 10px;
  background: transparent; color: var(--orange);
  font-weight: 700; font-size: 15px;
  border: 2px solid var(--orange);
  transition: all .25s;
}
.btn-outline-orange:hover { background: var(--orange); color: var(--white); }

/* ── NAVBAR ── */
.lnav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: rgba(20,20,50,.92);
  backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,.06);
  transition: all .3s;
}
.lnav.scrolled { background: rgba(20,20,50,.98); box-shadow: 0 4px 24px rgba(0,0,0,.35); }
.lnav-inner { display: flex; align-items: center; height: 68px; }
.lnav-logo { display: flex; align-items: center; gap: 10px; color: var(--white); font-size: 1.2rem; font-weight: 800; letter-spacing: -.3px; flex-shrink: 0; }
.lnav-logo .logo-box { width: 38px; height: 38px; border-radius: 10px; background: var(--orange); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--white); box-shadow: 0 4px 14px var(--orange-glow); flex-shrink: 0; }
.lnav-logo em { color: var(--orange); font-style: normal; }
.lnav-links { display: flex; align-items: center; gap: 2px; margin: 0 auto; list-style: none; }
.lnav-links a { padding: 7px 15px; border-radius: 8px; font-size: .875rem; font-weight: 500; color: rgba(255,255,255,.75); transition: all .2s; }
.lnav-links a:hover, .lnav-links a.active { color: var(--orange); background: rgba(254,159,67,.1); }
.lnav-auth { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.btn-nav-in { padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 600; color: rgba(255,255,255,.85); border: 1.5px solid rgba(255,255,255,.2); background: transparent; transition: all .2s; }
.btn-nav-in:hover { color: var(--orange); border-color: var(--orange); }
.btn-nav-up { padding: 8px 20px; border-radius: 8px; font-size: .875rem; font-weight: 700; background: var(--orange); color: var(--white); border: none; box-shadow: 0 3px 12px var(--orange-glow); transition: all .25s; }
.btn-nav-up:hover { background: var(--orange-dark); color: var(--white); }
.nav-user-pill { display: flex; align-items: center; gap: 9px; padding: 5px 14px 5px 5px; border-radius: 50px; border: 1.5px solid rgba(255,255,255,.15); background: rgba(255,255,255,.06); color: var(--white); font-size: .85rem; font-weight: 600; transition: all .2s; }
.nav-avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--orange); color: var(--white); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; }
.btn-nav-dash { padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 700; background: var(--orange); color: var(--white); border: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 3px 12px var(--orange-glow); transition: all .25s; }
.btn-nav-dash:hover { background: var(--orange-dark); color: var(--white); }
.lnav-toggle { display: none; border: none; background: none; width: 38px; height: 38px; border-radius: 8px; align-items: center; justify-content: center; cursor: pointer; color: rgba(255,255,255,.8); font-size: 1.3rem; }
.lnav-mobile { display: none !important; background: rgba(20,20,50,.98); border-top: 1px solid rgba(255,255,255,.06); padding: 10px 0; }
.lnav-mobile.open { display: block !important; }
.lnav-mobile a { display: flex; align-items: center; gap: 10px; padding: 11px 20px; font-size: .9rem; color: rgba(255,255,255,.75); font-weight: 500; transition: all .2s; }
.lnav-mobile a:hover { color: var(--orange); background: rgba(254,159,67,.08); }
.lnav-mobile .mob-divider { height: 1px; background: rgba(255,255,255,.07); margin: 6px 0; }
@media(max-width:991px) {
  .lnav-links, .lnav-auth-desktop { display: none !important; }
  .lnav-toggle { display: flex !important; }
}

/* ── Public Hero ── */
.pub-hero {
  padding: 120px 0 72px;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy2) 60%, #0f0f35 100%);
  position: relative; overflow: hidden;
}
.pub-hero::before {
  content: ''; position: absolute; top: -100px; right: -100px;
  width: 500px; height: 500px; border-radius: 50%;
  background: radial-gradient(circle, rgba(254,159,67,.15) 0%, transparent 70%);
}
.pub-hero::after {
  content: ''; position: absolute; bottom: -80px; left: -80px;
  width: 400px; height: 400px; border-radius: 50%;
  background: radial-gradient(circle, rgba(53,53,112,.4) 0%, transparent 70%);
}
.pub-hero-inner { position: relative; z-index: 2; }
.pub-hero h1 { font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; color: var(--white); line-height: 1.15; }
.pub-hero p { font-size: 1.05rem; color: rgba(255,255,255,.65); max-width: 600px; line-height: 1.7; margin-top: 12px; }
.pub-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: rgba(255,255,255,.4); margin-bottom: 16px; }
.pub-breadcrumb a { color: rgba(255,255,255,.55); transition: color .2s; }
.pub-breadcrumb a:hover { color: var(--orange); }
.pub-breadcrumb i { font-size: 10px; }

/* ── FOOTER ── */
.lfoot { background: #0c0c28; padding: 60px 0 28px; color: rgba(255,255,255,.5); }
.lfoot-logo { font-size: 1.3rem; font-weight: 800; color: var(--white); display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.lfoot-logo .logo-box { width: 34px; height: 34px; border-radius: 8px; background: var(--orange); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 1rem; }
.lfoot-heading { font-size: .85rem; font-weight: 700; color: var(--white); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
.lfoot a { color: rgba(255,255,255,.45); font-size: .875rem; display: block; margin-bottom: 10px; transition: color .2s; }
.lfoot a:hover { color: var(--orange); }
.lfoot-divider { border-color: rgba(255,255,255,.06); margin: 32px 0 20px; }
.lfoot-bottom { font-size: .8rem; color: rgba(255,255,255,.25); }

/* Back to top */
.pub-content { padding-top: 68px; }
</style>
@yield('extra-css')
</head>
<body>

<nav class="lnav" id="lnav">
  <div class="container">
    <div class="lnav-inner">
      <a href="{{ url('/') }}" class="lnav-logo me-5">
        <img src="{{ URL::asset('build/img/logo.svg') }}" alt="Mess Manager" style="height: 44px; max-width: 200px;">
      </a>

      <ul class="lnav-links d-none d-lg-flex">
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">{{ __('Home') }}</a></li>
        <li><a href="{{ route('public.features') }}" class="{{ request()->routeIs('public.features') ? 'active' : '' }}">{{ __('Features') }}</a></li>
        <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('Blog') }}</a></li>
        <li><a href="{{ route('public.about') }}" class="{{ request()->routeIs('public.about') ? 'active' : '' }}">{{ __('About') }}</a></li>
        <li><a href="{{ route('public.contact') }}" class="{{ request()->routeIs('public.contact') ? 'active' : '' }}">{{ __('Contact') }}</a></li>
        <li><a href="{{ route('public.faq') }}" class="{{ request()->routeIs('public.faq') ? 'active' : '' }}">{{ __('FAQ') }}</a></li>
      </ul>

      <div class="lnav-auth lnav-auth-desktop ms-auto d-none d-lg-flex align-items-center gap-2">
        {{-- Language switcher --}}
        @php $llocale = app()->getLocale(); @endphp
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-1"
                  type="button" data-bs-toggle="dropdown"
                  style="border-radius:20px;font-size:13px;font-weight:600;color:rgba(255,255,255,.85);background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2)">
            <img src="{{ URL::asset('build/img/flags/'.($llocale==='bn'?'bd':'us').'.png') }}" alt="" height="14" style="border-radius:2px">
            {{ $llocale==='bn' ? 'বাংলা' : 'EN' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow py-1" style="border-radius:10px;min-width:130px">
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $llocale==='en'?'active':'' }}" href="{{ route('lang.switch','en') }}">
              <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="" height="13" style="border-radius:2px"> English
              @if($llocale==='en') <i class="ti ti-check ms-auto text-success"></i> @endif
            </a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $llocale==='bn'?'active':'' }}" href="{{ route('lang.switch','bn') }}">
              <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="" height="13" style="border-radius:2px"> বাংলা
              @if($llocale==='bn') <i class="ti ti-check ms-auto text-success"></i> @endif
            </a></li>
          </ul>
        </div>
        @auth
          <a href="{{ url('/') }}" class="nav-user-pill">
            <div class="nav-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span style="max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</span>
          </a>
          <a href="{{ route('mess.index') }}" class="btn-nav-dash">
            <i class="ti ti-layout-dashboard"></i> {{ __('Dashboard') }}
          </a>
        @else
          <a href="{{ route('signin') }}" class="btn-nav-in">{{ __('Sign In') }}</a>
          <a href="{{ route('register') }}" class="btn-nav-up">
            <i class="ti ti-user-plus me-1"></i>{{ __('Sign Up') }}
          </a>
        @endauth
      </div>

      <button class="lnav-toggle ms-auto" id="navToggle"><i class="ti ti-menu-2" id="navToggleIcon"></i></button>
    </div>
  </div>

  <div class="lnav-mobile" id="navMobile">
    <div class="container">
      <a href="{{ url('/') }}"><i class="ti ti-home me-2"></i>{{ __('Home') }}</a>
      <a href="{{ route('public.features') }}"><i class="ti ti-layout-grid me-2"></i>{{ __('Features') }}</a>
      <a href="{{ route('blog.index') }}"><i class="ti ti-article me-2"></i>{{ __('Blog') }}</a>
      <a href="{{ route('public.about') }}"><i class="ti ti-info-circle me-2"></i>{{ __('About') }}</a>
      <a href="{{ route('public.contact') }}"><i class="ti ti-mail me-2"></i>{{ __('Contact') }}</a>
      <a href="{{ route('public.faq') }}"><i class="ti ti-help me-2"></i>{{ __('FAQ') }}</a>
      <div class="mob-divider"></div>
      <a href="{{ route('lang.switch','en') }}" style="{{ app()->getLocale()==='en'?'color:var(--orange);font-weight:700':'' }}">
        <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="" height="13" style="border-radius:2px;margin-right:6px"> English
      </a>
      <a href="{{ route('lang.switch','bn') }}" style="{{ app()->getLocale()==='bn'?'color:var(--orange);font-weight:700':'' }}">
        <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="" height="13" style="border-radius:2px;margin-right:6px"> বাংলা
      </a>
      <div class="mob-divider"></div>
      @auth
        <a href="{{ route('mess.index') }}" style="color:var(--orange);font-weight:700"><i class="ti ti-layout-dashboard me-2"></i>{{ __('Dashboard') }}</a>
        <a href="{{ route('signout') }}"><i class="ti ti-logout me-2"></i>{{ __('Logout') }}</a>
      @else
        <a href="{{ route('signin') }}"><i class="ti ti-login me-2"></i>{{ __('Sign In') }}</a>
        <a href="{{ route('register') }}" style="color:var(--orange);font-weight:700"><i class="ti ti-user-plus me-2"></i>{{ __('Sign Up') }}</a>
      @endauth
    </div>
  </div>
</nav>

<div class="pub-content">
  @yield('public-content')
</div>

<footer class="lfoot">
  <div class="container">
    <div class="row g-4 g-lg-5">
      <div class="col-12 col-lg-4">
        <div class="lfoot-logo">
          <img src="{{ URL::asset('build/img/logo.svg') }}" alt="Mess Manager" style="height: 40px; max-width: 190px;">

        </div>
        <p style="font-size:.875rem;line-height:1.7;max-width:300px">
          {{ __('Smart mess management for shared living. Track meals, expenses, and more — all in one beautiful platform.') }}
        </p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:1rem;transition:all .2s">
            <i class="ti ti-brand-facebook"></i></a>
          <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:1rem;transition:all .2s">
            <i class="ti ti-brand-twitter"></i></a>
          <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:1rem;transition:all .2s">
            <i class="ti ti-brand-linkedin"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <div class="lfoot-heading">{{ __('Product') }}</div>
        <a href="{{ url('/') }}">{{ __('Home') }}</a>
        <a href="{{ route('public.features') }}">{{ __('Features') }}</a>
        <a href="{{ url('/#pricing') }}">{{ __('Pricing') }}</a>
        <a href="{{ route('blog.index') }}">{{ __('Blog') }}</a>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <div class="lfoot-heading">{{ __('Company') }}</div>
        <a href="{{ route('public.about') }}">{{ __('About Us') }}</a>
        <a href="{{ route('public.contact') }}">{{ __('Contact') }}</a>
        <a href="{{ route('public.faq') }}">{{ __('FAQ') }}</a>
        <a href="{{ route('public.privacy') }}">{{ __('Privacy Policy') }}</a>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="lfoot-heading">{{ __('Account') }}</div>
        <a href="{{ route('signin') }}">{{ __('Sign In') }}</a>
        <a href="{{ route('register') }}">{{ __('Register Free') }}</a>
        @auth
          <a href="{{ route('mess.index') }}">{{ __('Dashboard') }}</a>
        @endauth
        <div class="mt-3" style="background:rgba(254,159,67,.1);border:1px solid rgba(254,159,67,.2);border-radius:10px;padding:12px 16px">
          <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:4px">{{ __('Start managing your mess today') }}</div>
          <a href="{{ route('register') }}" style="color:var(--orange);font-size:.875rem;font-weight:700">
            {{ __('Register Free') }} <i class="ti ti-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
    <hr class="lfoot-divider">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 lfoot-bottom">
      <span>© {{ date('Y') }} MessManager. {{ __('All rights reserved.') }}</span>
      <div class="d-flex gap-3">
        <a href="{{ route('public.privacy') }}" style="color:rgba(255,255,255,.3);font-size:.8rem">{{ __('Privacy Policy') }}</a>
        <a href="{{ route('public.contact') }}" style="color:rgba(255,255,255,.3);font-size:.8rem">{{ __('Contact') }}</a>
      </div>
    </div>
  </div>
</footer>

<script src="{{ URL::asset('build/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ URL::asset('build/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 700, once: true, offset: 60 });

const lnav = document.getElementById('lnav');
window.addEventListener('scroll', () => {
  lnav.classList.toggle('scrolled', window.scrollY > 40);
});

const navToggle     = document.getElementById('navToggle');
const navMobile     = document.getElementById('navMobile');
const navToggleIcon = document.getElementById('navToggleIcon');
navToggle.addEventListener('click', () => {
  const open = navMobile.classList.toggle('open');
  navToggleIcon.className = open ? 'ti ti-x' : 'ti ti-menu-2';
});
</script>
@yield('extra-js')
</body>
</html>
