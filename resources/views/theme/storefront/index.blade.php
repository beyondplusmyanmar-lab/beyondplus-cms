{{--
    Storefront homepage — product-first. The product/promotion grids fill from
    the Commerce plugin's hooks (same contract the Business theme uses); when
    Commerce is inactive those sections simply hide. The Storefront plugin seeds
    the Shop/Cart menu + a landing page on activation.
--}}
@extends('theme.storefront.layouts.app')

@section('content')
    @include('theme.storefront.sections.banner')
    @include('theme.storefront.sections.categories')
    @include('theme.storefront.sections.featured-products')
    @include('theme.storefront.sections.promotions')
@stop
