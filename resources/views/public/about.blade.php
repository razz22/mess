@extends('public.layout')
@section('title', __('About Us'))
@section('meta-description', __('Learn about MessManager — our mission, team, and the story behind the smart mess management platform.'))

@section('public-content')

{{-- Hero --}}
<section class="pub-hero">
  <div class="container pub-hero-inner">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right"></i>
      <span>{{ __('About Us') }}</span>
    </div>
    <h1 data-aos="fade-up">{{ __('About') }} <span style="color:var(--orange)">MessManager</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ __("We're on a mission to eliminate the chaos of shared living. No more disputes over meal counts, missing market receipts, or unclear monthly dues.") }}</p>
  </div>
</section>

{{-- Mission & Vision --}}
<section style="padding:80px 0; background:var(--white)">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="section-label mb-2">{{ __('Our Mission') }}</div>
        <h2 class="section-title mb-4">{{ __('Simplifying Shared Living, One Mess at a Time') }}</h2>
        <p style="color:var(--muted);line-height:1.8">
          {{ __('MessManager was born from a real problem — managing a shared mess is hard. Tracking who ate what, splitting grocery costs, handling monthly deposits, generating fair reports... all done manually on paper or WhatsApp groups.') }}
        </p>
        <p style="color:var(--muted);line-height:1.8;margin-top:12px">
          {{ __('We built a complete digital platform so that mess owners, managers, and members can focus on what matters — living together harmoniously — while the app handles all the numbers automatically.') }}
        </p>
        <div class="d-flex gap-3 mt-4 flex-wrap">
          <div style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--navy)">
            <i class="ti ti-check-circle" style="color:var(--orange);font-size:18px"></i> {{ __('Fully automated calculations') }}
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--navy)">
            <i class="ti ti-check-circle" style="color:var(--orange);font-size:18px"></i> {{ __('Real-time transparency') }}
          </div>
          <div style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--navy)">
            <i class="ti ti-check-circle" style="color:var(--orange);font-size:18px"></i> {{ __('Fair for every member') }}
          </div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          @foreach([
            ['icon'=>'ti-building-community','title'=>__('Our Mission'),'desc'=>__('Provide every shared mess with a digital backbone — fair, transparent, and effortless.'),'color'=>'var(--orange)'],
            ['icon'=>'ti-eye','title'=>__('Our Vision'),'desc'=>__('A world where no roommate ever argues over mess bills or disputed meal counts.'),'color'=>'var(--green)'],
            ['icon'=>'ti-heart','title'=>__('Our Values'),'desc'=>__('Transparency, fairness, simplicity. We build for real people with real problems.'),'color'=>'#60a5fa'],
          ] as $item)
          <div class="col-12">
            <div style="background:var(--light);border:1px solid var(--border);border-radius:16px;padding:24px;display:flex;align-items:flex-start;gap:16px;transition:all .3s" onmouseover="this.style.borderColor='var(--orange)';this.style.boxShadow='0 8px 24px rgba(254,159,67,.1)'" onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
              <div style="width:48px;height:48px;border-radius:12px;background:{{ $item['color'] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti {{ $item['icon'] }}" style="color:{{ $item['color'] }};font-size:1.4rem"></i>
              </div>
              <div>
                <div style="font-weight:700;font-size:.95rem;color:var(--navy);margin-bottom:4px">{{ $item['title'] }}</div>
                <div style="font-size:.875rem;color:var(--muted);line-height:1.65">{{ $item['desc'] }}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Stats --}}
