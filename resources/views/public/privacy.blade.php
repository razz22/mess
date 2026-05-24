@extends('public.layout')
@section('title', __('Privacy Policy'))
@section('meta-description', __('MessManager Privacy Policy — how we collect, use, and protect your data.'))

@section('public-content')

<section class="pub-hero">
  <div class="container pub-hero-inner">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right"></i>
      <span>{{ __('Privacy Policy') }}</span>
    </div>
    <h1 data-aos="fade-up">{{ __('Privacy') }} <span style="color:var(--orange)">{{ __('Policy') }}</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ __('Last updated: May 15, 2026. We take your privacy seriously.') }}</p>
  </div>
</section>

<section style="padding:80px 0;background:var(--white)">
  <div class="container">
    <div class="row g-5">
      {{-- TOC --}}
      <div class="col-lg-3 d-none d-lg-block" data-aos="fade-right">
        <div style="position:sticky;top:90px;background:var(--light);border:1px solid var(--border);border-radius:16px;padding:24px">
          <div style="font-weight:700;color:var(--navy);font-size:.875rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px">{{ __('Table of Contents') }}</div>
          @foreach([
            [__('1. Introduction'),'#intro'],
            [__('2. Data We Collect'),'#data-collect'],
            [__('3. How We Use It'),'#how-use'],
            [__('4. Cookies'),'#cookies'],
            [__('5. Third Parties'),'#third-parties'],
            [__('6. Data Security'),'#security'],
            [__('7. Your Rights'),'#rights'],
            [__('8. Contact Us'),'#contact'],
          ] as $toc)
          <a href="{{ $toc[1] }}" style="display:block;padding:8px 0;font-size:.875rem;color:var(--muted);border-bottom:1px solid var(--border);transition:color .2s" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--muted)'">{{ $toc[0] }}</a>
          @endforeach
        </div>
      </div>

      {{-- Content --}}
      <div class="col-lg-9">
        @php
        $sections = [
          ['id'=>'intro','title'=>__('1. Introduction'),'icon'=>'ti-info-circle','content'=>__('MessManager ("we", "us", or "our") operates the MessManager web application (the "Service"). This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data. By using the Service, you agree to the collection and use of information in accordance with this policy.')],
          ['id'=>'data-collect','title'=>__('2. Data We Collect'),'icon'=>'ti-database','content'=>__('We collect several different types of information for various purposes to provide and improve our Service to you. This includes: (a) Personal identification information such as name and email address when you register; (b) Mess and membership data including meal attendance records, expense entries, and deposit records you enter; (c) Usage data such as pages visited, time spent, browser type, and IP address; (d) Device information including operating system and browser version for compatibility purposes.')],
          ['id'=>'how-use','title'=>__('3. How We Use Your Data'),'icon'=>'ti-settings','content'=>__('We use the collected data for the following purposes: to provide and maintain the Service; to notify you about changes to our Service; to allow you to participate in interactive features; to provide customer support; to gather analysis or valuable information so that we can improve the Service; to monitor the usage of the Service; to detect, prevent and address technical issues; to calculate and display your monthly mess reports accurately.')],
          ['id'=>'cookies','title'=>__('4. Cookies'),'icon'=>'ti-cookie','content'=>__('We use cookies and similar tracking technologies to track the activity on our Service and hold certain information. Cookies are files with small amounts of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Service. We use session cookies for authentication and preference cookies to remember your settings.')],
          ['id'=>'third-parties','title'=>__('5. Third-Party Services'),'icon'=>'ti-link','content'=>__("Our Service may contain links to other sites that are not operated by us. If you click on a third party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit. We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services. We use Google OAuth for authentication — their privacy policy applies to that interaction.")],
          ['id'=>'security','title'=>__('6. Data Security'),'icon'=>'ti-shield-check','content'=>__('The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your personal data (including password hashing, HTTPS, encrypted sessions), we cannot guarantee its absolute security. We perform regular security reviews and promptly address any vulnerabilities identified.')],
          ['id'=>'rights','title'=>__('7. Your Rights'),'icon'=>'ti-user-check','content'=>__('You have the right to: access the personal data we hold about you; request correction of inaccurate data; request deletion of your account and associated data; withdraw consent for data processing at any time; receive your data in a portable format; lodge a complaint with a data protection authority. To exercise any of these rights, please contact us at the details below. We will respond within 30 days.')],
          ['id'=>'contact','title'=>__('8. Contact Us'),'icon'=>'ti-mail','content'=>__('If you have any questions about this Privacy Policy, please contact us at: Email: privacy@messmanager.app | Address: MessManager, Dhaka, Bangladesh. We are committed to resolving any privacy concerns you have about our Service.')],
        ];
        @endphp

        @foreach($sections as $sec)
        <div id="{{ $sec['id'] }}" class="mb-5" data-aos="fade-up">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(254,159,67,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="ti {{ $sec['icon'] }}" style="color:var(--orange);font-size:1.3rem"></i>
            </div>
            <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:0">{{ $sec['title'] }}</h2>
          </div>
          <div style="background:var(--light);border-left:3px solid var(--orange);padding:20px 24px;border-radius:0 12px 12px 0;color:var(--muted);line-height:1.8;font-size:.9rem">
            {{ $sec['content'] }}
          </div>
        </div>
        @endforeach

        <div style="background:var(--navy);border-radius:16px;padding:24px;margin-top:32px" data-aos="fade-up">
          <div style="font-weight:700;color:var(--white);margin-bottom:8px">{{ __('Questions About This Policy?') }}</div>
          <p style="color:rgba(255,255,255,.6);font-size:.875rem;margin-bottom:16px">{{ __("We're here to answer any privacy-related questions.") }}</p>
          <a href="{{ route('public.contact') }}" class="btn-orange" style="font-size:.875rem;padding:10px 20px"><i class="ti ti-mail"></i> {{ __('Contact Us') }}</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
