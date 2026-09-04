{{-- Product card. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool).
     Two materials: a paper body, then a tinted foot carrying the price and the action. --}}
<article class="card product">
    <div class="body">
        <div class="pname">{{ $p['name'] }}</div>
        <div class="psku">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $p['sku'] }}</div>
    </div>
    <div class="foot">
        @if ($p['price_hint'])
            <span class="money price">{{ $p['price_hint'] }}</span>
        @else
            <span class="muted small">{{ $mm ? 'ဈေးနှုန်း checkout တွင်' : 'Price at checkout' }}</span>
        @endif
        <form method="POST" action="{{ url('/store/cart/add') }}">
            @csrf
            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
            <button class="btn big" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to cart' }}</button>
        </form>
    </div>
</article>
