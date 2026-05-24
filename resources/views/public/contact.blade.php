@extends('public.layout')
@section('title', __('Contact Us'))
@section('meta-description', __("Get in touch with the MessManager team. We'd love to hear from you."))

@section('public-content')

<section class="pub-hero">
  <div class="container pub-hero-inner">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right"></i>
      <span>{{ __('Contact') }}</span>
    </div>
    <h1 data-aos="fade-up">{{ __('Get In') }} <span style="color:var(--orange)">{{ __('Touch') }}</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ __("Have a question, feedback, or need support? We're here to help and we respond within 24 hours.") }}</p>
  </div>
</section>

<section style="padding:80px 0;background:var(--white)">
  <div class="container">

    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4 rounded-3" data-aos="fade-down">
      <i class="ti ti-circle-check fs-4"></i>
      <div>{{ session('success') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4 rounded-3" data-aos="fade-down">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row g-5">
      {{-- Contact Info --}}
      <div class="col-lg-4" data-aos="fade-right">
        <div class="section-label mb-2">{{ __('Contact Info') }}</div>
        <h2 class="section-title mb-4" style="font-size:1.8rem">{{ __("Let's Talk") }}</h2>
        <p style="color:var(--muted);line-height:1.8;margin-bottom:28px">{{ __("Fill out the form or reach us through any of the channels below. We'll get back to you as soon as possible.") }}</p>

        @foreach([
          ['icon'=>'ti-mail','title'=>__('Email'),'val'=>'support@messmanager.app','color'=>'var(--orange)'],
          ['icon'=>'ti-phone','title'=>__('Phone'),'val'=>'+880 1700-000000','color'=>'var(--green)'],
          ['icon'=>'ti-map-pin','title'=>__('Address'),'val'=>__('Dhaka, Bangladesh'),'color'=>'#60a5fa'],
          ['icon'=>'ti-clock','title'=>__('Support Hours'),'val'=>__('Mon–Fri, 9 AM – 6 PM'),'color'=>'#a78bfa'],
        ] as $info)
        <div class="d-flex align-items-start gap-3 mb-4">
          <div style="width:44px;height:44px;border-radius:12px;background:{{ $info['color'] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="ti {{ $info['icon'] }}" style="color:{{ $info['color'] }};font-size:1.3rem"></i>
          </div>
          <div>
            <div style="font-weight:700;font-size:.85rem;color:var(--navy);text-transform:uppercase;letter-spacing:.5px">{{ $info['title'] }}</div>
            <div style="font-size:.9rem;color:var(--muted);margin-top:2px">{{ $info['val'] }}</div>
          </div>
        </div>
        @endforeach

        <div style="background:var(--navy);border-radius:16px;padding:24px;margin-top:24px">
          <div style="font-weight:700;color:var(--white);margin-bottom:8px">{{ __('Follow Us') }}</div>
          <div class="d-flex gap-3">
            @foreach(['ti-brand-facebook','ti-brand-twitter','ti-brand-linkedin','ti-brand-instagram'] as $icon)
            <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);font-size:1rem;transition:all .2s" onmouseover="this.style.background='rgba(254,159,67,.2)';this.style.color='var(--orange)'" onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.color='rgba(255,255,255,.5)'">
              <i class="ti {{ $icon }}"></i></a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Form --}}
      <div class="col-lg-8" data-aos="fade-left">
        <div style="background:var(--light);border:1px solid var(--border);border-radius:20px;padding:36px">
          <h3 style="font-weight:700;color:var(--navy);margin-bottom:6px">{{ __('Send a Message') }}</h3>
          <p style="color:var(--muted);font-size:.9rem;margin-bottom:28px">{{ __("All fields are required. We'll respond within 24 hours.") }}</p>

          <form action="{{ route('public.contact.store') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="font-size:.875rem">{{ __('Full Name') }} *</label>
                <div class="input-group">
                  <span class="input-group-text" style="border-right:0;background:var(--white)"><i class="ti ti-user text-muted"></i></span>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('Your full name') }}" style="border-left:0;padding-left:0">
                </div>
                @error('name')<div class="text-danger" style="font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="font-size:.875rem">{{ __('Email Address') }} *</label>
                <div class="input-group">
                  <span class="input-group-text" style="border-right:0;background:var(--white)"><i class="ti ti-mail text-muted"></i></span>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="your@email.com" style="border-left:0;padding-left:0">
                </div>
                @error('email')<div class="text-danger" style="font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" style="font-size:.875rem">{{ __('Subject') }} *</label>
                <div class="input-group">
                  <span class="input-group-text" style="border-right:0;background:var(--white)"><i class="ti ti-tag text-muted"></i></span>
                  <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="{{ __('What is this about?') }}" style="border-left:0;padding-left:0">
                </div>
                @error('subject')<div class="text-danger" style="font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" style="font-size:.875rem">{{ __('Message') }} *</label>
                <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror" placeholder="{{ __('Write your message here...') }}">{{ old('message') }}</textarea>
                @error('message')<div class="text-danger" style="font-size:.8rem;margin-top:4px">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <button type="submit" class="btn-orange w-100 justify-content-center" style="padding:14px">
                  <i class="ti ti-send"></i> {{ __('Send Message') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Map Placeholder --}}
    <div class="mt-5" data-aos="fade-up">
      <div style="background:var(--light);border:1px solid var(--border);border-radius:20px;height:280px;display:flex;align-items:center;justify-content:center;overflow:hidden">
        <div class="text-center">
          <i class="ti ti-map-2" style="font-size:3rem;color:var(--orange);opacity:.4;display:block;margin-bottom:12px"></i>
          <div style="font-weight:600;color:var(--navy)">{{ __('Our Office') }}</div>
          <div style="color:var(--muted);font-size:.875rem">{{ __('Dhaka, Bangladesh') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
