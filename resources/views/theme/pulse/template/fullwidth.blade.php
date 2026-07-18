{{-- Full-width page template (no sidebar). Selected via post_template = "fullwidth". --}}
@extends('theme.pulse.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <h1 class="pl-display mb-4" style="font-size:clamp(2.2rem,5.5vw,3.4rem);">{{ $post->title }}</h1>
    @if($post->featured_img)
        <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid mb-4 w-100" style="border-radius:24px;" alt="{{ $post->title }}">
    @endif
    <div class="bp-content">{!! bbParse($post->body) !!}</div>
</div>
@stop
