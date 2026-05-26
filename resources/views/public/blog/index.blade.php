@extends('public.layout')
@section('title', __('Blog') . ' — Thaka Khawa')
@section('meta-description', __('Read the latest articles, tips, and guides on mess management from the Thaka Khawa blog.'))

@section('extra-css')
<style>
/* ── Hero ── */
.blog-hero {
    background: linear-gradient(135deg, var(--navy) 0%, #1e1e5e 55%, #0d2137 100%);
    padding: 110px 0 72px;
    position: relative;
    overflow: hidden;
}
.blog-hero::before {
    content:''; position:absolute; top:-80px; right:-100px;
    width:480px; height:480px;
    background: radial-gradient(circle, rgba(254,159,67,.16) 0%, transparent 68%);
    border-radius:50%; pointer-events:none;
}
.blog-hero::after {
    content:''; position:absolute; bottom:-100px; left:-60px;
    width:360px; height:360px;
    background: radial-gradient(circle, rgba(62,183,128,.11) 0%, transparent 68%);
    border-radius:50%; pointer-events:none;
}

/* ── Filter Sidebar ── */
.blog-layout { display:flex; gap:28px; align-items:flex-start; }
.filter-sidebar {
    width: 268px;
    flex-shrink: 0;
    position: sticky;
    top: 88px;
}
.filter-sidebar-inner {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e9ecef;
    overflow: hidden;
}
.filter-header {
    background: var(--navy);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-header-title {
    font-size: .9rem;
    font-weight: 700;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.filter-header-title i { color: var(--orange); }
.filter-clear-btn {
    font-size: .75rem;
    color: rgba(255,255,255,.5);
    text-decoration: none;
    transition: color .18s;
    display: flex;
    align-items: center;
    gap: 4px;
}
.filter-clear-btn:hover { color: var(--orange); }

.filter-section {
    padding: 18px 20px;
    border-bottom: 1px solid #f3f4f6;
}
.filter-section:last-child { border-bottom: none; }
.filter-section-label {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #9ca3af;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-section-label i { font-size: .85rem; color: var(--orange); }

/* Search inside filter */
.filter-search-wrap { position: relative; }
.filter-search-wrap i {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: .9rem; pointer-events: none;
}
.filter-input {
    width: 100%;
    padding: 9px 10px 9px 34px;
    border: 1.5px solid #e9ecef;
    border-radius: 10px;
    font-size: .85rem;
    color: #374151;
    outline: none;
    transition: border-color .18s;
    background: #f9fafb;
}
.filter-input:focus { border-color: var(--orange); background: #fff; }

/* Date inputs */
.date-label { font-size: .73rem; color: #6b7280; margin-bottom: 4px; display:block; }
.filter-date {
    width: 100%;
    padding: 8px 10px;
    border: 1.5px solid #e9ecef;
    border-radius: 10px;
    font-size: .82rem;
    color: #374151;
    outline: none;
    transition: border-color .18s;
    background: #f9fafb;
}
.filter-date:focus { border-color: var(--orange); background: #fff; }

/* Category list */
.cat-filter-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s;
    text-decoration: none;
    color: #374151;
    font-size: .85rem;
    font-weight: 500;
    margin-bottom: 2px;
}
.cat-filter-item:hover { background: #f9fafb; color: #374151; }
.cat-filter-item.active { background: rgba(254,159,67,.1); color: var(--orange); font-weight: 700; }
.cat-filter-item .cat-count {
    font-size: .72rem;
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 50px;
    padding: 2px 9px;
    flex-shrink: 0;
}
.cat-filter-item.active .cat-count { background: rgba(254,159,67,.15); color: var(--orange); }

/* Tag checkboxes */
.tag-check-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 0;
    border-bottom: 1px solid #f9fafb;
    cursor: pointer;
}
.tag-check-item:last-child { border-bottom: none; }
.tag-check-item input[type=checkbox] { display: none; }
.tag-checkbox-box {
    width: 18px; height: 18px;
    border: 2px solid #dee2e6;
    border-radius: 5px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.tag-check-item.checked .tag-checkbox-box {
    background: var(--orange);
    border-color: var(--orange);
}
.tag-check-item.checked .tag-checkbox-box::after {
    content: '✓';
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    line-height: 1;
}
.tag-check-label { font-size: .83rem; color: #374151; flex: 1; }
.tag-check-count { font-size: .7rem; color: #9ca3af; }

/* Sort radio */
.sort-radio-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s;
    margin-bottom: 2px;
}
.sort-radio-item:hover { background: #f9fafb; }
.sort-radio-item.active { background: rgba(62,183,128,.1); }
.sort-radio-item input { display: none; }
.sort-dot {
    width: 16px; height: 16px;
    border: 2px solid #dee2e6;
    border-radius: 50%;
    flex-shrink: 0;
    transition: all .15s;
    display: flex; align-items:center; justify-content:center;
}
.sort-radio-item.active .sort-dot {
    border-color: var(--green);
    background: var(--green);
}
.sort-radio-item.active .sort-dot::after {
    content:'';
    width:6px; height:6px;
    background:#fff;
    border-radius:50%;
}
.sort-label { font-size: .83rem; color: #374151; font-weight: 500; }
.sort-radio-item.active .sort-label { color: var(--green); font-weight: 700; }

/* Apply button */
.filter-apply-btn {
    width: 100%;
    padding: 11px;
    background: var(--orange);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: .875rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s, transform .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.filter-apply-btn:hover { background: #e8890c; transform: translateY(-1px); }

/* Mobile filter toggle */
.filter-mobile-toggle {
    display: none;
    align-items: center;
    gap: 8px;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 9px 16px;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
}
@media(max-width:991px) {
    .blog-layout { display: block; }
    .filter-sidebar { width:100%; position:static; margin-bottom:24px; }
    .filter-mobile-toggle { display: flex; }
    .filter-sidebar-inner { display: none; }
    .filter-sidebar-inner.show { display: block; }
}

/* Blog cards */
.blog-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    overflow: hidden;
    transition: transform .28s ease, box-shadow .28s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 44px rgba(20,20,50,.11);
}
.blog-card-thumb {
    height: 198px;
    overflow: hidden;
    position: relative;
    background: linear-gradient(135deg, var(--navy) 0%, #1a1a5e 100%);
    flex-shrink: 0;
}
.blog-card-thumb img {
    width:100%; height:100%; object-fit:cover;
    transition: transform .5s ease;
}
.blog-card:hover .blog-card-thumb img { transform: scale(1.06); }
.card-cat-badge {
    position:absolute; top:12px; left:12px;
    background:var(--orange); color:#fff;
    font-size:.68rem; font-weight:700;
    padding:3px 10px; border-radius:50px;
}
.card-feat-badge {
    position:absolute; top:12px; right:12px;
    background:var(--green); color:#fff;
    font-size:.68rem; font-weight:700;
    padding:3px 10px; border-radius:50px;
}
.read-time-badge {
    position:absolute; bottom:10px; right:10px;
    background:rgba(0,0,0,.55); backdrop-filter:blur(4px);
    color:#fff; font-size:.68rem; font-weight:600;
    padding:3px 9px; border-radius:50px;
}
.blog-card-body { padding:18px; flex:1; display:flex; flex-direction:column; }
.blog-card-title { font-size:.93rem; font-weight:700; color:var(--navy); line-height:1.45; margin-bottom:7px; }
.blog-card-title a { color:inherit; text-decoration:none; transition:color .2s; }
.blog-card-title a:hover { color:var(--orange); }
.blog-card-excerpt { font-size:.8rem; color:#6b7280; line-height:1.65; flex:1; }
.blog-card-meta { border-top:1px solid #f3f4f6; padding-top:11px; margin-top:13px; }
.author-dot {
    width:26px; height:26px; border-radius:50%;
    background:var(--orange); color:#fff;
    font-size:.65rem; font-weight:700;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.blog-card-tags { display:flex; gap:5px; flex-wrap:wrap; padding:0 18px 14px; }
.blog-tag-pill {
    font-size:.7rem; background:#f3f4f6; color:#6b7280;
    border-radius:50px; padding:2px 9px; text-decoration:none;
    transition:all .18s;
}
.blog-tag-pill:hover { background:var(--orange); color:#fff; }

/* Active filter strip */
.active-filters-strip {
    display:flex; flex-wrap:wrap; gap:6px;
    align-items:center; margin-bottom:18px;
}
.afilter-tag {
    background:var(--navy); color:#fff;
    font-size:.75rem; padding:4px 12px;
    border-radius:50px;
    display:inline-flex; align-items:center; gap:6px;
}
.afilter-tag a { color:rgba(255,255,255,.6); text-decoration:none; transition:color .15s; }
.afilter-tag a:hover { color:var(--orange); }

/* Featured strip */
.featured-strip {
    background:linear-gradient(120deg,var(--navy) 0%,#1e1e5e 100%);
    border-radius:18px; overflow:hidden;
    margin-bottom:28px;
    display:flex; min-height:220px;
}
.featured-strip-img { width:38%; object-fit:cover; flex-shrink:0; }
.featured-strip-body { padding:28px 24px; display:flex; flex-direction:column; justify-content:center; flex:1; }

/* Pagination */
.blog-pagination { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:40px; flex-wrap:wrap; }
.page-btn {
    width:38px; height:38px; display:flex; align-items:center; justify-content:center;
    border-radius:10px; font-size:.85rem; font-weight:600; text-decoration:none;
    border:1.5px solid #e9ecef; background:#fff; color:var(--navy);
    transition:all .18s;
}
.page-btn:hover, .page-btn.active { background:var(--orange); border-color:var(--orange); color:#fff; }
.page-btn.disabled { background:#f3f4f6; border-color:#f3f4f6; color:#d1d5db; pointer-events:none; }
.page-btn-wide { width:auto; padding:0 16px; }
</style>
@endsection

@section('public-content')

{{-- HERO --}}
<section class="blog-hero">
  <div class="container" style="position:relative;z-index:1">
    <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px" data-aos="fade-right">
      <a href="{{ url('/') }}" style="font-size:.82rem;color:rgba(255,255,255,.45);text-decoration:none">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right" style="font-size:.7rem;color:rgba(255,255,255,.3)"></i>
      <span style="font-size:.82rem;color:rgba(255,255,255,.7)">{{ __('Blog') }}</span>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(254,159,67,.15);border:1px solid rgba(254,159,67,.3);border-radius:50px;padding:5px 14px;margin-bottom:18px">
          <i class="ti ti-news" style="color:var(--orange);font-size:.85rem"></i>
          <span style="color:var(--orange);font-size:.78rem;font-weight:700;letter-spacing:.3px">{{ __('Thaka Khawa Blog') }}</span>
        </div>
        <h1 style="font-size:clamp(1.9rem,3.8vw,2.9rem);font-weight:800;color:#fff;line-height:1.22;margin-bottom:14px">
          {{ __('Insights for Better Mess Living') }}
        </h1>
        <p style="color:rgba(255,255,255,.58);font-size:1rem;line-height:1.7;max-width:480px">
          {{ __('Tips, guides, and stories about shared living, mess management, budgeting, and community.') }}
        </p>
      </div>
      <div class="col-lg-6 d-none d-lg-flex justify-content-end" data-aos="fade-left">
        @php
          $heroStats = [
            ['icon'=>'ti-article','val'=>\App\Models\Blog::published()->count(),'label'=>__('Articles'),'color'=>'var(--orange)'],
            ['icon'=>'ti-users','val'=>\App\Models\Blog::published()->distinct('user_id')->count('user_id'),'label'=>__('Authors'),'color'=>'#60a5fa'],
            ['icon'=>'ti-heart','val'=>\App\Models\BlogLike::count(),'label'=>__('Likes'),'color'=>'#f87171'],
            ['icon'=>'ti-eye','val'=>number_format(\App\Models\Blog::published()->sum('views')),'label'=>__('Views'),'color'=>'var(--green)'],
          ];
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:300px">
          @foreach($heroStats as $s)
          <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:20px 14px;text-align:center;backdrop-filter:blur(6px)">
            <i class="ti {{ $s['icon'] }}" style="font-size:1.5rem;color:{{ $s['color'] }};display:block;margin-bottom:7px"></i>
            <div style="font-size:1.3rem;font-weight:800;color:#fff">{{ $s['val'] }}</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.45);font-weight:500">{{ $s['label'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- MAIN --}}
<section style="padding:48px 0 80px;background:#f8f9fa;min-height:60vh">
  <div class="container">

    {{-- Mobile filter toggle --}}
    <button class="filter-mobile-toggle" onclick="document.getElementById('filterSidebarInner').classList.toggle('show')">
      <i class="ti ti-adjustments-horizontal"></i> {{ __('Filters') }}
      @php
        $afc = (int)request()->filled('category') + (int)(count((array)request('tag'))>0) + (int)request()->filled('date_from') + (int)request()->filled('date_to') + (int)(request('sort') && request('sort')!=='latest');
      @endphp
      @if($afc > 0)
      <span style="background:var(--orange);color:#fff;border-radius:50px;font-size:.7rem;padding:1px 8px">{{ $afc }}</span>
      @endif
    </button>

    <div class="blog-layout">

      {{-- ── LEFT FILTER SIDEBAR ── --}}
      <aside class="filter-sidebar">
        <div class="filter-sidebar-inner" id="filterSidebarInner">

          {{-- Header --}}
          <div class="filter-header">
            <div class="filter-header-title">
              <i class="ti ti-adjustments-horizontal"></i> {{ __('Filters') }}
              @if($afc > 0)
              <span style="background:var(--orange);border-radius:50px;font-size:.7rem;padding:1px 8px">{{ $afc }}</span>
              @endif
            </div>
            @if($afc > 0)
            <a href="{{ route('blog.index') }}" class="filter-clear-btn"><i class="ti ti-refresh"></i> {{ __('Clear all') }}</a>
            @endif
          </div>

          <form action="{{ route('blog.index') }}" method="GET" id="filterForm">

            {{-- Search --}}
            <div class="filter-section">
              <div class="filter-section-label"><i class="ti ti-search"></i> {{ __('Search') }}</div>
              <div class="filter-search-wrap">
                <i class="ti ti-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Keywords...') }}" class="filter-input">
              </div>
            </div>

            {{-- Date Range --}}
            <div class="filter-section">
              <div class="filter-section-label"><i class="ti ti-calendar"></i> {{ __('Date Range') }}</div>
              <div class="d-flex flex-column gap-2">
                <div>
                  <span class="date-label">{{ __('From') }}</span>
                  <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-date">
                </div>
                <div>
                  <span class="date-label">{{ __('To') }}</span>
                  <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-date">
                </div>
              </div>
            </div>

            {{-- Category --}}
            <div class="filter-section">
              <div class="filter-section-label"><i class="ti ti-folder"></i> {{ __('Category') }}</div>
              <a href="javascript:void(0)" onclick="clearCategory()" class="cat-filter-item {{ !request('category') ? 'active' : '' }}">
                <span>{{ __('All Categories') }}</span>
                <span class="cat-count">{{ $categories->sum('blogs_count') }}</span>
              </a>
              @foreach($categories as $cat)
              <label class="cat-filter-item {{ request('category') == $cat->id ? 'active' : '' }}" style="cursor:pointer">
                <input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }} style="display:none">
                <span>{{ $cat->name }}</span>
                <span class="cat-count">{{ $cat->blogs_count }}</span>
              </label>
              @endforeach
            </div>

            {{-- Tags --}}
            @if($tags->count())
            <div class="filter-section">
              <div class="filter-section-label"><i class="ti ti-tag"></i> {{ __('Tags') }}</div>
              <div style="max-height:200px;overflow-y:auto">
                @foreach($tags as $tag)
                @php $tagChecked = in_array($tag->slug, (array)request('tag')); @endphp
                <label class="tag-check-item {{ $tagChecked ? 'checked' : '' }}" onclick="this.classList.toggle('checked')">
                  <input type="checkbox" name="tag[]" value="{{ $tag->slug }}" {{ $tagChecked ? 'checked' : '' }}>
                  <div class="tag-checkbox-box"></div>
                  <span class="tag-check-label">{{ $tag->name }}</span>
                  <span class="tag-check-count">({{ $tag->blogs_count }})</span>
                </label>
                @endforeach
              </div>
            </div>
            @endif

            {{-- Sort --}}
            <div class="filter-section">
              <div class="filter-section-label"><i class="ti ti-arrows-sort"></i> {{ __('Sort By') }}</div>
              @foreach(['latest'=>[__('Latest First'),'ti-clock'],'popular'=>[__('Most Viewed'),'ti-eye'],'liked'=>[__('Most Liked'),'ti-heart']] as $val=>$info)
              @php $sortActive = request('sort', 'latest') === $val; @endphp
              <label class="sort-radio-item {{ $sortActive ? 'active' : '' }}" onclick="this.classList.add('active');this.closest('.filter-section').querySelectorAll('.sort-radio-item').forEach(el=>el!==this&&el.classList.remove('active'))">
                <input type="radio" name="sort" value="{{ $val }}" {{ $sortActive ? 'checked' : '' }}>
                <div class="sort-dot"></div>
                <i class="ti {{ $info[1] }}" style="font-size:.85rem;color:{{ $sortActive ? 'var(--green)' : '#9ca3af' }}"></i>
                <span class="sort-label">{{ $info[0] }}</span>
              </label>
              @endforeach
            </div>

            {{-- Apply --}}
            <div class="filter-section">
              <button type="submit" class="filter-apply-btn">
                <i class="ti ti-filter"></i> {{ __('Apply Filters') }}
              </button>
            </div>

          </form>
        </div>
      </aside>

      {{-- ── RIGHT CONTENT ── --}}
      <div style="flex:1;min-width:0">

        {{-- Active filter chips --}}
        @php $hasFilters = request()->hasAny(['search','category','tag','date_from','date_to']); @endphp
        @if($hasFilters)
        <div class="active-filters-strip">
          <span style="font-size:.75rem;color:#9ca3af;font-weight:600;margin-right:2px">{{ __('Active:') }}</span>
          @if(request('search'))
          <span class="afilter-tag">
            <i class="ti ti-search" style="font-size:.72rem"></i> "{{ Str::limit(request('search'),25) }}"
            <a href="{{ route('blog.index', request()->except('search','page')) }}"><i class="ti ti-x" style="font-size:.68rem"></i></a>
          </span>
          @endif
          @if(request('category') && ($ac=$categories->firstWhere('id',request('category'))))
          <span class="afilter-tag">
            <i class="ti ti-folder" style="font-size:.72rem"></i> {{ $ac->name }}
            <a href="{{ route('blog.index', request()->except('category','page')) }}"><i class="ti ti-x" style="font-size:.68rem"></i></a>
          </span>
          @endif
          @foreach((array)request('tag') as $ts)
            @if($to=$tags->firstWhere('slug',$ts))
            <span class="afilter-tag">
              <i class="ti ti-tag" style="font-size:.72rem"></i> {{ $to->name }}
              <a href="{{ route('blog.index', array_merge(request()->except('page'),['tag'=>array_diff((array)request('tag'),[$ts])])) }}">
                <i class="ti ti-x" style="font-size:.68rem"></i>
              </a>
            </span>
            @endif
          @endforeach
          @if(request('date_from')||request('date_to'))
          <span class="afilter-tag">
            <i class="ti ti-calendar" style="font-size:.72rem"></i>
            {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M j, Y') : '…' }}
            – {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M j, Y') : 'now' }}
            <a href="{{ route('blog.index', request()->except('date_from','date_to','page')) }}"><i class="ti ti-x" style="font-size:.68rem"></i></a>
          </span>
          @endif
          <a href="{{ route('blog.index') }}" style="font-size:.75rem;color:#9ca3af;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:50px;border:1px dashed #dee2e6">
            <i class="ti ti-x" style="font-size:.7rem"></i> {{ __('Clear all') }}
          </a>
        </div>
        @endif

        {{-- Results bar --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:8px">
          <div style="font-size:.85rem;color:#6b7280">
            <strong style="color:var(--navy)">{{ $blogs->total() }}</strong> {{ $blogs->total()!=1?__('articles'):__('article') }} {{ __('found') }}
            @if($blogs->total()>0) — {{ __('page') }} {{ $blogs->currentPage() }} {{ __('of') }} {{ $blogs->lastPage() }} @endif
          </div>
          @auth
          <a href="{{ route('member.blog.create') }}" class="btn-orange" style="font-size:.8rem;padding:8px 16px">
            <i class="ti ti-plus me-1"></i> {{ __('Write Article') }}
          </a>
          @endauth
        </div>

        {{-- Featured strip --}}
        @if($featured && !$hasFilters)
        <div class="featured-strip" data-aos="fade-up">
          @if($featured->thumbnail)
          <img src="{{ asset("storage/".$featured->thumbnail) }}" class="featured-strip-img d-none d-md-block" alt="{{ $featured->title }}">
          @endif
          <div class="featured-strip-body">
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(254,159,67,.2);border-radius:50px;padding:4px 12px;margin-bottom:12px;width:fit-content">
              <i class="ti ti-star-filled" style="color:var(--orange);font-size:.78rem"></i>
              <span style="color:var(--orange);font-size:.72rem;font-weight:700;letter-spacing:.3px">{{ __('FEATURED') }}</span>
            </div>
            <h2 style="font-size:1.2rem;font-weight:800;color:#fff;line-height:1.35;margin-bottom:9px">
              <a href="{{ route('blog.show', $featured->slug) }}" style="color:inherit;text-decoration:none">{{ Str::limit($featured->title,80) }}</a>
            </h2>
            @if($featured->excerpt)
            <p style="color:rgba(255,255,255,.55);font-size:.83rem;line-height:1.65;margin-bottom:16px">{{ Str::limit($featured->excerpt,110) }}</p>
            @endif
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
              <div style="display:flex;align-items:center;gap:8px">
                <div class="author-dot">{{ strtoupper(substr($featured->user->name,0,1)) }}</div>
                <div>
                  <div style="font-size:.78rem;color:#fff;font-weight:600">{{ $featured->user->name }}</div>
                  <div style="font-size:.7rem;color:rgba(255,255,255,.4)">{{ $featured->published_at?->format('M j, Y') }}</div>
                </div>
              </div>
              <a href="{{ route('blog.show', $featured->slug) }}" class="btn-orange" style="padding:7px 16px;font-size:.78rem;margin-left:auto">
                {{ __('Read') }} <i class="ti ti-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
        @endif

        {{-- Cards --}}
        @if($blogs->isEmpty())
        <div style="text-align:center;padding:64px 0">
          <i class="ti ti-article-off" style="font-size:3.5rem;color:#d1d5db;display:block;margin-bottom:16px"></i>
          <h4 style="color:var(--navy);font-weight:700;margin-bottom:8px">{{ __('No Articles Found') }}</h4>
          <p style="color:#6b7280;margin-bottom:24px">{{ __('Try adjusting your filters or search terms.') }}</p>
          <a href="{{ route('blog.index') }}" class="btn-orange" style="padding:11px 28px">
            <i class="ti ti-refresh me-1"></i> {{ __('Clear All Filters') }}
          </a>
        </div>
        @else
        <div class="row g-4">
          @foreach($blogs as $blog)
          <div class="col-sm-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3)*60 }}">
            <div class="blog-card">
              <div class="blog-card-thumb">
                @if($blog->thumbnail)
                  <img src="{{ asset("storage/".$blog->thumbnail) }}" alt="{{ $blog->title }}">
                @else
                  <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                    <i class="ti ti-article" style="font-size:2.5rem;color:rgba(255,255,255,.08)"></i>
                  </div>
                @endif
                @if($blog->category)<span class="card-cat-badge">{{ $blog->category->name }}</span>@endif
                @if($blog->featured)<span class="card-feat-badge">★ {{ __('Featured') }}</span>@endif
                <span class="read-time-badge"><i class="ti ti-clock me-1"></i>{{ $blog->read_time }}m</span>
              </div>
              <div class="blog-card-body">
                <h5 class="blog-card-title">
                  <a href="{{ route('blog.show', $blog->slug) }}">{{ Str::limit($blog->title, 62) }}</a>
                </h5>
                @if($blog->excerpt)
                <p class="blog-card-excerpt">{{ Str::limit($blog->excerpt, 100) }}</p>
                @endif
                <div class="blog-card-meta">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="author-dot">{{ strtoupper(substr($blog->user->name,0,1)) }}</div>
                    <div style="font-size:.78rem;color:var(--navy);font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $blog->user->name }}</div>
                    <div style="font-size:.72rem;color:#9ca3af;flex-shrink:0">{{ $blog->published_at?->format('M j, Y') }}</div>
                  </div>
                  <div class="d-flex gap-3" style="font-size:.73rem;color:#9ca3af">
                    <span><i class="ti ti-eye me-1"></i>{{ number_format($blog->views) }}</span>
                    <span><i class="ti ti-heart me-1"></i>{{ $blog->likes->count() }}</span>
                    <span><i class="ti ti-message-circle me-1"></i>{{ $blog->allComments->count() }}</span>
                    <a href="{{ route('blog.show', $blog->slug) }}" style="margin-left:auto;font-size:.78rem;font-weight:700;color:var(--orange);text-decoration:none">Read →</a>
                  </div>
                </div>
              </div>
              @if($blog->tags->count())
              <div class="blog-card-tags">
                @foreach($blog->tags->take(3) as $tag)
                <a href="{{ route('blog.index', ['tag'=>[$tag->slug]]) }}" class="blog-tag-pill">{{ $tag->name }}</a>
                @endforeach
              </div>
              @endif
            </div>
          </div>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if($blogs->hasPages())
        <div class="blog-pagination">
          @if($blogs->onFirstPage())
          <span class="page-btn page-btn-wide disabled">← {{ __('Prev') }}</span>
          @else
          <a href="{{ $blogs->previousPageUrl() }}" class="page-btn page-btn-wide">← {{ __('Prev') }}</a>
          @endif

          @foreach($blogs->getUrlRange(max(1,$blogs->currentPage()-2), min($blogs->lastPage(),$blogs->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="page-btn {{ $page==$blogs->currentPage()?'active':'' }}">{{ $page }}</a>
          @endforeach

          @if($blogs->hasMorePages())
          <a href="{{ $blogs->nextPageUrl() }}" class="page-btn page-btn-wide">{{ __('Next') }} →</a>
          @else
          <span class="page-btn page-btn-wide disabled">{{ __('Next') }} →</span>
          @endif
        </div>
        @endif
        @endif

      </div>{{-- end right --}}
    </div>{{-- end blog-layout --}}
  </div>
</section>

@section('extra-js')
<script>
// Keep category radio "All" working by clearing the input
function clearCategory() {
    document.querySelectorAll('input[name="category"]').forEach(r => r.checked = false);
    document.querySelectorAll('.cat-filter-item').forEach(el => el.classList.remove('active'));
    document.querySelector('.cat-filter-item:first-child')?.classList.add('active');
}
// Sync radio click → active class
document.querySelectorAll('input[name="category"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.cat-filter-item').forEach(el => el.classList.remove('active'));
        radio.closest('.cat-filter-item')?.classList.add('active');
    });
});
// Sync checkbox clicks → checked class + update hidden checkbox
document.querySelectorAll('.tag-check-item').forEach(label => {
    label.addEventListener('click', () => {
        const cb = label.querySelector('input[type=checkbox]');
        // classList already toggled by onclick on label, sync checkbox
        cb.checked = label.classList.contains('checked');
    });
});
// Sync sort radio → active class
document.querySelectorAll('input[name="sort"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.sort-radio-item').forEach(el => el.classList.remove('active'));
        radio.closest('.sort-radio-item')?.classList.add('active');
    });
});
</script>
@endsection

@endsection
