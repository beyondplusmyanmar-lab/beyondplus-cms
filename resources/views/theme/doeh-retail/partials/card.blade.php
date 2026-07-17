{{-- Product card. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool), $pickup (bool). --}}
<div class="rt-card rt-prod">
    <div class="swatch" aria-hidden="true">{{ mb_strtoupper(mb_substr($p['name'], 0, 1)) }}</div>
    <div class="body">
        <div>
            <div class="pname">{{ $p['name'] }}</div>
            <div class="psku">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $p['sku'] }}</div>
        </div>
        <div class="prow">
            @if ($p['price_hint'])<span class="rt-price price">{{ $p['price_hint'] }}</span>@else<span></span>@endif
            @if ($pickup)<span class="rt-chip pickup">{{ $mm ? 'ဆိုင်တွင် ယူ' : 'Pickup' }}</span>@endif
        </div>
        <form method="POST" action="{{ url('/store/cart/add') }}">
            @csrf
            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
            <button class="rt-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to bag' }}</button>
        </form>
    </div>
</div>
