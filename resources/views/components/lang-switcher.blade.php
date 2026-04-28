@php $locale = app()->getLocale(); @endphp
<div class="dropdown lang-switcher-wrap">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 px-3 py-1"
            type="button" data-bs-toggle="dropdown" aria-expanded="false"
            style="border-radius:20px;font-size:13px;font-weight:600;min-width:100px">
        @if($locale === 'bn')
            <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="BD" height="14" style="border-radius:2px">
            বাংলা
        @else
            <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="US" height="14" style="border-radius:2px">
            English
        @endif
        <i class="ti ti-chevron-down" style="font-size:11px"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1" style="min-width:140px;border-radius:10px">
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $locale==='en'?'active':'' }}"
               href="{{ route('lang.switch','en') }}">
                <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="US" height="14" style="border-radius:2px">
                <span>English</span>
                @if($locale==='en') <i class="ti ti-check ms-auto text-success fs-12"></i> @endif
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $locale==='bn'?'active':'' }}"
               href="{{ route('lang.switch','bn') }}">
                <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="BD" height="14" style="border-radius:2px">
                <span>বাংলা</span>
                @if($locale==='bn') <i class="ti ti-check ms-auto text-success fs-12"></i> @endif
            </a>
        </li>
    </ul>
</div>
