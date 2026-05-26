@extends('public.layout')
@section('title', __('Features'))
@section('meta-description', __('Explore all the powerful features of Thaka Khawa — meal tracking, expenses, reports, market duties, and more.'))

@section('public-content')

<section class="pub-hero">
  <div class="container pub-hero-inner">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right"></i>
      <span>{{ __('Features') }}</span>
    </div>
    <h1 data-aos="fade-up">{{ __('Everything Your Mess') }} <span style="color:var(--orange)">{{ __('Needs') }}</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ __('A complete platform covering every aspect of shared mess life — automated, accurate, and always accessible from any device.') }}</p>
  </div>
</section>

{{-- Feature Cards --}}
<section style="padding:80px 0;background:var(--white)">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-label mb-2">{{ __('What We Offer') }}</div>
      <h2 class="section-title">{{ __('12 Powerful Features') }}</h2>
      <p class="section-sub mt-3">{{ __('Everything automated. Nothing manual. Say goodbye to spreadsheets.') }}</p>
    </div>
    @php
    $features = [
      ['icon'=>'ti-tools-kitchen-2','title'=>__('Meal Management'),'desc'=>__('Track daily meal attendance for breakfast, lunch & dinner. Auto-mark members ON by default, set cut-off times, and handle meal-off requests with a single tap. All data is captured for monthly calculation.'),'color'=>'var(--orange)'],
      ['icon'=>'ti-shopping-cart','title'=>__('Market Routine'),'desc'=>__('Assign market duty using a visual calendar. Build shopping lists, track spending, and let members exchange duties. All purchases automatically feed into expense tracking.'),'color'=>'var(--green)'],
      ['icon'=>'ti-coins','title'=>__('Expense Tracking'),'desc'=>__('Log all mess expenses with categories. Get a full breakdown per member every month — electricity, gas, cook bill, groceries, and more. Every taka accounted for.'),'color'=>'#60a5fa'],
      ['icon'=>'ti-cash','title'=>__('Deposit Management'),'desc'=>__('Record monthly deposits from each member. Track who paid, who is due, carry-forward amounts, and keep a clear financial ledger that updates in real-time.'),'color'=>'#a78bfa'],
      ['icon'=>'ti-report-analytics','title'=>__('Monthly Reports'),'desc'=>__("Auto-generate monthly reports showing meal cost per head, each member's expense share, total payable, due amount, and carry-forward balance. PDF-ready."),'color'=>'var(--orange)'],
      ['icon'=>'ti-crown','title'=>__('Manager Rotation'),'desc'=>__('Assign a new manager each month from among members. Members rate the manager with star ratings. The best-rated manager gets recognized and rewarded.'),'color'=>'var(--green)'],
      ['icon'=>'ti-trophy','title'=>__('Rewards & Points'),'desc'=>__('Recognize outstanding members monthly. Award points and gifts to the best member, top market person, and highest-rated manager. Build positive mess culture.'),'color'=>'#60a5fa'],
      ['icon'=>'ti-layout-kanban','title'=>__('Meal Kanban Board'),'desc'=>__("Plan daily meals visually. Add items to cook, move them to Cooking then Done. Every member can see what's being prepared in real-time, reducing confusion."),'color'=>'#a78bfa'],
      ['icon'=>'ti-flag','title'=>__('Member Reporting'),'desc'=>__('Members report rule violations to the manager. Reporters earn points when issues are resolved. Promotes accountability and keeps the mess environment healthy.'),'color'=>'var(--orange)'],
      ['icon'=>'ti-users','title'=>__('Member Management'),'desc'=>__('Add, remove, and manage members with distinct roles — owner, manager, author, or member. Share an 8-digit invite code for instant onboarding.'),'color'=>'var(--green)'],
      ['icon'=>'ti-arrows-exchange','title'=>__('Duty Exchange'),'desc'=>__("Can't do market duty? Request an exchange with another member. They can accept or reject — fully managed in-app with notifications for both parties."),'color'=>'#60a5fa'],
      ['icon'=>'ti-home','title'=>__('House Rent Management'),'desc'=>__('Track monthly rent, advances, and payments for each room. Generate rent invoices, mark payments, and maintain a complete rent fund ledger for the property.'),'color'=>'#a78bfa'],
    ];
    @endphp
    <div class="row g-4">
      @foreach($features as $i => $f)
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
        <div style="background:var(--light);border:1px solid var(--border);border-radius:18px;padding:28px;height:100%;transition:all .3s;position:relative;overflow:hidden" onmouseover="this.style.borderColor='var(--orange)';this.style.boxShadow='0 12px 36px rgba(254,159,67,.12)';this.style.transform='translateY(-4px)';this.querySelector('.feat-icon').style.background='var(--orange)';this.querySelector('.feat-icon').style.color='var(--white)'" onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='none';this.querySelector('.feat-icon').style.background='rgba(254,159,67,.12)';this.querySelector('.feat-icon').style.color='var(--orange)'">
          <div class="feat-icon" style="width:52px;height:52px;border-radius:14px;background:rgba(254,159,67,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--orange);margin-bottom:18px;transition:all .3s">
            <i class="ti {{ $f['icon'] }}"></i>
          </div>
          <h5 style="font-size:.95rem;font-weight:700;color:var(--navy);margin-bottom:8px">{{ $f['title'] }}</h5>
          <p style="font-size:.85rem;color:var(--muted);line-height:1.7;margin:0">{{ $f['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Comparison Table --}}
<section style="padding:80px 0;background:var(--light)">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-label mb-2">{{ __('Pricing Comparison') }}</div>
      <h2 class="section-title">{{ __('Free vs Paid Plans') }}</h2>
      <p class="section-sub mt-3">{{ __('Start free and upgrade as your mess grows. No hidden fees ever.') }}</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-10" data-aos="fade-up">
        <div style="background:var(--white);border:1px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr style="background:var(--navy)">
                  <th style="padding:20px 24px;color:rgba(255,255,255,.6);font-size:.85rem;font-weight:600;border:0">{{ __('Feature') }}</th>
                  <th style="padding:20px 24px;color:var(--white);font-size:.95rem;font-weight:700;border:0;text-align:center">{{ __('Free') }}</th>
                  <th style="padding:20px 24px;color:var(--orange);font-size:.95rem;font-weight:700;border:0;text-align:center">{{ __('Paid') }} <span style="font-size:.75rem;opacity:.7">/ {{ __('month') }}</span></th>
                </tr>
              </thead>
              <tbody>
                @php
                $rows = [
                  [__('Members per mess'),__('Up to 10'),__('Up to 50+')],
                  [__('Meal tracking'),'✓','✓'],
                  [__('Expense tracking'),'✓','✓'],
                  [__('Monthly reports'),'✓','✓'],
                  [__('Market routine'),'✓','✓'],
                  [__('Member management'),'✓','✓'],
                  [__('House rent management'),'—','✓'],
                  [__('Multiple messes per user'),'1','5+'],
                  [__('Priority support'),'—','✓'],
                  [__('Custom expense categories'),'3',__('Unlimited')],
                  [__('Data export'),'—','✓'],
                  [__('Advanced analytics'),'—','✓'],
                ];
                @endphp
                @foreach($rows as $i => $row)
                <tr style="background:{{ $i % 2 === 0 ? 'var(--white)' : 'var(--light)' }}">
                  <td style="padding:14px 24px;color:var(--navy);font-weight:500;border-color:var(--border)">{{ $row[0] }}</td>
                  <td style="padding:14px 24px;text-align:center;border-color:var(--border);color:{{ $row[1]==='—' ? 'var(--muted)' : ($row[1]==='✓' ? 'var(--green)' : 'var(--navy)') }};font-weight:{{ $row[1]==='✓' ? '700' : '500' }}">{{ $row[1] }}</td>
                  <td style="padding:14px 24px;text-align:center;border-color:var(--border);color:{{ $row[2]==='—' ? 'var(--muted)' : ($row[2]==='✓' ? 'var(--green)' : 'var(--orange)') }};font-weight:700">{{ $row[2] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section style="padding:80px 0;background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 100%);text-align:center">
  <div class="container" data-aos="zoom-in">
    <div class="section-label mb-3" style="color:var(--orange)">{{ __('Get Started Today') }}</div>
    <h2 style="font-size:clamp(1.8rem,3vw,2.4rem);font-weight:800;color:var(--white);margin-bottom:12px">{{ __('Ready to Transform Your Mess?') }}</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:32px;font-size:1.05rem">{{ __('Join 500+ messes already running smarter on Thaka Khawa.') }}</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('register') }}" class="btn-orange" style="font-size:16px;padding:14px 32px"><i class="ti ti-rocket"></i> {{ __('Start Free Today') }}</a>
      <a href="{{ route('public.contact') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;border-radius:10px;border:2px solid rgba(255,255,255,.25);color:rgba(255,255,255,.8);font-weight:600;font-size:16px;transition:all .2s" onmouseover="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'" onmouseout="this.style.borderColor='rgba(255,255,255,.25)';this.style.color='rgba(255,255,255,.8)'"><i class="ti ti-mail"></i> {{ __('Talk to Sales') }}</a>
    </div>
    <div class="mt-4 d-flex justify-content-center gap-4 flex-wrap" style="color:rgba(255,255,255,.35);font-size:13px">
      <span><i class="ti ti-check me-1" style="color:var(--green)"></i>{{ __('No credit card needed') }}</span>
      <span><i class="ti ti-check me-1" style="color:var(--green)"></i>{{ __('Free plan available forever') }}</span>
      <span><i class="ti ti-check me-1" style="color:var(--green)"></i>{{ __('Setup in under 2 minutes') }}</span>
    </div>
  </div>
</section>

@endsection
