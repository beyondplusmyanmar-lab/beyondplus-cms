@extends('theme.default.layouts.app')

@section('content')
@php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp

<section class="bp-hero text-center">
    <div class="container">
        <h1 class="display-4 mb-3">{{ $siteName }}</h1>
        <p class="lead mb-4 opacity-75">
            {{ optional(site_information('blogdescription'))->option_value ?: 'Publish and manage your content with a modern Laravel CMS.' }}
        </p>
        <a href="#featured" class="btn btn-light btn-lg px-4">
            Explore posts <i class="bi bi-arrow-down"></i>
        </a>
    </div>
</section>

<section id="featured" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="bp-section-title">Featured Posts</h2>
            <p class="text-muted mb-0">Latest updates and articles</p>
        </div>

        <div class="row g-4">
            @forelse (bp_post(8) as $post)
                @php
                    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                        $post = $post->translate;
                    }
                @endphp
                <div class="col-lg-3 col-sm-6">
                    <article class="card bp-card h-100">
                        <a href="{{ url('/'.$post->post_link) }}">
                            <img src="{{ url('/uploads/'.$post->featured_img) }}" class="card-img-top" alt="{{ $post->title }}">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ url('/'.$post->post_link) }}" class="text-dark stretched-link">{{ $post->title }}</a>
                            </h5>
                            <p class="card-text text-muted small">
                                {{ \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', strip_tags($post->body)), 90) }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 text-muted small">
                            <i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }}
                            &middot; {{ $post->created_at->diffForHumans() }}
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-journal-text fs-1"></i>
                    <p class="mt-3 mb-0">No posts yet. Sign in to the <a href="{{ url('/bp-admin') }}">admin panel</a> to create one.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@stop