<section style="background:var(--navy);padding:64px 0">
  <div class="container">
    <div class="row g-4 text-center">
      @foreach([
        ['num'=>'500+','label'=>__('Active Messes'),'icon'=>'ti-building-community'],
        ['num'=>'10,000+','label'=>__('Members Managed'),'icon'=>'ti-users'],
        ['num'=>'৳2M+','label'=>__('Expenses Tracked'),'icon'=>'ti-coin'],
        ['num'=>'15+','label'=>__('Countries'),'icon'=>'ti-world'],
      ] as $stat)
      <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
        <div style="padding:24px">
          <i class="ti {{ $stat['icon'] }}" style="font-size:2rem;color:rgba(254,159,67,.3);display:block;margin-bottom:8px"></i>
          <div style="font-size:2.2rem;font-weight:800;color:var(--orange)">{{ $stat['num'] }}</div>
          <div style="font-size:.875rem;color:rgba(255,255,255,.5);margin-top:4px">{{ $stat['label'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Team --}}
<section style="padding:80px 0;background:var(--light)">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-label mb-2">{{ __('The Team') }}</div>
      <h2 class="section-title">{{ __('People Behind MessManager') }}</h2>
      <p class="section-sub mt-3">{{ __('A small, passionate team dedicated to making shared living easier for everyone.') }}</p>
    </div>
    <div class="row g-4 justify-content-center">
      @foreach([
        ['name'=>'Faysal Ibrahim','role'=>__('Founder & CEO'),'init'=>'FI','color'=>'var(--orange)'],
        ['name'=>'Fariha Rahman','role'=>__('Head of Product'),'init'=>'FR','color'=>'var(--green)'],
        ['name'=>'Readul Ahmed','role'=>__('Lead Developer'),'init'=>'RA','color'=>'#60a5fa'],
        ['name'=>'Nusrat Jahan','role'=>__('UI/UX Designer'),'init'=>'NJ','color'=>'#a78bfa'],
      ] as $member)
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
        <div style="background:var(--white);border:1px solid var(--border);border-radius:20px;padding:28px 20px;text-align:center;transition:all .3s" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,.1)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
          <div style="width:72px;height:72px;border-radius:50%;background:{{ $member['color'] }};color:var(--white);font-size:1.5rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">{{ $member['init'] }}</div>
          <div style="font-weight:700;color:var(--navy);font-size:.95rem">{{ $member['name'] }}</div>
          <div style="font-size:.8rem;color:var(--muted);margin-top:4px">{{ $member['role'] }}</div>
          <div class="d-flex justify-content-center gap-2 mt-3">
            <a href="#" style="color:var(--muted);transition:color .2s" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--muted)'"><i class="ti ti-brand-linkedin"></i></a>
            <a href="#" style="color:var(--muted);transition:color .2s" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--muted)'"><i class="ti ti-brand-twitter"></i></a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Timeline --}}
<section style="padding:80px 0;background:var(--white)">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-label mb-2">{{ __('Our Journey') }}</div>
      <h2 class="section-title">{{ __('How We Got Here') }}</h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        @foreach([
          ['year'=>'2022','title'=>__('The Problem Identified'),'desc'=>__('Our founder struggled managing a 12-person mess manually. Disputes over meal counts and expenses led to the idea.'),'color'=>'var(--orange)'],
          ['year'=>'2023','title'=>__('First Version Launched'),'desc'=>__('MessManager v1 launched with basic meal tracking and expense splitting for a small group of beta users.'),'color'=>'var(--green)'],
          ['year'=>'2024','title'=>__('Major Feature Expansion'),'desc'=>__('Added market routines, monthly reports, manager rotation, rewards system, and house rent management.'),'color'=>'#60a5fa'],
          ['year'=>'2025','title'=>__('500+ Messes & Growing'),'desc'=>__('Crossed 500 active messes and 10,000 members. Expanded to international users and added subscription plans.'),'color'=>'#a78bfa'],
          ['year'=>'2026','title'=>__('Platform & Community'),'desc'=>__('Launched the public blog, community features, and mobile-optimized experience for all users.'),'color'=>'var(--orange)'],
        ] as $ev)
        <div class="d-flex gap-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
          <div style="flex-shrink:0;text-align:center">
            <div style="width:56px;height:56px;border-radius:50%;background:{{ $ev['color'] }};color:var(--white);font-weight:800;font-size:.8rem;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(0,0,0,.15)">{{ $ev['year'] }}</div>
            @if(!$loop->last)
            <div style="width:2px;background:var(--border);height:32px;margin:0 auto"></div>
            @endif
          </div>
          <div style="background:var(--light);border:1px solid var(--border);border-radius:14px;padding:20px;flex:1">
            <div style="font-weight:700;color:var(--navy);margin-bottom:6px">{{ $ev['title'] }}</div>
            <div style="font-size:.875rem;color:var(--muted);line-height:1.65">{{ $ev['desc'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section style="padding:80px 0;background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 100%);text-align:center">
  <div class="container" data-aos="zoom-in">
    <div class="section-label mb-3" style="color:var(--orange)">{{ __('Get Started') }}</div>
    <h2 style="font-size:clamp(1.8rem,3vw,2.4rem);font-weight:800;color:var(--white);margin-bottom:12px">{{ __('Ready to Join 500+ Messes?') }}</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:32px;font-size:1.05rem">{{ __('Start for free today. No credit card required.') }}</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('register') }}" class="btn-orange" style="font-size:16px;padding:14px 32px"><i class="ti ti-rocket"></i> {{ __('Create Free Account') }}</a>
      <a href="{{ route('public.contact') }}" class="btn-outline-orange" style="font-size:16px;padding:14px 32px;color:var(--white);border-color:rgba(255,255,255,.3)" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'"><i class="ti ti-mail"></i> {{ __('Contact Us') }}</a>
    </div>
  </div>
</section>

@endsection
