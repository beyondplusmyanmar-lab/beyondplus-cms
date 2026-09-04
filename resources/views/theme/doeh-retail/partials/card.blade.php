{{-- Product ticket. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool), $pickup (bool).
     Shaped like a shelf-edge label: monogram niche above, printed label below, price as the hero. --}}
<article class="rt-ticket">
    <div class="niche" aria-hidden="true">
        <span class="monogram">{{ mb_strtoupper(mb_substr($p['name'], 0, 1)) }}</span>
    </div>
    <div class="label">
        <div>
            <div class="pname">{{ $p['name'] }}</div>
            <div class="psku">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $p['sku'] }}</div>
        </div>
        <div class="price-row">
            @if ($p['price_hint'])
                <span class="rt-money price">{{ $p['price_hint'] }}</span>
            @else
                <span class="rt-muted rt-small">{{ $mm ? 'ဈေးနှုန်း checkout တွင်' : 'Price at checkout' }}</span>
            @endif
            @if ($pickup)
                <span class="rt-mark is-stock rt-muted"><span class="dot"></span>{{ $mm ? 'ဆိုင်တွင် ယူ' : 'Pickup' }}</span>
            @endif
        </div>
        <form method="POST" action="{{ url('/store/cart/add') }}">
            @csrf
            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
            <button class="rt-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to bag' }}</button>
        </form>
    </div>
</article>
