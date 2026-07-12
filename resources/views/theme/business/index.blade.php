{{--
    Business theme — homepage.
    Sections are reusable partials in sections/. Each guards its own data and
    hides itself when empty. POS-driven sections (featured products, promotions,
    store locations) read from filter hooks and appear only when a commerce
    plugin registers them — see sections/featured-products.blade.php.
--}}
@extends('theme.business.layouts.app')

@section('content')
    @include('theme.business.sections.hero')
    @include('theme.business.sections.services')
    @include('theme.business.sections.featured-products')
    @include('theme.business.sections.about')
    @include('theme.business.sections.why-choose-us')
    @include('theme.business.sections.statistics')
    @include('theme.business.sections.testimonials')
    @include('theme.business.sections.news')
    @include('theme.business.sections.faq')
    @include('theme.business.sections.contact')
@stop
