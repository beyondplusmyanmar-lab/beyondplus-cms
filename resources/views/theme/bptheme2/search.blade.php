{{-- Search results template --}}
@extends('theme.bptheme2.layouts.app')

@section('title', 'Search')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            @include('theme.bptheme2.sidebar')
        </aside>

        <div class="col-lg-9">
            <h1 class="h3 mb-4">
                Search results@isset($query) for “{{ $query }}”@endisset
            </h1>

            @isset($posts)
                @forelse($posts as $post)
                    @php
                        if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                            $post = $post->translate;
                        }
                    @endphp
                    <article class="mb-4 pb-4 border-bottom">
                        <h2 class="h5">
                            <a href="{{ url('/'.$post->post_link) }}" class="text-dark">{{ $post->title }}</a>
                        </h2>
                        <p class="text-body mb-0">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                        </p>
                    </article>
                @empty
                    <p class="text-muted">No results found.</p>
                @endforelse
            @else
                <p class="text-muted">Enter a search term to find posts.</p>
            @endisset
        </div>
    </div>
</div>
@stop
