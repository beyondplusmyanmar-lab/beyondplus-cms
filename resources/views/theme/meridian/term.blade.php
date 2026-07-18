{{-- Category / term listing --}}
@extends('theme.meridian.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="md-kicker mb-1">{{ $mm ? 'ကဏ္ဍ' : 'Section' }}</div>
            <h1 class="md-serif mb-4" style="font-weight:600;letter-spacing:-.01em;">{{ $mm ? 'ဆောင်းပါးများ' : 'Stories in this category' }}</h1>

            @forelse($posts as $post)
                @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                <article class="md-story mb-4">
                    <h2 class="md-story-title h4"><a href="{{ url('/'.$post->post_link) }}">{{ $post->title }}</a></h2>
                    <p class="md-dek mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}</p>
                    <div class="md-byline">{{ $post->created_at->translatedFormat('j M Y') }}</div>
                </article>
            @empty
                <p class="md-dek">{{ $mm ? 'ဤကဏ္ဍတွင် ဆောင်းပါးများ မရှိသေးပါ။' : 'No stories in this category yet.' }}</p>
            @endforelse

            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
        <aside class="col-lg-4">@include('theme.meridian.sidebar')</aside>
    </div>
</div>
@stop
