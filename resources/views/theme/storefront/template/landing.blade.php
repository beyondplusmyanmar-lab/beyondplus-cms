{{-- Custom landing template — a full-width promo page with a shop CTA.
     Selected via post_template = "landing". --}}
@extends('theme.storefront.layouts.app')
@section('title', $post->title)
@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<section class="sf-section pt-3">
    <div class="container">
        <div class="rounded-3 text-center text-white p-5" style="background: linear-gradient(120deg, var(--sf-primary), var(--sf-accent));">
            <h1 class="fw-bold mb-2">{{ $post->title }}</h1>
            @if($post->featured_img)<img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded my-3" style="max-height:280px;" alt="{{ $post->title }}">@endif
        </div>
    </div>
</section>
<div class="container pb-4">
    <div class="sf-panel">
        <div class="bp-content">{!! bbParse($post->body) !!}</div>
        <div class="text-center mt-4"><a href="{{ url('/shop') }}" class="btn btn-primary btn-lg">{{ $mm ? 'ဈေးဝယ်ရန်' : 'Shop now' }}</a></div>
    </div>
</div>
@stop
