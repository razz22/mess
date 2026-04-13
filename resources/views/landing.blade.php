<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MessManager — Smart Mess Management System for shared living">
    <title>MessManager — Smart Mess Management Platform</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('/build/img/favicon.png')}}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/tabler-icons.min.css') }}">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/style.css') }}">

    <style>
        :root {
            --primary: #7539ff;
            --primary-dark: #5b2dd9;
        }

        body { font-family: 'Inter', sans-serif; }

        /* Navbar */
        .landing-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 14px 0;
        }
        .landing-nav .nav-logo { font-size: 1.4rem; font-weight: 800; color: var(--primary); text-decoration: none; }
        .landing-nav .nav-logo span { color: #222; }

        /* Hero */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f5ff 0%, #eef2ff 50%, #f0fdf4 100%);
            display: flex; align-items: center;
            padding: 100px 0 60px;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(117, 57, 255, 0.1);
            color: var(--primary);
            border: 1px solid rgba(117, 57, 255, 0.2);
            padding: 6px 16px; border-radius: 50px;
            font-size: 13px; font-weight: 600;
            margin-bottom: 20px;
        }
        .hero-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.15; color: #1a1a2e; }
        .hero-title .highlight { color: var(--primary); }
        .hero-subtitle { font-size: 1.1rem; color: #6b7280; max-width: 540px; line-height: 1.7; }
        .hero-img-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .hero-img-header { background: var(--primary); padding: 12px 16px; display: flex; align-items: center; gap: 6px; }
        .hero-img-dot { width: 10px; height: 10px; border-radius: 50%; }
        .hero-mock { padding: 24px; }
        .mock-stat { background: #f9f9ff; border-radius: 12px; padding: 16px; border: 1px solid rgba(117,57,255,0.1); }

        /* Features / Services */
        .services-section { padding: 80px 0; background: white; }
        .section-badge { color: var(--primary); font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .section-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; color: #1a1a2e; }
        .service-card {
            border: 1px solid #f0eeff;
            border-radius: 16px;
            padding: 28px;
            height: 100%;
            transition: all 0.3s ease;
            background: white;
        }
        .service-card:hover { border-color: var(--primary); box-shadow: 0 8px 30px rgba(117,57,255,0.12); transform: translateY(-4px); }
        .service-icon {
            width: 56px; height: 56px;
            background: rgba(117, 57, 255, 0.08);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: var(--primary);
            margin-bottom: 16px;
        }
        .service-card h5 { font-size: 1rem; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
        .service-card p { font-size: 0.875rem; color: #6b7280; line-height: 1.6; margin-bottom: 0; }

        /* How It Works */
        .how-section { padding: 80px 0; background: #f8f5ff; }
        .step-number {
            width: 48px; height: 48px;
            background: var(--primary);
            color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 800;
            margin: 0 auto 16px;
        }

        /* Pricing */
        .pricing-section { padding: 80px 0; background: white; }
        .pricing-card {
            border: 2px solid #f0eeff;
            border-radius: 20px;
            padding: 36px;
            height: 100%;
            transition: all 0.3s;
        }
        .pricing-card.featured { border-color: var(--primary); background: linear-gradient(135deg, #f8f5ff, white); }
        .pricing-card .price { font-size: 2.5rem; font-weight: 800; color: var(--primary); }
        .pricing-card .price span { font-size: 1rem; font-weight: 500; color: #6b7280; }
        .pricing-card ul li { padding: 6px 0; color: #374151; font-size: 0.9rem; }
        .pricing-card ul li i { color: #10b981; }

        /* CTA */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        .cta-section h2 { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; }

        /* Footer */
        .landing-footer { background: #1a1a2e; color: #9ca3af; padding: 40px 0; }
        .landing-footer .footer-logo { font-size: 1.4rem; font-weight: 800; color: white; }
        .landing-footer a { color: #9ca3af; text-decoration: none; }
        .landing-footer a:hover { color: white; }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); color: white; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(117,57,255,0.3); }
        .btn-outline-custom {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 10px 26px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-outline-custom:hover { background: var(--primary); color: white; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="landing-nav">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="landing-nav nav-logo">
                <i class="ti ti-building-community me-1"></i>Mess<span>Manager</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="#services" class="text-muted text-decoration-none d-none d-md-inline fw-medium">Services</a>
                <a href="#pricing" class="text-muted text-decoration-none d-none d-md-inline fw-medium">Pricing</a>
                <a href="{{ route('signin') }}" class="btn-outline-custom btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary-custom btn-sm">Get Started</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge"><i class="ti ti-sparkles me-1"></i>Smart Mess Management Platform</div>
                <h1 class="hero-title">Manage Your <span class="highlight">Mess</span> Like Never Before</h1>
                <p class="hero-subtitle mt-3">
                    MessManager is a complete solution for shared living groups — track meals, manage expenses,
                    coordinate market duties, calculate monthly dues, and more. All in one place.
                </p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="{{ route('register') }}" class="btn-primary-custom">
                        <i class="ti ti-rocket me-2"></i>Start Free Today
                    </a>
                    <a href="#services" class="btn-outline-custom">
                        <i class="ti ti-eye me-2"></i>See Features
                    </a>
                </div>
                <div class="d-flex gap-4 mt-4 text-muted small">
                    <div><i class="ti ti-check text-success me-1"></i>Free plan available</div>
                    <div><i class="ti ti-check text-success me-1"></i>No credit card needed</div>
                    <div><i class="ti ti-check text-success me-1"></i>Setup in 2 minutes</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-img-wrapper">
                    <div class="hero-img-header">
                        <div class="hero-img-dot" style="background:#ff5f57"></div>
                        <div class="hero-img-dot" style="background:#ffbd2e"></div>
                        <div class="hero-img-dot" style="background:#28c840"></div>
                        <span class="text-white small ms-2">MessManager Dashboard</span>
                    </div>
                    <div class="hero-mock p-3">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="mock-stat">
                                    <div class="text-muted" style="font-size:11px">Total Members</div>
                                    <div class="fw-bold fs-5" style="color:var(--primary)">18 <span class="text-muted fs-12">/ 20</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mock-stat">
                                    <div class="text-muted" style="font-size:11px">This Month Deposit</div>
                                    <div class="fw-bold fs-5 text-success">৳54,000</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mock-stat">
                                    <div class="text-muted" style="font-size:11px">Total Expenses</div>
                                    <div class="fw-bold fs-5 text-warning">৳48,320</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mock-stat">
                                    <div class="text-muted" style="font-size:11px">Cash in Hand</div>
                                    <div class="fw-bold fs-5 text-info">৳5,680</div>
                                </div>
                            </div>
                        </div>
                        <div style="background:#f9f9ff; border-radius:12px; padding:12px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold small">Today's Meals</div>
                                <span class="badge" style="background:rgba(117,57,255,0.1);color:var(--primary);font-size:10px">April 7</span>
                            </div>
                            @foreach(['Breakfast' => ['time' => '8:00 AM', 'count' => '16', 'color' => 'warning'], 'Lunch' => ['time' => '1:00 PM', 'count' => '18', 'color' => 'success'], 'Dinner' => ['time' => '8:00 PM', 'count' => '15', 'color' => 'primary']] as $meal => $info)
                            <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                                <span class="small">{{ $meal }}</span>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="text-muted" style="font-size:11px">{{ $info['count'] }} attending</span>
                                    <span class="badge bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}" style="font-size:10px">Open</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge">Our Services</div>
            <h2 class="section-title mt-2">Everything Your Mess Needs</h2>
            <p class="text-muted">A complete platform covering every aspect of shared mess life</p>
        </div>
        <div class="row g-4">
            @php
            $services = [
                ['icon' => 'ti-tools-kitchen-2', 'title' => 'Meal Management', 'desc' => 'Track daily meal attendance for breakfast, lunch & dinner. Auto-mark members ON, set cut-off times, and manage meal off requests easily.'],
                ['icon' => 'ti-shopping-cart', 'title' => 'Market Routine', 'desc' => 'Assign market duty to members using a visual calendar. Build shopping lists, track spending, and allow members to exchange duties.'],
                ['icon' => 'ti-coins', 'title' => 'Expense Tracking', 'desc' => 'Log all mess expenses (electricity, gas, cook bill, service charge, etc.) with categories. Get a breakdown per member each month.'],
                ['icon' => 'ti-cash', 'title' => 'Deposit Management', 'desc' => 'Record monthly deposits from each member. Track who has paid, who is due, and keep a clear financial ledger for the mess.'],
                ['icon' => 'ti-report-analytics', 'title' => 'Monthly Reports', 'desc' => 'Auto-generate monthly reports showing meal cost, expense share, total payable, due amount, and carry-forward for each member.'],
                ['icon' => 'ti-crown', 'title' => 'Manager Rotation', 'desc' => 'Assign a new manager each month who handles operations. Members rate the manager with stars. Best manager gets rewards!'],
                ['icon' => 'ti-trophy', 'title' => 'Rewards & Points', 'desc' => 'Recognize outstanding members monthly. Award points and gifts to the best member, best market person, and top manager.'],
                ['icon' => 'ti-layout-kanban', 'title' => 'Meal Items Kanban', 'desc' => 'Plan daily meals with a visual Kanban board. Add items to cook, move them to "Cooking" and then "Done" — keep everyone informed.'],
                ['icon' => 'ti-flag', 'title' => 'Member Reporting', 'desc' => 'Members can report rule violations to the manager. Reporters earn points when their reports are resolved, promoting accountability.'],
                ['icon' => 'ti-users', 'title' => 'Member Management', 'desc' => 'Add, remove, and manage members with roles (owner, manager, author, member). Share an invite code for easy onboarding.'],
                ['icon' => 'ti-arrows-exchange', 'title' => 'Duty Exchange', 'desc' => 'Can\'t do market duty today? Request an exchange with another member. The other member accepts or rejects — fully managed in-app.'],
                ['icon' => 'ti-chart-bar', 'title' => 'Financial Summary', 'desc' => 'See cash in hand, total deposits vs expenses, per-meal cost, and individual dues — all calculated automatically at month end.'],
            ];
            @endphp
            @foreach($services as $service)
            <div class="col-md-6 col-xl-4">
                <div class="service-card">
                    <div class="service-icon"><i class="ti {{ $service['icon'] }}"></i></div>
                    <h5>{{ $service['title'] }}</h5>
                    <p>{{ $service['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge">How It Works</div>
            <h2 class="section-title mt-2">Up & Running in Minutes</h2>
        </div>
        <div class="row g-4 text-center">
            @foreach([
                ['num' => '1', 'icon' => 'ti-user-plus', 'title' => 'Create Account', 'desc' => 'Register for free with your name, email, and password. No credit card required.'],
                ['num' => '2', 'icon' => 'ti-building-community', 'title' => 'Create Your Mess', 'desc' => 'Set up your mess with a name and address. You get an 8-digit invite code instantly.'],
                ['num' => '3', 'icon' => 'ti-users', 'title' => 'Invite Members', 'desc' => 'Share the invite code with your housemates. They register and join with the code.'],
                ['num' => '4', 'icon' => 'ti-rocket', 'title' => 'Start Managing', 'desc' => 'Mark meals, assign market duty, record expenses, and generate monthly reports!'],
            ] as $step)
            <div class="col-md-6 col-xl-3">
                <div class="step-number">{{ $step['num'] }}</div>
                <div class="service-icon mx-auto mb-3"><i class="ti {{ $step['icon'] }}"></i></div>
                <h6 class="fw-bold">{{ $step['title'] }}</h6>
                <p class="text-muted small">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="pricing-section" id="pricing">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge">Pricing</div>
            <h2 class="section-title mt-2">Simple, Transparent Pricing</h2>
            <p class="text-muted">Start free, upgrade when you need more</p>
        </div>

        @php
            // Always show a Free card first, then paid plans from DB
            $hasPaidPlans = $plans->where('price', '>', 0)->count();
            $colClass = $hasPaidPlans ? 'col-md-' . max(3, min(4, intval(12 / ($hasPaidPlans + 1)))) : 'col-md-4';
        @endphp

        <div class="row g-4 justify-content-center">

            {{-- Free Plan (always shown, values from SystemSettings) --}}
            <div class="{{ $colClass }}">
                <div class="pricing-card">
                    <h5 class="fw-bold">Free</h5>
                    <div class="price mt-2">৳0 <span>/ month</span></div>
                    <hr>
                    <ul class="list-unstyled mt-3">
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Up to {{ $sysSettings->default_max_members }} members per mess</li>
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Create up to {{ $sysSettings->default_max_messes }} {{ Str::plural('mess', $sysSettings->default_max_messes) }}</li>
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>All core features</li>
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Meal attendance</li>
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Market routine</li>
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Monthly reports</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-outline-custom d-block text-center mt-4">Get Started Free</a>
                </div>
            </div>

            {{-- Paid Plans from DB --}}
            @foreach($plans->where('price', '>', 0) as $plan)
            <div class="{{ $colClass }}">
                <div class="pricing-card {{ $plan->is_featured ? 'featured' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="fw-bold">{{ $plan->name }}</h5>
                        @if($plan->is_featured)
                        <span class="badge" style="background:var(--primary)">Popular</span>
                        @endif
                    </div>
                    <div class="price mt-2">
                        ৳{{ number_format($plan->price, 0) }}
                        <span>/ {{ $plan->duration_months > 1 ? $plan->duration_months . ' months' : 'month' }}</span>
                    </div>
                    <hr>
                    <ul class="list-unstyled mt-3">
                        <li class="py-1"><i class="ti ti-check me-2 text-success"></i>Up to {{ $plan->max_members }} members per mess</li>
                        @if($plan->features)
                            @foreach($plan->features as $feature)
                            <li class="py-1"><i class="ti ti-check me-2 text-success"></i>{{ $feature }}</li>
                            @endforeach
                        @endif
                    </ul>
                    <a href="{{ route('register') }}"
                        class="{{ $plan->is_featured ? 'btn-primary-custom' : 'btn-outline-custom' }} d-block text-center mt-4">
                        {{ $plan->button_label ?: 'Get ' . $plan->name }}
                    </a>
                </div>
            </div>
            @endforeach

            {{-- Empty state: no paid plans yet --}}
            @if($plans->where('price', '>', 0)->isEmpty())
            <div class="col-12 text-center text-muted py-3">
                <p class="small">Upgrade plans coming soon. Start with the free plan today!</p>
            </div>
            @endif

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container text-center">
        <h2>Ready to Simplify Your Mess Life?</h2>
        <p class="mt-3 mb-4 opacity-75 fs-5">Join hundreds of messes already using MessManager to stay organized.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-light text-primary fw-bold px-4 py-3 rounded-3">
                <i class="ti ti-rocket me-2"></i>Create Free Account
            </a>
            <a href="{{ route('signin') }}" class="btn btn-outline-light fw-bold px-4 py-3 rounded-3">
                <i class="ti ti-login me-2"></i>Sign In
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="landing-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-logo mb-2"><i class="ti ti-building-community me-1"></i>MessManager</div>
                <p class="small">Smart mess management for shared living. Track meals, expenses, and more — all in one platform.</p>
            </div>
            <div class="col-md-2">
                <h6 class="text-white fw-bold mb-3">Product</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="#services">Features</a></li>
                    <li class="mb-1"><a href="#pricing">Pricing</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="text-white fw-bold mb-3">Account</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="{{ route('signin') }}">Sign In</a></li>
                    <li class="mb-1"><a href="{{ route('register') }}">Register</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-3">Quick Stats</h6>
                <div class="d-flex gap-4">
                    <div><div class="text-white fw-bold fs-5">500+</div><div class="small">Messes</div></div>
                    <div><div class="text-white fw-bold fs-5">10K+</div><div class="small">Members</div></div>
                    <div><div class="text-white fw-bold fs-5">৳2M+</div><div class="small">Tracked</div></div>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="text-center small">
            © {{ date('Y') }} MessManager. All rights reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="{{ URL::asset('build/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ URL::asset('build/js/bootstrap.bundle.min.js') }}"></script>
<script>
// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
});
// Navbar shadow on scroll
window.addEventListener('scroll', () => {
    document.querySelector('.landing-nav').style.boxShadow = window.scrollY > 20
        ? '0 4px 20px rgba(0,0,0,0.1)' : 'none';
});
</script>
</body>
</html>
