@extends('theme.bptheme4.layouts.app')

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
    $pill = fn ($name) => 'c'.(abs(crc32((string) $name)) % 5);
    $posts = bp_post(9);
    $lead = $posts->first();
    $rest = $posts->slice(1);
@endphp

{{-- ── Gradient-mesh hero ── --}}
<section class="container mt-4">
    <div class="pl-hero p-4 p-lg-5">
        <div class="row align-items-center g-4 position-relative">
            <div class="col-lg-7">
                <span class="pl-eyebrow mb-3">{{ $mm ? 'ကြိုဆိုပါသည်' : 'Welcome' }}</span>
                <h1 class="pl-display mt-3 mb-3" style="font-size:clamp(2.3rem,5.5vw,4rem);line-height:1.05;">
                    <span class="pl-grad-text">{{ $siteName }}</span>
                </h1>
                <p class="fs-5 pl-muted mb-4" style="max-width:32rem;">
                    {{ optional(site_information('blogdescription'))->option_value ?: 'Stories, updates and ideas — published boldly and built for every screen.' }}
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#latest" class="btn btn-pl">{{ $mm ? 'ဆောင်းပါးများ ကြည့်ရန်' : 'Explore posts' }} <i class="bi bi-arrow-down"></i></a>
                    <a href="{{ url('/events') }}" class="btn btn-pl-soft">{{ $mm ? 'ပွဲများ' : 'Events' }}</a>
                </div>
            </div>
            @if($lead && $lead->featured_img)
                <div class="col-lg-5">
                    @php $l = $localize($lead); @endphp
                    <a href="{{ url('/'.$l->post_link) }}" class="d-block pl-card">
                        <img src="{{ bp_upload_url($l->featured_img) }}" class="pl-img" alt="{{ $l->title }}" style="aspect-ratio:4/3;">
                        <div class="p-3">
                            <span class="pl-eyebrow mb-2">{{ $mm ? 'အဓိကသတင်း' : 'Featured' }}</span>
                            <div class="pl-display mt-2" style="font-size:1.1rem;line-height:1.25;">{{ $l->title }}</div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ── Latest posts ── --}}
<section id="latest" class="container py-5" style="scroll-margin-top:80px;">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <span class="pl-eyebrow mb-2">{{ $mm ? 'နောက်ဆုံးရ' : 'Fresh' }}</span>
            <h2 class="pl-display mt-2 mb-0 h3">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest posts' }}</h2>
        </div>
        <a href="{{ url('/blog') }}" class="btn btn-pl-soft btn-sm">{{ $mm ? 'အားလုံး' : 'View all' }} <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        @forelse($rest as $post)
            @php $c = $catOf($post); $p = $localize($post); @endphp
            <div class="col-lg-4 col-sm-6">
                <article class="pl-card">
                    <a href="{{ url('/'.$p->post_link) }}" class="d-block position-relative">
                        <img src="{{ bp_upload_url($p->featured_img) }}" class="pl-img" alt="{{ $p->title }}">
                        @if($c->tax_name)<span class="pl-pill {{ $pill($c->tax_name) }} position-absolute" style="top:.7rem;left:.7rem;">{{ $c->tax_name }}</span>@endif
                    </a>
                    <div class="p-4">
                        <h3 class="pl-display h6 mb-2" style="line-height:1.3;"><a href="{{ url('/'.$p->post_link) }}" class="text-reset stretched-link">{{ $p->title }}</a></h3>
                        <p class="small pl-muted mb-3">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;',' ',strip_tags($p->body)), 92) }}</p>
                        <div class="small pl-muted d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle text-primary"></i> {{ optional($post->creator)->name ?? 'Admin' }}
                            <span>·</span> {{ $post->created_at->diffForHumans() }}
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12 text-center pl-muted py-5">
                <i class="bi bi-emoji-smile fs-1 text-primary"></i>
                <p class="mt-3 mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }} <a href="{{ url('/bp-admin') }}">{{ $mm ? 'အက်ဒမင်' : 'Open the admin panel' }}</a> {{ $mm ? '' : 'to create one.' }}</p>
            </div>
        @endforelse
    </div>
</section>
@stop
