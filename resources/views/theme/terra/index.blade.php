@extends('theme.terra.layouts.app')

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
    $posts = bp_post(9);
@endphp

{{-- ── Statement hero: type does the work, no imagery ── --}}
<section class="container" style="padding-top:5.5rem;padding-bottom:3.5rem;">
    <div class="row">
        <div class="col-lg-10">
            <div class="tr-label mb-4">{{ $mm ? 'ကြိုဆိုပါသည်' : 'welcome' }}</div>
            <h1 class="tr-display mb-3" style="font-size:clamp(2.4rem,6vw,4.5rem);line-height:1.05;">
                {{ optional(site_information('blogdescription'))->option_value ?: ($mm ? 'ရိုးရှင်းစွာ ဖတ်ရှုနိုင်သော ဆောင်းပါးများ။' : 'Words worth your attention, published simply.') }}
            </h1>
            <p class="fs-5 tr-muted mb-0" style="max-width:34rem;">
                {{ $mm ? $siteName.' မှ နောက်ဆုံးရ ဆောင်းပါးများ။' : 'The latest from '.$siteName.'.' }}
            </p>
        </div>
    </div>
</section>

{{-- ── Post index — airy, hairline-separated rows ── --}}
<section class="container pb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="tr-label">{{ $mm ? 'နောက်ဆုံးရ' : 'latest' }}</div>
        <a href="{{ url('/blog') }}" class="tr-cat tr-ul">{{ $mm ? 'အားလုံး' : 'All posts' }} <i class="bi bi-arrow-right"></i></a>
    </div>

    @forelse($posts as $post)
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
                        <p class="tr-muted mb-0 d-none d-sm-block">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;',' ',strip_tags($p->body)), 120) }}</p>
                    </div>
                    <div class="col-lg-3 d-none d-lg-block order-lg-3">
                        @if($p->featured_img)<img src="{{ bp_upload_url($p->featured_img) }}" class="tr-thumb" alt="{{ $p->title }}" loading="lazy" decoding="async">@endif
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="tr-row"><div class="py-5 text-center tr-muted">
            <p class="mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }} <a href="{{ url('/bp-admin') }}" class="tr-ul">{{ $mm ? 'အက်ဒမင်' : 'Open the admin panel' }}</a>{{ $mm ? '' : '.' }}</p>
        </div></div>
    @endforelse
</section>
@stop
