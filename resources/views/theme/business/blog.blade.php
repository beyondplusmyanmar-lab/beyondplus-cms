{{-- Blog / News listing --}}
@extends('theme.business.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'သတင်း' : 'News')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <h1 class="h3 mb-4">{{ $mm ? 'သတင်းများ' : 'News & Articles' }}</h1>
    <div class="row g-4">
        <div class="col-lg-8">
            @php $posts = $posts ?? bp_post(10); @endphp
            @forelse($posts as $post)
                @php
                    $postCategory = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                @endphp
                <article class="bz-card mb-4 p-4">
                    @if($postCategory->tax_name)
                        <a href="{{ url('/cat/'.$postCategory->tax_link) }}" class="badge text-white text-decoration-none mb-2" style="background:var(--bz-primary);">{{ $postCategory->tax_name }}</a>
                    @endif
                    <h2 class="h4"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--bz-text);">{{ $post->title }}</a></h2>
                    <p class="bz-muted small mb-2">
                        <i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }}
                        &middot; {{ $post->created_at->diffForHumans() }}
                    </p>
                    <p class="mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 260) }}</p>
                    <a href="{{ url('/'.$post->post_link) }}" class="small fw-semibold">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Read more' }} <i class="bi bi-arrow-right"></i></a>
                </article>
            @empty
                <p class="bz-muted">{{ $mm ? 'သတင်း မရှိသေးပါ။' : 'No posts yet.' }}</p>
            @endforelse

            @if($posts->hasPages())
                <div class="mt-4">{{ $posts->links() }}</div>
            @endif
        </div>
        <aside class="col-lg-4">
            @include('theme.business.sidebar')
        </aside>
    </div>
</div>
@stop
