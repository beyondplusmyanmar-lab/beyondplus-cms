{{-- Category / term listing --}}
@extends('theme.bptheme3.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="tr-label mb-3">{{ $mm ? 'ကဏ္ဍ' : 'category' }}</div>
    <h1 class="tr-display mb-5" style="font-size:clamp(2rem,5vw,3.2rem);">{{ $mm ? 'ဆောင်းပါးများ' : 'In this category' }}</h1>

    @forelse($posts as $post)
        @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
        <div class="tr-row">
            <a href="{{ url('/'.$post->post_link) }}" class="tr-row-link">
                <div class="row align-items-center g-3">
                    <div class="col-lg-2 col-4 order-lg-1 order-2"><div class="tr-date">{{ $post->created_at->translatedFormat('j M Y') }}</div></div>
                    <div class="col-lg-10 col-8 order-lg-2 order-1">
                        <h2 class="tr-row-title mb-1">{{ $post->title }}</h2>
                        <p class="tr-muted mb-0 d-none d-sm-block">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 150) }}</p>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="tr-row"><p class="py-5 text-center tr-muted mb-0">{{ $mm ? 'ဤကဏ္ဍတွင် ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts in this category yet.' }}</p></div>
    @endforelse

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
@stop
