{{-- Contact page (/contact) — reuses the homepage contact section for consistency. --}}
@extends('theme.business.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'ဆက်သွယ်ရန်' : 'Contact')

@section('content')
    @include('theme.business.sections.contact')
@stop
