@extends('theme.terra.layouts.app')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $tagline  = optional(site_information('blogdescription'))->option_value;
    $localize = function ($post) use ($mm) {
        if ($mm && isset($post->translate) && $post->translate->lang == 2) { return $post->translate; }
        return $post;
    };
    $catOf = function ($post) {
        return optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
    };
    $excerpt = function ($post, $len = 120) {
        return \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', strip_tags($post->body)), $len);
    };

    $posts = bp_post(9);
    $lead  = $posts->first();
    $rest  = $posts->slice(1)->values();

    // bp_post() returns a paginator, so take its items() before collecting.
    $topics = collect($posts->items())
        ->flatMap(fn ($p) => $p->categories)
        ->filter(fn ($c) => $c->tax_link !== 'uncategorized')
        ->unique('tax_link')->take(5)->values();
@endphp

{{-- ── Statement hero: type does the work, no imagery ── --}}
<section class="container" style="padding-top:5.5rem;padding-bottom:3rem;">
    <div class="row">
        <div class="col-lg-10">
            <div class="tr-label mb-4">{{ $mm ? 'ကြိုဆိုပါသည်' : 'welcome' }}</div>
            <h1 class="tr-display mb-3" style="font-size:clamp(2.4rem,6vw,4.5rem);line-height:1.05;">
                {{ $tagline ?: ($mm ? 'ရိုးရှင်းစွာ ဖတ်ရှုနိုင်သော ဆောင်းပါးများ။' : 'Words worth your attention, published simply.') }}
            </h1>
            <p class="fs-5 tr-muted mb-3" style="max-width:34rem;">
                {{ $mm ? $siteName.' မှ နောက်ဆုံးရ ဆောင်းပါးများ။' : 'The latest from '.$siteName.'.' }}
            </p>
            @if($topics->count())
                <div class="d-flex flex-wrap align-items-center" style="gap:.35rem 1.1rem;">
                    <span class="tr-cat">{{ $mm ? 'ခေါင်းစဉ်များ' : 'Topics' }}</span>
                    @foreach($topics as $t)
                        <a href="{{ url('/cat/'.$t->tax_link) }}" class="tr-topic tr-ul">{{ $t->tax_name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

@if($lead)
    {{-- ── Lead feature: one promoted post, the only place with hierarchy ── --}}
    @php $lc = $catOf($lead); $l = $localize($lead); @endphp
    <section class="container">
        <div class="tr-feature py-4 py-lg-5">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="{{ $lead->featured_img ? 'col-lg-7' : 'col-lg-10' }} order-2 order-lg-1">
                    <div class="tr-label mb-3">{{ $mm ? 'အထူးဖော်ပြချက်' : 'featured' }}</div>
                    @if($lc->tax_name)<div class="tr-cat mb-2">{{ $lc->tax_name }}</div>@endif
                    <h2 class="tr-feature-title mb-3"><a href="{{ url('/'.$l->post_link) }}" class="tr-ul">{{ $l->title }}</a></h2>
                    <p class="fs-5 tr-muted mb-3" style="max-width:38rem;">{{ $excerpt($l, 190) }}</p>
                    <div class="d-flex align-items-center" style="gap:1rem;">
                        <a href="{{ url('/'.$l->post_link) }}" class="btn-tr">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Read article' }} <i class="bi bi-arrow-right"></i></a>
                        <span class="tr-date">{{ $lead->created_at->translatedFormat('j M Y') }}</span>
                    </div>
                </div>
                @if($lead->featured_img)
                    <div class="col-lg-5 order-1 order-lg-2">
                        <a href="{{ url('/'.$l->post_link) }}"><img src="{{ bp_upload_url($lead->featured_img) }}" class="tr-lead-img" alt="{{ $l->title }}" fetchpriority="high"></a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ── Post index — airy, hairline-separated rows ── --}}
<section class="container pb-5 pt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="tr-label">{{ $mm ? 'နောက်ဆုံးရ' : 'more stories' }}</div>
        <a href="{{ url('/blog') }}" class="tr-cat tr-ul">{{ $mm ? 'အားလုံး' : 'All posts' }} <i class="bi bi-arrow-right"></i></a>
    </div>

    @forelse($rest as $post)
        @php $c = $catOf($post); $p = $localize($post); @endphp
        <div class="tr-row">
            <a href="{{ url('/'.$p->post_link) }}" class="tr-row-link">
                <div class="row align-items-center g-3 g-lg-4">
                    <div class="col-lg-2 col-4 order-lg-1 order-2">
                        <div class="tr-date">{{ $post->created_at->translatedFormat('j M') }}</div>
                        <div class="tr-date">{{ $post->created_at->translatedFormat('Y') }}</div>
                    </div>
                    <div class="col-lg-7 col-8 order-lg-2 order-1">
                        @if($c->tax_name)<div class="tr-cat mb-1">{{ $c->tax_name }}</div>@endif
                        <h2 class="tr-row-title mb-1">{{ $p->title }}</h2>
                        <p class="tr-muted mb-0 d-none d-sm-block">{{ $excerpt($p, 120) }}</p>
                    </div>
                    <div class="col-lg-3 d-none d-lg-block order-lg-3">
                        @if($p->featured_img)<img src="{{ bp_upload_url($p->featured_img) }}" class="tr-thumb" alt="{{ $p->title }}" loading="lazy" decoding="async">@endif
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="tr-row"><div class="py-5 text-center tr-muted">
            <p class="mb-0">{{ $mm ? 'နောက်ထပ် ဆောင်းပါးများ မရှိသေးပါ။' : 'More stories will appear here as you publish them.' }}</p>
        </div></div>
    @endforelse
</section>
@stop
