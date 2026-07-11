{{-- Blog listing — the airy post index --}}
@extends('theme.bptheme3.layouts.app')

@section('title', 'Blog')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $posts = $posts ?? bp_post(10);
@endphp
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="tr-label mb-3">{{ $mm ? 'ဆောင်းပါးများ' : 'writing' }}</div>
    <h1 class="tr-display mb-5" style="font-size:clamp(2rem,5vw,3.2rem);">{{ $mm ? 'ဆောင်းပါးများ' : 'All posts' }}</h1>

    @forelse($posts as $post)
        @php
            $c = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
            if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
        @endphp
        <div class="tr-row">
            <a href="{{ url('/'.$post->post_link) }}" class="tr-row-link">
                <div class="row align-items-center g-3 g-lg-4">
                    <div class="col-lg-2 col-4 order-lg-1 order-2">
                        <div class="tr-date">{{ $post->created_at->translatedFormat('j M Y') }}</div>
                    </div>
                    <div class="col-lg-7 col-8 order-lg-2 order-1">
                        @if($c->tax_name)<div class="tr-cat mb-1">{{ $c->tax_name }}</div>@endif
                        <h2 class="tr-row-title mb-1">{{ $post->title }}</h2>
                        <p class="tr-muted mb-0 d-none d-sm-block">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 140) }}</p>
                    </div>
                    <div class="col-lg-3 d-none d-lg-block order-lg-3">
                        @if($post->featured_img)<img src="{{ bp_upload_url($post->featured_img) }}" class="tr-thumb" alt="{{ $post->title }}">@endif
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="tr-row"><p class="py-5 text-center tr-muted mb-0">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }}</p></div>
    @endforelse

    @if($posts->hasPages())<div class="mt-4">{{ $posts->links() }}</div>@endif
</div>
@stop
