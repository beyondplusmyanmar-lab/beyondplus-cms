{{-- Full-width page template (no sidebar). Selected via post_template = "fullwidth". --}}
@extends('theme.bptheme3.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <h1 class="tr-display mb-4" style="font-size:clamp(2.2rem,5.5vw,3.4rem);">{{ $post->title }}</h1>
    @if($post->featured_img)
        <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded-1 mb-4 w-100" alt="{{ $post->title }}">
    @endif
    <div class="bp-content" style="max-width:46rem;">{!! bbParse($post->body) !!}</div>
</div>
@stop
