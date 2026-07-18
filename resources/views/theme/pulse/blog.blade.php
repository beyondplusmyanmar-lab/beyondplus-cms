{{-- Blog listing --}}
@extends('theme.pulse.layouts.app')

@section('title', 'Blog')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $pill = fn ($name) => 'c'.(abs(crc32((string) $name)) % 5);
    $posts = $posts ?? bp_post(9);
@endphp
<div class="container py-5">
    <div class="row g-4 mb-2">
        <div class="col">
            <span class="pl-eyebrow mb-2">{{ $mm ? 'ဘလော့' : 'The blog' }}</span>
            <h1 class="pl-display mt-2 mb-0" style="font-size:clamp(2rem,5vw,3rem);">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest writing' }}</h1>
        </div>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
            @php
                $c = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
            @endphp
            <div class="col-lg-4 col-sm-6">
                <article class="pl-card">
                    <a href="{{ url('/'.$post->post_link) }}" class="d-block position-relative">
                        <img src="{{ bp_upload_url($post->featured_img) }}" class="pl-img" alt="{{ $post->title }}">
                        @if($c->tax_name)<span class="pl-pill {{ $pill($c->tax_name) }} position-absolute" style="top:.7rem;left:.7rem;">{{ $c->tax_name }}</span>@endif
                    </a>
                    <div class="p-4">
                        <h2 class="pl-display h5 mb-2" style="line-height:1.3;"><a href="{{ url('/'.$post->post_link) }}" class="text-reset stretched-link">{{ $post->title }}</a></h2>
                        <p class="small pl-muted mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 110) }}</p>
                        <div class="small pl-muted"><i class="bi bi-person-circle text-primary"></i> {{ optional($post->creator)->name ?? 'Admin' }} · {{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </article>
            </div>
        @empty
            <p class="pl-muted">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }}</p>
        @endforelse
    </div>

    @if($posts->hasPages())<div class="mt-5">{{ $posts->links() }}</div>@endif
</div>
@stop
