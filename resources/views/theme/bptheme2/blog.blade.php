{{-- Blog listing --}}
@extends('theme.bptheme2.layouts.app')

@section('title', 'Blog')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $posts = $posts ?? bp_post(10);
@endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="nc-eyebrow mb-1">{{ $mm ? 'ဘလော့' : 'The blog' }}</div>
            <h1 class="h2 text-light mb-4">{{ $mm ? 'ဆောင်းပါးများ' : 'Latest writing' }}</h1>

            @forelse($posts as $post)
                @php
                    $c = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                @endphp
                <article class="nc-card mb-4">
                    <div class="row g-0">
                        @if($post->featured_img)
                            <div class="col-sm-4">
                                <a href="{{ url('/'.$post->post_link) }}"><img src="{{ bp_upload_url($post->featured_img) }}" class="w-100 h-100" style="object-fit:cover;min-height:160px;" alt="{{ $post->title }}"></a>
                            </div>
                        @endif
                        <div class="col-sm p-4">
                            @if($c->tax_name)<a href="{{ url('/cat/'.$c->tax_link) }}" class="nc-badge mb-2 d-inline-block">{{ $c->tax_name }}</a>@endif
                            <h2 class="h4"><a href="{{ url('/'.$post->post_link) }}" class="text-light">{{ $post->title }}</a></h2>
                            <p class="nc-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}</p>
                            <div class="small nc-muted"><i class="bi bi-person"></i> {{ optional($post->creator)->name ?? 'Admin' }} · {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </article>
            @empty
                <p class="nc-muted">{{ $mm ? 'ဆောင်းပါးများ မရှိသေးပါ။' : 'No posts yet.' }}</p>
            @endforelse

            @if($posts->hasPages())<div class="mt-4">{{ $posts->links() }}</div>@endif
        </div>
        <aside class="col-lg-4">@include('theme.bptheme2.sidebar')</aside>
    </div>
</div>
@stop
