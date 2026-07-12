{{-- Contact page template. Selected via post_template = "contact". --}}
@extends('theme.storefront.layouts.app')
@section('title', $post->title)
@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
    $phone = bp_option('sf_phone'); $email = bp_option('sf_email') ?: optional(site_information('admin_email'))->option_value; $address = bp_option('sf_address');
@endphp
<div class="container py-4">
    <div class="row g-3">
        <div class="col-lg-7"><div class="sf-panel"><h1 class="h4 mb-3">{{ $post->title }}</h1><div class="bp-content">{!! bbParse($post->body) !!}</div></div></div>
        <div class="col-lg-5"><div class="sf-panel">
            <h5 class="sf-panel-title mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</h5>
            <div class="d-grid gap-2 small">
                @if($phone)<p class="mb-0"><i class="bi bi-telephone text-primary me-1"></i> {{ $phone }}</p>@endif
                <p class="mb-0"><i class="bi bi-envelope text-primary me-1"></i> {{ $email ?: 'admin@example.com' }}</p>
                @if($address)<p class="mb-0"><i class="bi bi-geo-alt text-primary me-1"></i> {{ $address }}</p>@endif
            </div>
        </div></div>
    </div>
</div>
@stop
