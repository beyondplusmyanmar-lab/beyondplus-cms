{{-- Blog listing — editorial river of stories --}}
@extends('theme.meridian.layouts.app')

@section('title', 'Blog')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $posts = $posts ?? bp_post(10);
@endphp
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="md-kicker mb-1">{{ $mm ? 'ဂျာနယ်' : 'The Journal' }}</div>
            <h1 class="md-serif mb-4" style="font-weight:600;letter-spacing:-.01em;">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest writing' }}</h1>

            @forelse($posts as $post)
                @php
                    $c = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                @endphp
                <article class="md-story mb-4">
                    <div class="row g-3">
                        @if($post->featured_img)
                            <div class="col-sm-4">
                                <a href="{{ url('/'.$post->post_link) }}">
                                    <img src="{{ bp_upload_url($post->featured_img) }}" class="w-100 rounded-1" style="aspect-ratio:4/3;object-fit:cover;" alt="{{ $post->title }}">
                                </a>
                            </div>
                        @endif
                        <div class="col-sm">
                            @if($c->tax_name)<a href="{{ url('/cat/'.$c->tax_link) }}" class="md-kicker">{{ $c->tax_name }}</a>@endif
                            <h2 class="md-story-title h4 mt-1 mb-2"><a href="{{ url('/'.$post->post_link) }}">{{ $post->title }}</a></h2>
                            <p class="md-dek mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}</p>
                            <div class="md-byline">{{ optional($post->creator)->name ?? 'Editorial' }} <span class="mx-1">·</span> {{ $post->created_at->translatedFormat('j M Y') }}</div>
                        </div>
                    </div>
                </article>
            @empty
                <p class="md-dek">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No stories yet.' }}</p>
            @endforelse

            @if($posts->hasPages())
                <div class="mt-4">{{ $posts->links() }}</div>
            @endif
        </div>
        <aside class="col-lg-4">@include('theme.meridian.sidebar')</aside>
    </div>
</div>
@stop
