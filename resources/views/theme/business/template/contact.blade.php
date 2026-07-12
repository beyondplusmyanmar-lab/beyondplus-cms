{{-- Contact page template. Selected via post_template = "contact". --}}
@extends('theme.business.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
    $phone   = bp_option('biz_phone');
    $email   = bp_option('biz_email') ?: optional(site_information('admin_email'))->option_value;
    $address = bp_option('biz_address');
    $hours   = bp_option('biz_hours');
@endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <h1 class="mb-4">{{ $post->title }}</h1>
            <div class="bp-content">
                {!! bbParse($post->body) !!}
            </div>
        </div>
        <div class="col-lg-5">
            <div class="bz-card p-4">
                <h5 class="h6 mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</h5>
                <div class="d-grid gap-3">
                    @if($phone)<p class="mb-0"><i class="bi bi-telephone text-primary me-1"></i> <a class="bz-muted" href="tel:{{ str_replace([' ', '-', '(', ')'], '', $phone) }}">{{ $phone }}</a></p>@endif
                    <p class="mb-0"><i class="bi bi-envelope text-primary me-1"></i> <a class="bz-muted" href="mailto:{{ $email }}">{{ $email ?: 'admin@example.com' }}</a></p>
                    @if($address)<p class="mb-0"><i class="bi bi-geo-alt text-primary me-1"></i> <span class="bz-muted">{{ $address }}</span></p>@endif
                    @if($hours)<p class="mb-0"><i class="bi bi-clock text-primary me-1"></i> <span class="bz-muted">{!! nl2br(e($hours)) !!}</span></p>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
