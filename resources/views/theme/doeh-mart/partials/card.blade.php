{{-- Product card. Expects: $p ({sku,name,price_hint}), $ready (bool), $mm (bool), $pickup (bool).
     DOEH products carry no picture, so the thumb is a neutral box mark. --}}
<article class="mt-card">
    <div class="thumb" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
    <div class="body">
        <div class="pname" title="{{ $p['name'] }}">{{ $p['name'] }}</div>
        @if ($p['price_hint'])
            <div class="price mt-money">{{ $p['price_hint'] }}</div>
        @else
            <div class="small mt-muted">{{ $mm ? 'ဈေးနှုန်း checkout တွင်' : 'Price at checkout' }}</div>
        @endif
        <div class="meta">
            <span>{{ $p['sku'] }}</span>
            @if ($pickup)<span><i class="bi bi-shop"></i> {{ $mm ? 'ဆိုင်တွင် ယူ' : 'Pickup' }}</span>@endif
        </div>
        <form method="POST" action="{{ url('/store/cart/add') }}">
            @csrf
            <input type="hidden" name="sku" value="{{ $p['sku'] }}">
            <button class="btn btn-outline-primary btn-sm w-100" type="submit" @unless($ready) disabled @endunless>
                <i class="bi bi-cart-plus"></i> {{ $mm ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to cart' }}
            </button>
        </form>
    </div>
</article>
