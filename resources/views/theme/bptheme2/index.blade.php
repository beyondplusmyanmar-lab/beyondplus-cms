@extends('theme.bptheme2.layouts.app')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $localize = function ($post) use ($mm) {
        if ($mm && isset($post->translate) && $post->translate->lang == 2) { return $post->translate; }
        return $post;
    };
    $catOf = function ($post) {
        return optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
    };
    $sliders = bp_slider();
    $posts = bp_post(8);
@endphp

{{-- ── Hero ── --}}
<section class="container position-relative text-center" style="padding-top:5rem;padding-bottom:3.5rem;">
    <div class="nc-eyebrow mb-3">{{ $mm ? 'ကြိုဆိုပါသည်' : 'Welcome' }}</div>
    <h1 class="nc-gradient-text display-3 fw-bold mb-3" style="line-height:1.05;">{{ $siteName }}</h1>
    <p class="fs-5 nc-muted mx-auto mb-4" style="max-width:40rem;">
        {{ optional(site_information('blogdescription'))->option_value ?: 'Stories, updates and ideas — presented in a modern, multi-language experience.' }}
    </p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="#latest" class="btn btn-nc">{{ $mm ? 'ဆောင်းပါးများ ကြည့်ရန်' : 'Explore posts' }} <i class="bi bi-arrow-down"></i></a>
        <a href="{{ url('/events') }}" class="btn btn-nc-ghost">{{ $mm ? 'ပွဲများ' : 'Events' }}</a>
    </div>
</section>

@if($sliders->count() > 0)
    {{-- Featured carousel in a glass frame --}}
    <section class="container mb-5">
        <div class="nc-glass p-2">
            <div id="ncCarousel" class="carousel slide rounded-4 overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-inner rounded-4">
                    @foreach($sliders as $i => $slide)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ bp_upload_url($slide->slider_link) }}" class="d-block w-100" style="height:440px;object-fit:cover;" alt="{{ $slide->slider_name }}">
                            <div class="carousel-caption text-start" style="background:linear-gradient(0deg,rgba(10,7,17,.85),transparent);left:0;right:0;bottom:0;padding:2rem;">
                                <h2 class="nc-display fw-bold">{{ $slide->slider_name }}</h2>
                                @if($slide->slider_description)<p class="d-none d-md-block nc-muted mb-0">{{ $slide->slider_description }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($sliders->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#ncCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Previous</span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ncCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span></button>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ── Latest posts ── --}}
<section id="latest" class="container pb-5" style="scroll-margin-top:80px;">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <div class="nc-eyebrow mb-1">{{ $mm ? 'နောက်ဆုံးရ' : 'Fresh off the press' }}</div>
            <h2 class="h3 mb-0 text-light">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest posts' }}</h2>
        </div>
        <a href="{{ url('/blog') }}" class="btn btn-nc-ghost btn-sm">{{ $mm ? 'အားလုံး' : 'View all' }} <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
            @php $c = $catOf($post); $p = $localize($post); @endphp
            <div class="col-lg-3 col-sm-6">
                <article class="nc-card h-100">
                    <a href="{{ url('/'.$p->post_link) }}" class="d-block position-relative">
                        <img src="{{ bp_upload_url($p->featured_img) }}" class="nc-img" alt="{{ $p->title }}">
                        @if($c->tax_name)<span class="nc-badge position-absolute" style="top:.6rem;left:.6rem;">{{ $c->tax_name }}</span>@endif
                    </a>
                    <div class="p-3">
                        <h5 class="h6 mb-2"><a href="{{ url('/'.$p->post_link) }}" class="text-light stretched-link">{{ $p->title }}</a></h5>
                        <p class="small nc-muted mb-2">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;',' ',strip_tags($p->body)), 84) }}</p>
                        <div class="small nc-muted"><i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }} · {{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12 text-center nc-muted py-5">
                <i class="bi bi-stars fs-1"></i>
                <p class="mt-3 mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }} <a href="{{ url('/bp-admin') }}">{{ $mm ? 'အက်ဒမင်' : 'Open the admin panel' }}</a> {{ $mm ? '' : 'to create one.' }}</p>
            </div>
        @endforelse
    </div>
</section>
@stop
