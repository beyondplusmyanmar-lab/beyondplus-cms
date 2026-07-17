{{-- One service row. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool). --}}
<div class="sv-svc">
    <div class="s-mid">
        <div class="s-name">{{ $p['name'] }}</div>
        <div class="s-note">
            <span class="sv-chip">{{ $mm ? 'ချိန်းဆို၍' : 'By appointment' }}</span>
            <span style="margin-left:8px;">{{ $mm ? 'ကုဒ်' : 'Ref' }} {{ $p['sku'] }}</span>
        </div>
    </div>
    @if ($p['price_hint'])<span class="sv-price s-price">{{ $p['price_hint'] }}</span>@endif
    <form method="POST" action="{{ url('/store/cart/add') }}">
        @csrf
        <input type="hidden" name="sku" value="{{ $p['sku'] }}">
        <button class="sv-btn sm" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'မှာယူရန်' : 'Book' }}</button>
    </form>
</div>
