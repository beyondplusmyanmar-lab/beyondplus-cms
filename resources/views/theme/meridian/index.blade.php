@extends('theme.meridian.layouts.app')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $localize = function ($post) use ($mm) {
        if ($mm && isset($post->translate) && $post->translate->lang == 2) { return $post->translate; }
        return $post;
    };
    $catOf = function ($post) {
        return optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
    };

    $posts = bp_post(9);
    $lead = $posts->first();
    $rest = $posts->slice(1);
    $sliders = bp_slider();
@endphp

<div class="container">

    @if($lead)
        {{-- ── Lead story: the signature editorial split ── --}}
        <section class="md-lead py-4 py-lg-5">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-7">
                    @if($lead->featured_img)
                        <a href="{{ url('/'.$lead->post_link) }}">
                            <img src="{{ bp_upload_url($lead->featured_img) }}" class="md-lead-img rounded-1" alt="{{ $lead->title }}">
                        </a>
                    @endif
                </div>
                <div class="col-lg-5">
                    @php $lc = $catOf($lead); $l = $localize($lead); @endphp
                    @if($lc->tax_name)
                        <a href="{{ url('/cat/'.$lc->tax_link) }}" class="md-kicker">{{ $lc->tax_name }}</a>
                    @else
                        <span class="md-kicker">{{ $mm ? 'အဓိကသတင်း' : 'Top story' }}</span>
                    @endif
                    <h1 class="md-lead-title mt-2 mb-3">
                        <a href="{{ url('/'.$l->post_link) }}">{{ $l->title }}</a>
                    </h1>
                    <p class="md-dek fs-5 mb-3">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;',' ',strip_tags($l->body)), 180) }}</p>
                    <div class="md-byline mb-3">
                        {{ $mm ? 'ရေးသားသူ' : 'By' }} {{ optional($lead->creator)->name ?? 'Editorial' }}
                        <span class="mx-1">·</span> {{ $lead->created_at->translatedFormat('j M Y') }}
                    </div>
                    <a href="{{ url('/'.$l->post_link) }}" class="btn btn-md-ghost">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Continue reading' }}</a>
                </div>
            </div>
        </section>
    @endif

    {{-- ── Latest section ── --}}
    <section class="py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="md-serif h3 mb-0">{{ $mm ? 'နောက်ဆုံးရ ဆောင်းပါးများ' : 'Latest stories' }}</h2>
            <a href="{{ url('/blog') }}" class="md-kicker md-kicker--muted">{{ $mm ? 'အားလုံးကြည့်ရန်' : 'View all' }} <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-4 g-lg-5">
            @forelse($rest as $post)
                @php $c = $catOf($post); $p = $localize($post); @endphp
                <div class="col-md-6 col-lg-4">
                    <article class="md-story h-100">
                        @if($p->featured_img)
                            <a href="{{ url('/'.$p->post_link) }}">
                                <img src="{{ bp_upload_url($p->featured_img) }}" class="md-story-img rounded-1 mb-3" alt="{{ $p->title }}" loading="lazy" decoding="async">
                            </a>
                        @endif
                        @if($c->tax_name)
                            <a href="{{ url('/cat/'.$c->tax_link) }}" class="md-kicker">{{ $c->tax_name }}</a>
                        @endif
                        <h3 class="md-story-title h5 mt-1 mb-2"><a href="{{ url('/'.$p->post_link) }}">{{ $p->title }}</a></h3>
                        <p class="md-dek small mb-2">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;',' ',strip_tags($p->body)), 110) }}</p>
                        <div class="md-byline">{{ optional($post->creator)->name ?? 'Editorial' }} <span class="mx-1">·</span> {{ $post->created_at->translatedFormat('j M Y') }}</div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-journal-text fs-1"></i>
                    <p class="mt-3 mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No stories yet.' }}
                        <a href="{{ url('/bp-admin') }}">{{ $mm ? 'အက်ဒမင်' : 'Open the admin panel' }}</a> {{ $mm ? '' : 'to publish one.' }}</p>
                </div>
            @endforelse
        </div>
    </section>

    @if($sliders->count() > 0)
        {{-- Editor's picks, sourced from the slider entries --}}
        <section class="pb-5">
            <hr class="md-hairline mb-4">
            <div class="md-kicker mb-3">{{ $mm ? 'အယ်ဒီတာ ရွေးချယ်မှု' : "Editor's picks" }}</div>
            <div class="row g-4">
                @foreach($sliders->take(3) as $slide)
                    <div class="col-md-4">
                        <a href="{{ $slide->slider_url ?: '#' }}" class="d-block position-relative">
                            <img src="{{ bp_upload_url($slide->slider_link) }}" class="w-100 rounded-1" style="aspect-ratio:16/9;object-fit:cover;" alt="{{ $slide->slider_name }}" loading="lazy" decoding="async">
                            <div class="mt-2"><span class="md-story-title h6">{{ $slide->slider_name }}</span></div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@stop
