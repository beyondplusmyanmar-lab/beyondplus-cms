{{-- Blog listing template --}}
@extends('theme.default.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            @include('theme.default.sidebar')
        </aside>

        <div class="col-lg-9">
            <h1 class="h3 mb-4">Blog</h1>
            @php $posts = $posts ?? bp_post(10); @endphp
            @forelse($posts as $post)
                @php
                    $postCategory = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                        $post = $post->translate;
                    }
                @endphp
                <article class="mb-4 pb-4 border-bottom">
                    <h2 class="h4">
                        <a href="{{ url('/'.$post->post_link) }}" class="text-dark">{{ $post->title }}</a>
                    </h2>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }}
                        &middot; {{ $post->created_at->diffForHumans() }}
                        @if($postCategory->tax_name)
                            &middot; <a href="{{ url('/cat/'.$postCategory->tax_link) }}" class="badge text-white text-decoration-none" style="background:var(--bp-accent);">{{ $postCategory->tax_name }}</a>
                        @endif
                    </p>
                    <div class="text-body">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 300) }}
                    </div>
                    <a href="{{ url('/'.$post->post_link) }}" class="small">Read more <i class="bi bi-arrow-right"></i></a>
                </article>
            @empty
                <p class="text-muted">No posts yet.</p>
            @endforelse

            @if($posts->hasPages())
                <div class="mt-4">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@stop
