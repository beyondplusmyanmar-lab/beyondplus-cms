{{-- Full-width page (no sidebar). Selected via post_template = "fullwidth". --}}
@extends('theme.storefront.layouts.app')
@section('title', $post->title)
@section('content')
@php if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
<div class="container py-4">
    <div class="sf-panel">
        <h1 class="h3 mb-3">{{ $post->title }}</h1>
        @if($post->featured_img)<img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded mb-3 w-100" alt="{{ $post->title }}">@endif
        <div class="bp-content">{!! bbParse($post->body) !!}</div>
    </div>
</div>
@stop
