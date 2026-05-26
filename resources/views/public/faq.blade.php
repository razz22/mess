@extends('public.layout')
@section('title', __('FAQ'))
@section('meta-description', __('Frequently asked questions about Thaka Khawa — the smart mess management system.'))

@section('public-content')

<section class="pub-hero">
  <div class="container pub-hero-inner">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right"></i>
      <span>{{ __('FAQ') }}</span>
    </div>
    <h1 data-aos="fade-up">{{ __('Frequently Asked') }} <span style="color:var(--orange)">{{ __('Questions') }}</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ __("Everything you need to know about Thaka Khawa. Can't find your answer?") }} <a href="{{ route('public.contact') }}" style="color:var(--orange)">{{ __('Contact us') }}</a>.</p>
  </div>
</section>

<section style="padding:80px 0;background:var(--white)">
  <div class="container">

    {{-- Category filter --}}
    <div class="d-flex gap-2 flex-wrap mb-5 justify-content-center" data-aos="fade-up">
      @foreach([
        __('All'),
        __('Getting Started'),
        __('Billing'),
        __('Meals'),
        __('Members'),
        __('Expenses'),
        __('Reports'),
      ] as $cat)
      <button class="faq-filter btn btn-sm {{ $loop->first ? 'active' : '' }}" data-cat="{{ $cat }}"
        style="border-radius:50px;padding:8px 18px;font-weight:600;font-size:.85rem;
               background:{{ $loop->first ? 'var(--orange)' : 'var(--light)' }};
               color:{{ $loop->first ? 'var(--white)' : 'var(--navy)' }};
               border:1px solid {{ $loop->first ? 'var(--orange)' : 'var(--border)' }};
               transition:all .2s">{{ $cat }}</button>
      @endforeach
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-9">
        @php
        $faqs = [
          ['cat'=>__('Getting Started'),'q'=>__('How do I create a mess?'),'a'=>__("After registering, go to your dashboard and click \"Create Mess\". Enter your mess name and address. You'll immediately get an 8-digit invite code to share with your housemates. The whole setup takes under 2 minutes.")],
          ['cat'=>__('Getting Started'),'q'=>__('How do members join a mess?'),'a'=>__('Members register on Thaka Khawa and then click "Join Mess" from their dashboard. They enter the 8-digit invite code provided by the mess owner. After joining, the owner or manager can assign them a role.')],
          ['cat'=>__('Getting Started'),'q'=>__('What roles are available in a mess?'),'a'=>__('There are four roles: Owner (full control, billing), Manager (monthly rotating role with full operational access), Author (can post and manage certain content), and Member (basic access for meal marking and viewing reports).')],
          ['cat'=>__('Billing'),'q'=>__('Is Thaka Khawa free?'),'a'=>__('Yes! Thaka Khawa offers a generous free plan for messes up to a certain member limit. Paid plans unlock higher member limits and premium features. No credit card is required to start.')],
          ['cat'=>__('Billing'),'q'=>__('Can I upgrade or downgrade my plan?'),'a'=>__('Absolutely. You can request an upgrade at any time from your mess settings. The owner submits an upgrade request and the admin approves it. Downgrades take effect at the end of your current billing period.')],
          ['cat'=>__('Meals'),'q'=>__('How does meal tracking work?'),'a'=>__("The system has daily meal schedules for breakfast, lunch, and dinner. Members are marked ON by default. They can turn meals off (meal-off) or the manager can toggle attendance. Each meal has a cut-off time — after that, the schedule is locked automatically.")],
          ['cat'=>__('Meals'),'q'=>__('What is the Meal Kanban board?'),'a'=>__("The Meal Kanban board lets the cook or manager plan daily meals visually. You add items to cook in the \"To Do\" column, then drag them to \"Cooking\" and finally \"Done\" — keeping every member informed in real-time about what's being prepared.")],
          ['cat'=>__('Members'),'q'=>__('Can I remove a member from my mess?'),'a'=>__('Yes. As the owner or manager, you can remove any member from the Members page. Their historical meal and expense data is preserved in reports, but they lose access to the mess immediately.')],
          ['cat'=>__('Members'),'q'=>__('How does the market duty rotation work?'),'a'=>__("You can assign a member as the market person for any day using the market calendar. They receive a shopping list, log their purchases, and complete the duty. Members can also request a duty exchange if they're unavailable.")],
          ['cat'=>__('Expenses'),'q'=>__('How are monthly expenses split?'),'a'=>__("Thaka Khawa automatically calculates each member's share based on their meal count for the month. Fixed shared expenses (like electricity) are split equally. The system generates a complete breakdown showing total payable, deposit, and due amounts.")],
          ['cat'=>__('Reports'),'q'=>__('How do I generate a monthly report?'),'a'=>__('Go to Reports → Monthly Report, select the month, and click Generate. The system calculates meal costs, shared expense splits, deposits, dues, and carry-forward amounts for every member automatically.')],
          ['cat'=>__('Reports'),'q'=>__('Can members see their own financial summary?'),'a'=>__('Yes. Each member can view their personal report showing meals attended, their cost share, total deposit, amount due or excess, and carry-forward to next month — with full transparency.')],
        ];
        @endphp

        <div class="accordion" id="faqAccordion">
          @foreach($faqs as $i => $faq)
          <div class="faq-item accordion-item border-0 mb-3" data-cat="{{ $faq['cat'] }}" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 60 }}" style="border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06)">
            <h2 class="accordion-header">
              <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-semibold" type="button"
                      data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}"
                      style="background:var(--white);color:var(--navy);font-size:.95rem;border-radius:14px">
                <span class="me-3" style="width:28px;height:28px;border-radius:50%;background:rgba(254,159,67,.12);color:var(--orange);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;flex-shrink:0">{{ $i+1 }}</span>
                {{ $faq['q'] }}
              </button>
            </h2>
            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
              <div class="accordion-body" style="background:var(--white);color:var(--muted);line-height:1.8;padding:0 24px 20px 64px">
                {{ $faq['a'] }}
                <div class="mt-2">
                  <span style="font-size:.75rem;background:rgba(254,159,67,.1);color:var(--orange);padding:3px 10px;border-radius:50px;font-weight:600">{{ $faq['cat'] }}</span>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
          <div style="background:var(--light);border:1px solid var(--border);border-radius:20px;padding:36px">
            <i class="ti ti-help-circle" style="font-size:2.5rem;color:var(--orange);display:block;margin-bottom:12px"></i>
            <h4 style="font-weight:700;color:var(--navy)">{{ __('Still Have Questions?') }}</h4>
            <p style="color:var(--muted);margin:8px 0 20px">{{ __('Our support team is ready to help you get started.') }}</p>
            <a href="{{ route('public.contact') }}" class="btn-orange"><i class="ti ti-mail"></i> {{ __('Contact Support') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('extra-js')
<script>
document.querySelectorAll('.faq-filter').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.faq-filter').forEach(b => {
      b.style.background = 'var(--light)';
      b.style.color = 'var(--navy)';
      b.style.borderColor = 'var(--border)';
      b.classList.remove('active');
    });
    this.style.background = 'var(--orange)';
    this.style.color = 'var(--white)';
    this.style.borderColor = 'var(--orange)';
    this.classList.add('active');

    const cat = this.dataset.cat;
    const allLabel = document.querySelectorAll('.faq-filter')[0].dataset.cat;
    document.querySelectorAll('.faq-item').forEach(item => {
      item.style.display = (cat === allLabel || item.dataset.cat === cat) ? 'block' : 'none';
    });
  });
});
</script>
@endsection
