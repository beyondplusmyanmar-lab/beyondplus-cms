{{-- Overrides doeh-commerce-storefront::shop. Data: products, cart, ready. --}}
@extends('theme.example-theme.layouts.app')
@section('title', 'Shop')

@section('content')
    <h1 style="margin-bottom:6px;">Shop</h1>
    <p class="muted" style="margin-bottom:18px;">DOEH computes the real total at checkout — prices here are hints.</p>

    @foreach ($products as $p)
        <div class="card" style="display:flex; align-items:center; gap:14px;">
            <div style="flex:1;">
                <strong>{{ $p['name'] }}</strong>
                <div class="muted" style="font-size:13px;">SKU {{ $p['sku'] }}@if ($p['price_hint']) · {{ $p['price_hint'] }}@endif</div>
            </div>
            <form method="POST" action="{{ url('/store/cart/add') }}">
                @csrf
                <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                <button class="btn" style="padding:7px 14px;" type="submit">Add</button>
            </form>
        </div>
    @endforeach

    <p><a href="{{ url('/store/cart') }}">Go to cart →</a></p>
@endsection
