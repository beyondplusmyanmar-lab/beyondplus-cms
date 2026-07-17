@extends('doeh-commerce-storefront::layout')
@section('title', 'DOEH Commerce — shop')

@section('content')
    <h1>Shop</h1>
    <p class="sub">A fixture of products. Add one, then place an order — DOEH prices it and returns a real order.</p>

    @unless ($ready)
        <div class="err">DOEH Commerce is not configured yet — set the merchant key in Plugins → DOEH Commerce → Settings.</div>
    @endunless

    @forelse ($products as $p)
        <div class="card row">
            <div>
                <div class="name">{{ $p['name'] }}</div>
                <div class="hint">SKU {{ $p['sku'] }}@if($p['price_hint']) · {{ $p['price_hint'] }}@endif</div>
            </div>
            <form method="POST" action="{{ url('/store/cart/add') }}">
                @csrf
                <input type="hidden" name="sku" value="{{ $p['sku'] }}">
                <button class="btn" type="submit">Add to cart</button>
            </form>
        </div>
    @empty
        <div class="card muted">No products configured. Add some in Plugins → DOEH Commerce Storefront → Settings.</div>
    @endforelse

    <p style="margin-top:16px;"><a class="plain" href="{{ url('/store/cart') }}">View cart →</a></p>
@endsection
