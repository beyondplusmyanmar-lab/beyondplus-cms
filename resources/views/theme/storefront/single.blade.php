@extends('theme.storefront.layouts.app')
@section('title', $post->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->body), 155))
@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $cats = $post->categories;
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-4">
    <div class="row g-3">
        <article class="col-lg-8">
            <div class="sf-panel">
                <h1 class="h3 mb-2">{{ $post->title }}</h1>
                @if($cats->count())<div class="mb-3">@foreach($cats as $cat)<a href="{{ url('/cat/'.$cat->tax_link) }}" class="badge text-white text-decoration-none me-1" style="background:var(--sf-primary);">{{ $cat->tax_name }}</a>@endforeach</div>@endif
                @if($post->featured_img)<img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded mb-3 w-100" alt="{{ $post->title }}">@endif
                <div class="bp-content">{!! bbParse($post->body) !!}</div>
            </div>
        </article>
        <aside class="col-lg-4">@include('theme.storefront.sidebar')</aside>
    </div>
</div>
@stop
