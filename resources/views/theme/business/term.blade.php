{{-- Category / term listing --}}
@extends('theme.business.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            @forelse($posts as $post)
                @php
                    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                @endphp
                <article class="bz-card mb-4 p-4">
                    <h2 class="h4"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--bz-text);">{{ $post->title }}</a></h2>
                    <p class="mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 260) }}</p>
                    <a href="{{ url('/'.$post->post_link) }}" class="small fw-semibold">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Read more' }} <i class="bi bi-arrow-right"></i></a>
                </article>
            @empty
                <p class="bz-muted">{{ $mm ? 'ဤ အမျိုးအစားတွင် သတင်း မရှိသေးပါ။' : 'No posts in this category yet.' }}</p>
            @endforelse

            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
        <aside class="col-lg-4">
            @include('theme.business.sidebar')
        </aside>
    </div>
</div>
@stop
