{{-- Contact page template. Selected via post_template = "contact". --}}
@extends('theme.nocturne.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <h1 class="h2 text-light mb-4">{{ $post->title }}</h1>
            <div class="bp-content">{!! bbParse($post->body) !!}</div>
        </div>
        <div class="col-lg-5">
            <div class="nc-glass p-4">
                <div class="nc-eyebrow mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</div>
                <p class="mb-2 text-light"><i class="bi bi-envelope text-primary"></i>
                    {{ optional(site_information('admin_email'))->option_value ?: 'admin@example.com' }}</p>
                <p class="mb-0 nc-muted"><i class="bi bi-geo-alt text-primary"></i>
                    {{ $mm ? 'လိပ်စာကို အက်ဒမင် တွင် ထည့်သွင်းပါ။' : 'Add your address in the admin settings.' }}</p>
            </div>
        </div>
    </div>
</div>
@stop
