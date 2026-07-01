{{-- Category / term listing template --}}
@extends('theme.bptheme1.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            @include('theme.bptheme1.sidebar')
        </aside>

        <div class="col-lg-9">
            @forelse($posts as $post)
                @php
                    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                        $post = $post->translate;
                    }
                @endphp
                <article class="mb-4 pb-4 border-bottom">
                    <h2 class="h4">
                        <a href="{{ url('/'.$post->post_link) }}" class="text-dark">{{ $post->title }}</a>
                    </h2>
                    <div class="text-body">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 300) }}
                    </div>
                    <a href="{{ url('/'.$post->post_link) }}" class="small">Read more <i class="bi bi-arrow-right"></i></a>
                </article>
            @empty
                <p class="text-muted">No posts in this category yet.</p>
            @endforelse

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@stop
