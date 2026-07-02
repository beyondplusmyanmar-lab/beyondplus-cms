{{-- Contact page template. Selected via post_template = "contact". --}}
@extends('theme.default.layouts.app')

@section('title', $post->title)

@section('content')
@php
    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
        $post = $post->translate;
    }
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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="bp-section-title mb-3">Get in touch</h5>
                    <p class="mb-2"><i class="bi bi-envelope text-primary"></i>
                        {{ optional(site_information('admin_email'))->option_value ?: 'admin@example.com' }}</p>
                    <p class="mb-0 text-muted"><i class="bi bi-geo-alt text-primary"></i>
                        Add your address in the admin settings.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
