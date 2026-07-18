{{-- Category / term listing --}}
@extends('theme.pulse.layouts.app')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $pill = fn ($name) => 'c'.(abs(crc32((string) $name)) % 5);
@endphp
<div class="container py-5">
    <span class="pl-eyebrow mb-2">{{ $mm ? 'ကဏ္ဍ' : 'Category' }}</span>
    <h1 class="pl-display mt-2 mb-4" style="font-size:clamp(2rem,5vw,3rem);">{{ $mm ? 'ဆောင်းပါးများ' : 'In this category' }}</h1>

    <div class="row g-4">
        @forelse($posts as $post)
            @php
                $c = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
            @endphp
            <div class="col-lg-4 col-sm-6">
                <article class="pl-card">
                    <a href="{{ url('/'.$post->post_link) }}" class="d-block position-relative">
                        <img src="{{ bp_upload_url($post->featured_img) }}" class="pl-img" alt="{{ $post->title }}">
                        @if($c->tax_name)<span class="pl-pill {{ $pill($c->tax_name) }} position-absolute" style="top:.7rem;left:.7rem;">{{ $c->tax_name }}</span>@endif
                    </a>
                    <div class="p-4">
                        <h2 class="pl-display h5 mb-2"><a href="{{ url('/'.$post->post_link) }}" class="text-reset stretched-link">{{ $post->title }}</a></h2>
                        <p class="small pl-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 100) }}</p>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><p class="pl-muted">{{ $mm ? 'ဤကဏ္ဍတွင် ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts in this category yet.' }}</p></div>
        @endforelse
    </div>

    <div class="mt-5">{{ $posts->links() }}</div>
</div>
@stop
