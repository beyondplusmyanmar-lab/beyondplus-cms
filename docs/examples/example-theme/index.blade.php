@extends('theme.example-theme.layouts.app')
@section('title', 'Home')

@section('content')
    @php
        $siteName = trim((string) bp_option('ex_name')) ?: (optional(site_information('blogname'))->option_value ?: config('app.name'));
        // Checkbox convention: unchecked stores 'no'; '' means never-configured
        // and keeps the default-on behaviour — hence the ?: 'yes'.
        $showHero = (bp_option('ex_show_hero', 'yes') ?: 'yes') === 'yes';
        $products = function_exists('doeh_storefront_products') ? doeh_storefront_products() : [];
        // The loyalty section comes from the identity plugin's filter; empty
        // string when identity is off → the section simply doesn't render.
        $loyalty = function_exists('bp_apply_filters') ? trim(bp_apply_filters('doeh_loyalty_panel', '')) : '';
    @endphp

    @if ($showHero)
    <section class="card" style="text-align:center; padding:40px 20px;">
        <h1 style="margin-bottom:8px;">{{ $siteName }}</h1>
        <p class="muted" style="margin-bottom:18px;">Good things, ordered simply.</p>
        <a class="btn" href="{{ url('/store') }}">Browse the shop</a>
    </section>
    @endif

    @if ($loyalty !== '')
        <section class="card">{!! $loyalty !!}</section>
    @endif

    @if (! empty($products))
        <section>
            <h2 style="margin-bottom:12px;">Products</h2>
            @foreach ($products as $p)
                <div class="card" style="display:flex; align-items:center; gap:14px;">
                    <div style="flex:1;">
                        <strong>{{ $p['name'] }}</strong>
                        @if ($p['price_hint'])<span class="muted"> · {{ $p['price_hint'] }}</span>@endif
                    </div>
                    {{-- Add-to-cart is a plain form to the storefront plugin's route. --}}
                    <form method="POST" action="{{ url('/store/cart/add') }}">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                        <button class="btn" style="padding:7px 14px;" type="submit">Add</button>
                    </form>
                </div>
            @endforeach
        </section>
    @endif
@endsection
