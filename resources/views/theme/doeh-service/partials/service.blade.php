{{-- One service row. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool). --}}
<div class="sv-svc">
    <div class="s-mid">
        <div class="s-name">{{ $p['name'] }}</div>
        <div class="s-note">
            <span class="sv-chip">{{ $mm ? 'ချိန်းဆို၍' : 'By appointment' }}</span>
            <span>{{ $mm ? 'ကုဒ်' : 'Ref' }} {{ $p['sku'] }}</span>
        </div>
    </div>
    @if ($p['price_hint'])
        <span class="sv-price s-price">{{ $p['price_hint'] }}</span>
    @else
        <span class="sv-muted sv-small" style="white-space:nowrap;">{{ $mm ? 'ဈေးနှုန်း ချိန်းဆိုချိန်တွင်' : 'Priced at booking' }}</span>
    @endif
    <form method="POST" action="{{ url('/store/cart/add') }}">
        @csrf
        <input type="hidden" name="sku" value="{{ $p['sku'] }}">
        <button class="sv-btn sm" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'မှာယူရန်' : 'Book' }}</button>
    </form>
</div>
