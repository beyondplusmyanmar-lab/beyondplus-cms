{{-- Overrides doeh-commerce-storefront::cart. Data: lines, ready, fulfillment_types. --}}
@extends('theme.example-theme.layouts.app')
@section('title', 'Cart')

@section('content')
    <h1 style="margin-bottom:18px;">Your cart</h1>

    @if (empty($lines))
        <div class="card muted">Your cart is empty. <a href="{{ url('/store') }}">Back to the shop →</a></div>
    @else
        @foreach ($lines as $l)
            <div class="card" style="display:flex; align-items:center; gap:14px;">
                <div style="flex:1;">
                    <strong>{{ $l['name'] }}</strong>
                    <span class="muted" style="font-size:13px;"> · qty {{ $l['qty'] }}@if ($l['price_hint']) · {{ $l['price_hint'] }}@endif</span>
                </div>
                <form method="POST" action="{{ url('/store/cart/remove') }}">
                    @csrf
                    <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                    <button class="btn" style="background:#6b7280; padding:6px 12px;" type="submit">Remove</button>
                </form>
            </div>
        @endforeach

        <form method="POST" action="{{ url('/store/checkout') }}" class="card">
            @csrf

            {{-- THE FULFILMENT SELECTOR PATTERN: render only when there is an
                 actual choice (≥2 types from the theme manifest), submit the
                 offered values VERBATIM, list pickup first (pre-checked). The
                 plugin rejects anything not offered — never coerces. --}}
            @if (count($fulfillment_types ?? []) > 1)
                @php
                    $ftCopy = [
                        'pickup'   => ['Pickup', 'Collect from the store'],
                        'dine_in'  => ['Dine in', 'Enjoy at the store'],
                        'delivery' => ['Delivery', 'Delivered to your address'],
                    ];
                @endphp
                <div class="muted" style="font-size:14px; margin-bottom:8px;">How would you like to receive it?</div>
                @foreach ($fulfillment_types as $t)
                    @php [$ftLabel, $ftDesc] = $ftCopy[$t] ?? [ucfirst($t), '']; @endphp
                    <label style="display:flex; gap:10px; align-items:baseline; padding:6px 0; cursor:pointer;">
                        <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
                        <span><strong>{{ $ftLabel }}</strong> <span class="muted" style="font-size:13px;">— {{ $ftDesc }}</span></span>
                    </label>
                @endforeach
                <div style="height:12px;"></div>
            @endif

            <label class="muted" for="phone" style="font-size:14px;">Phone (optional)</label>
            <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                   style="width:100%; padding:10px; border:1px solid var(--line); border-radius:9px; margin:6px 0 14px; font:inherit;">
            <button class="btn" type="submit" @unless($ready) disabled @endunless>Place order</button>
            @unless ($ready)<p class="muted" style="font-size:13px; margin-top:10px;">Ordering opens once DOEH Commerce is configured.</p>@endunless
        </form>
    @endif
@endsection
