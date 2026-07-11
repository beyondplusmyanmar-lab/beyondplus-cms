{{-- Contact page template. Selected via post_template = "contact". --}}
@extends('theme.bptheme1.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-7 md-article">
            <h1 class="md-article-title display-6 mb-4">{{ $post->title }}</h1>
            <div class="bp-content">{!! bbParse($post->body) !!}</div>
        </div>
        <div class="col-lg-5">
            <div class="p-4" style="border:1px solid var(--md-rule);background:#fff;">
                <div class="md-aside-title">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</div>
                <p class="mb-2"><i class="bi bi-envelope text-primary"></i>
                    {{ optional(site_information('admin_email'))->option_value ?: 'admin@example.com' }}</p>
                <p class="mb-0 md-dek"><i class="bi bi-geo-alt text-primary"></i>
                    {{ $mm ? 'လိပ်စာကို အက်ဒမင် တွင် ထည့်သွင်းပါ။' : 'Add your address in the admin settings.' }}</p>
            </div>
        </div>
    </div>
</div>
@stop
