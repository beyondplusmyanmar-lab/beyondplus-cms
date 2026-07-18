@extends('theme.nocturne.layouts.app')

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
    $excerpt = function ($post, $len = 150) {
        return \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', strip_tags($post->body)), $len);
    };

    $posts    = bp_post(9);
    $lead     = $posts->first();
    $rest     = $posts->slice(1)->values();
    $sliders  = bp_slider();

    // Unique real categories from the loaded posts — powers the topic chips.
    $topics = collect($posts)
        ->flatMap(fn ($p) => $p->categories)
        ->filter(fn ($c) => $c->tax_link !== 'uncategorized')
        ->unique('tax_link')->take(6)->values();
@endphp

<div class="container">

    {{-- ── Hero: welcome + lead-post spotlight ── --}}
    <section class="row g-4 g-lg-5 align-items-center" style="padding-top:4.5rem;padding-bottom:3rem;">
        <div class="col-lg-5">
            <div class="nc-eyebrow mb-3">{{ $mm ? 'ကြိုဆိုပါသည်' : 'Welcome' }}</div>
            <h1 class="nc-gradient-text fw-bold mb-3" style="font-size:clamp(2.4rem,5vw,3.6rem);line-height:1.04;">{{ $siteName }}</h1>
            <p class="fs-5 nc-muted mb-4" style="max-width:34rem;">
                {{ $tagline ?: ($mm ? 'ဇာတ်လမ်းများ၊ အပ်ဒိတ်များနှင့် အတွေးအမြင်များ — ခေတ်မီ၊ ဘာသာစုံ အတွေ့အကြုံဖြင့်။'
                                   : 'Stories, updates and ideas — presented in a modern, multi-language experience.') }}
            </p>
            <div class="d-flex gap-2 flex-wrap mb-4">
                <a href="#latest" class="btn btn-nc">{{ $mm ? 'ဆောင်းပါးများ ကြည့်ရန်' : 'Explore posts' }} <i class="bi bi-arrow-down"></i></a>
                <a href="{{ url('/events') }}" class="btn btn-nc-ghost">{{ $mm ? 'ပွဲများ' : 'Events' }}</a>
            </div>
            @if($topics->count())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($topics as $t)
                        <a href="{{ url('/cat/'.$t->tax_link) }}" class="nc-chip"><i class="bi bi-hash"></i>{{ $t->tax_name }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-7">
            @if($lead)
                @php $lc = $catOf($lead); $l = $localize($lead); @endphp
                <a href="{{ url('/'.$l->post_link) }}" class="nc-spotlight d-block">
                    @if($lead->featured_img)
                        <img src="{{ bp_upload_url($lead->featured_img) }}" alt="{{ $l->title }}" fetchpriority="high">
                    @else
                        <div class="nc-spotlight-empty"><i class="bi bi-stars" style="font-size:3rem;color:#c4b5fd;"></i></div>
                    @endif
                    <div class="nc-shade"></div>
                    <div class="nc-shade-body">
                        @if($lc->tax_name)<span class="nc-badge mb-2">{{ $lc->tax_name }}</span>@endif
                        <h2 class="nc-display fw-bold text-white mb-2" style="font-size:clamp(1.4rem,2.6vw,2.1rem);line-height:1.15;">{{ $l->title }}</h2>
                        <p class="d-none d-sm-block mb-2" style="color:#d3cbe6;max-width:40rem;">{{ $excerpt($l, 130) }}</p>
                        <span class="fw-semibold" style="color:#c4b5fd;">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Read article' }} <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            @else
                <div class="nc-spotlight nc-spotlight-empty">
                    <div class="text-center nc-muted">
                        <i class="bi bi-stars" style="font-size:2.5rem;color:#c4b5fd;"></i>
                        <p class="mt-3 mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet — publish one to see it featured here.' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if($sliders->count() > 0)
        {{-- ── Editor's picks: slider entries as compact glass cards ── --}}
        <section class="pb-2">
            <div class="nc-eyebrow mb-3">{{ $mm ? "အယ်ဒီတာ ရွေးချယ်မှု" : "Editor's picks" }}</div>
            <div class="row g-4">
                @foreach($sliders->take(3) as $slide)
                    <div class="col-md-4">
                        <a href="{{ $slide->slider_url ?: '#' }}" class="nc-card d-block h-100">
                            <div class="position-relative">
                                <img src="{{ bp_upload_url($slide->slider_link) }}" class="nc-img" alt="{{ $slide->slider_name }}" loading="lazy" decoding="async">
                                <span class="nc-badge position-absolute" style="top:.6rem;left:.6rem;">{{ $mm ? 'ရွေးချယ်မှု' : 'Pick' }}</span>
                            </div>
                            <div class="p-3">
                                <h3 class="h6 mb-1 text-light">{{ $slide->slider_name }}</h3>
                                @if($slide->slider_description)<p class="small nc-muted mb-0">{{ \Illuminate\Support\Str::limit($slide->slider_description, 70) }}</p>@endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ── Latest posts ── --}}
    <section id="latest" class="py-5" style="scroll-margin-top:80px;">
        <div class="d-flex align-items-end justify-content-between mb-4">
            <div>
                <div class="nc-eyebrow mb-1">{{ $mm ? 'နောက်ဆုံးရ' : 'Fresh off the press' }}</div>
                <h2 class="h3 mb-0 text-light">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest posts' }}</h2>
            </div>
            <a href="{{ url('/blog') }}" class="btn btn-nc-ghost btn-sm">{{ $mm ? 'အားလုံး' : 'View all' }} <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-4">
            @forelse($rest as $post)
                @php $c = $catOf($post); $p = $localize($post); @endphp
                <div class="col-lg-3 col-sm-6">
                    <article class="nc-card h-100">
                        <a href="{{ url('/'.$p->post_link) }}" class="d-block position-relative">
                            @if($p->featured_img)
                                <img src="{{ bp_upload_url($p->featured_img) }}" class="nc-img" alt="{{ $p->title }}" loading="lazy" decoding="async">
                            @else
                                <div class="nc-img nc-spotlight-empty"></div>
                            @endif
                            @if($c->tax_name)<span class="nc-badge position-absolute" style="top:.6rem;left:.6rem;">{{ $c->tax_name }}</span>@endif
                        </a>
                        <div class="p-3">
                            <h3 class="h6 mb-2"><a href="{{ url('/'.$p->post_link) }}" class="text-light stretched-link">{{ $p->title }}</a></h3>
                            <p class="small nc-muted mb-2">{{ $excerpt($p, 84) }}</p>
                            <div class="small nc-muted"><i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }} · {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center nc-muted py-4">
                    <p class="mb-0">{{ $mm ? 'နောက်ထပ် ဆောင်းပါးများ မရှိသေးပါ။' : 'More posts will appear here as you publish them.' }}</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ── Closing call-to-action ── --}}
    <section class="pb-5">
        <div class="nc-cta p-4 p-lg-5 text-center">
            <div class="nc-eyebrow mb-2">{{ $mm ? 'ဆက်လက် ချိတ်ဆက်ပါ' : 'Stay in the loop' }}</div>
            <h2 class="nc-display fw-bold text-white mb-2" style="font-size:clamp(1.6rem,3vw,2.4rem);">{{ $mm ? 'ဇာတ်လမ်းတိုင်း မလွတ်စေနဲ့' : 'Never miss a story' }}</h2>
            <p class="nc-muted mx-auto mb-4" style="max-width:34rem;">{{ $mm ? 'နောက်ဆုံးရ ဆောင်းပါးများ၊ ပွဲများနှင့် အပ်ဒိတ်များကို ကြည့်ရှုပါ။' : 'Browse the latest articles, upcoming events and updates from the newsroom.' }}</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ url('/blog') }}" class="btn btn-nc">{{ $mm ? 'ဆောင်းပါးအားလုံး' : 'Browse all articles' }} <i class="bi bi-arrow-right"></i></a>
                <a href="{{ url('/contact') }}" class="btn btn-nc-ghost">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</a>
            </div>
        </div>
    </section>
</div>
@stop
