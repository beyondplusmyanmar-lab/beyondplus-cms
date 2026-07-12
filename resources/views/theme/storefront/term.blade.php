@extends('theme.storefront.layouts.app')
@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-4">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="sf-panel">
                @forelse($posts as $post)
                    @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                    <article class="mb-4 pb-3 border-bottom">
                        <h2 class="h5"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--sf-text);">{{ $post->title }}</a></h2>
                        <p class="mb-1">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}</p>
                        <a href="{{ url('/'.$post->post_link) }}" class="small fw-semibold">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Read more' }} <i class="bi bi-arrow-right"></i></a>
                    </article>
                @empty
                    <p class="sf-muted">{{ $mm ? 'ဤ အမျိုးအစားတွင် မရှိသေးပါ။' : 'Nothing in this category yet.' }}</p>
                @endforelse
                <div class="mt-3">{{ $posts->links() }}</div>
            </div>
        </div>
        <aside class="col-lg-4">@include('theme.storefront.sidebar')</aside>
    </div>
</div>
@stop
