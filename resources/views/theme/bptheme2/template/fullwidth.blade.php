{{-- Full-width page template (no sidebar). Selected via post_template = "fullwidth". --}}
@extends('theme.bptheme2.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <h1 class="display-6 text-light mb-4">{{ $post->title }}</h1>
    @if($post->featured_img)
        <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded-4 mb-4 w-100" alt="{{ $post->title }}">
    @endif
    <div class="bp-content">{!! bbParse($post->body) !!}</div>
</div>
@stop
