@extends('doeh-commerce-storefront::layout')
@section('title', 'DOEH Commerce — cart')

@section('content')
    <h1>Your cart</h1>
    <p class="sub">DOEH computes the real total at checkout — the prices here are only hints.</p>

    @if (empty($lines))
        <div class="card muted">Your cart is empty. <a class="plain" href="{{ url('/store') }}">Back to the shop →</a></div>
    @else
        <div class="card">
            <table>
                @foreach ($lines as $l)
                    <tr>
                        <td>
                            <div class="name">{{ $l['name'] }}</div>
                            <div class="hint">SKU {{ $l['sku'] }} · qty {{ $l['qty'] }}@if($l['price_hint']) · {{ $l['price_hint'] }}@endif</div>
                        </td>
                        <td class="r">
                            <form method="POST" action="{{ url('/store/cart/remove') }}">
                                @csrf
                                <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                <button class="btn sec" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <form method="POST" action="{{ url('/store/checkout') }}" class="card">
            @csrf
            @if (count($fulfillment_types ?? []) > 1)
                <fieldset style="border:1px solid var(--line); border-radius:9px; padding:12px 14px; margin:0 0 14px;">
                    <legend class="hint" style="padding:0 6px;">How would you like to receive it?</legend>
                    @foreach ($fulfillment_types as $t)
                        @php [$label, $desc] = doeh_storefront_fulfillment_label($t); @endphp
                        <label style="display:flex; gap:10px; align-items:baseline; padding:6px 2px; cursor:pointer;">
                            <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
                            <span><span class="name">{{ $label }}</span>@if($desc) <span class="hint">— {{ $desc }}</span>@endif</span>
                        </label>
                    @endforeach
                </fieldset>
            @endif
            <label class="hint" for="phone">Customer phone (optional)</label>
            <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                   style="width:100%; padding:10px; border:1px solid var(--line); border-radius:9px; margin:6px 0 14px; font:inherit;">
            <button class="btn big" type="submit" @unless($ready) disabled @endunless>Place order</button>
            @unless ($ready)
                <p class="hint" style="margin:10px 0 0;">Ordering is disabled until DOEH Commerce is configured.</p>
            @endunless
        </form>

        <p><a class="plain" href="{{ url('/store') }}">← Keep shopping</a></p>
    @endif
@endsection
