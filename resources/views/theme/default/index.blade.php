@extends('theme.default.layouts.app')

@section('content')
@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $sliders = bp_slider();
@endphp

@if($sliders->count() > 0)
    <section class="bp-slider">
        <div id="bpCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($sliders as $i => $slide)
                    <button type="button" data-bs-target="#bpCarousel" data-bs-slide-to="{{ $i }}"
                            class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($sliders as $i => $slide)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <img src="{{ bp_upload_url($slide->slider_link) }}" class="d-block w-100 bp-slide-img" alt="{{ $slide->slider_name }}">
                        <div class="carousel-caption">
                            <h2 class="fw-bold">{{ $slide->slider_name }}</h2>
                            @if($slide->slider_description)
                                <p class="d-none d-md-block">{{ $slide->slider_description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($sliders->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#bpCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#bpCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
                </button>
            @endif
        </div>
    </section>
@else
    <section class="bp-hero">
        <div class="container">
            <h1 class="display-4 mb-3" style="max-width:16ch;">{{ $siteName }}</h1>
            <p class="lead mb-4" style="max-width:52ch;">
                {{ optional(site_information('blogdescription'))->option_value ?: (app()->getLocale() === 'mm' ? 'သတင်းများနှင့် အကြောင်းအရာများ ဤနေရာတွင် ဖတ်ရှုပါ။' : 'News, updates and everything else worth reading.') }}
            </p>
            <a href="#featured" class="btn btn-primary btn-lg px-4">
                {{ app()->getLocale() === 'mm' ? 'ပို့စ်များ ဖတ်ရန်' : 'Read the latest' }}
            </a>
        </div>
    </section>
@endif

<section id="featured" class="py-5">
    <div class="container">
        <div class="bp-sec-head">
            <h2 class="bp-section-title">{{ app()->getLocale() === 'mm' ? 'နောက်ဆုံး ပို့စ်များ' : 'Latest posts' }}</h2>
            <a href="{{ url('/blog') }}">{{ app()->getLocale() === 'mm' ? 'အားလုံး ဖတ်ရန်' : 'Browse all posts' }}</a>
        </div>
        <hr class="bp-rule">

        <div class="row g-4">
            @forelse (bp_post(8) as $post)
                @php
                    $postCategory = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                        $post = $post->translate;
                    }
                @endphp
                <div class="col-lg-3 col-sm-6">
                    <article class="card bp-card h-100">
                        <div class="position-relative">
                            <a href="{{ url('/'.$post->post_link) }}">
                                <img src="{{ bp_upload_url($post->featured_img) }}" class="card-img-top" alt="{{ $post->title }}">
                            </a>
                            @if($postCategory->tax_name)
                                <a href="{{ url('/cat/'.$postCategory->tax_link) }}" class="badge bp-cat-badge position-absolute text-decoration-none" style="top:.6rem; left:.6rem; z-index:2;">{{ $postCategory->tax_name }}</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ url('/'.$post->post_link) }}" class="text-dark stretched-link">{{ $post->title }}</a>
                            </h5>
                            <p class="card-text text-muted small">
                                {{ \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', strip_tags($post->body)), 90) }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 text-muted small">
                            <span>{{ optional($post->creator)->name ?? 'Admin' }}</span>
                            <span class="ms-2">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-journal-text fs-1"></i>
                    <p class="mt-3 mb-0">{{ app()->getLocale() === 'mm' ? 'ပို့စ် မရှိသေးပါ။' : 'Nothing published yet.' }} <a href="{{ url('/bp-admin') }}">{{ app()->getLocale() === 'mm' ? 'ပထမဆုံး ပို့စ် ရေးရန်' : 'Write the first post' }}</a></p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@stop
