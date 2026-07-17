{{-- Overrides doeh-commerce-storefront::cart — "the check". Data: lines, ready. --}}
@extends('theme.doeh-restaurant.layouts.app')
@section('title', 'Your order')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <header style="text-align:center; margin-bottom:24px;">
        <div class="r-eyebrow" style="margin-bottom:10px;">{{ $mm ? 'သင့်အော်ဒါ' : 'Your order' }}</div>
        <h1 style="font-size:34px;">{{ $mm ? 'အော်ဒါ' : 'The order' }}</h1>
    </header>

    @if (empty($lines))
        <div class="r-card r-muted" style="padding:24px; text-align:center;">
            {{ $mm ? 'အော်ဒါ ဗလာဖြစ်နေသည်။' : 'Your order is empty.' }}
            <a href="{{ url('/store') }}">{{ $mm ? 'မီနူးသို့ →' : 'Browse the menu →' }}</a>
        </div>
    @else
        {{-- The check: dish · qty on the left, price hint on the right --}}
        <div class="r-card" style="padding:8px 26px; margin-bottom:18px;">
            @foreach ($lines as $l)
                <div class="r-menu-row">
                    <div>
                        <div class="mr-name">{{ $l['name'] }}</div>
                        <div class="mr-sku">{{ $l['sku'] }} · {{ $mm ? 'အရေအတွက်' : 'qty' }} {{ $l['qty'] }}</div>
                    </div>
                    <span class="mr-dots"></span>
                    @if ($l['price_hint'])<span class="mr-price">{{ $l['price_hint'] }}</span>@endif
                    <form method="POST" action="{{ url('/store/cart/remove') }}">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                        <button class="r-btn ghost sm" type="submit">{{ $mm ? 'ဖယ်ရန်' : 'Remove' }}</button>
                    </form>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ url('/store/checkout') }}" class="r-card" style="padding:22px 24px;">
            @csrf
            @if (count($fulfillment_types ?? []) > 1)
                @php
                    $ftCopy = [
                        'pickup'  => [$mm ? 'လာယူမည်' : 'Pickup',  $mm ? 'ကောင်တာမှာ လာယူပါ' : 'Collect at the counter'],
                        'dine_in' => [$mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in', $mm ? 'စားပွဲသို့ ပို့ပေးပါမည်' : 'We’ll bring it to your table'],
                        'delivery'=> [$mm ? 'အိမ်အရောက် ပို့မည်' : 'Delivery', $mm ? 'သင့်လိပ်စာသို့ ပို့ပေးပါမည်' : 'Delivered to your address'],
                    ];
                @endphp
                <div class="r-eyebrow" style="color:var(--muted); margin-bottom:8px;">{{ $mm ? 'ဘယ်လို ရယူမလဲ' : 'How would you like it?' }}</div>
                <div style="display:grid; gap:6px; margin:0 0 16px;">
                    @foreach ($fulfillment_types as $t)
                        @php [$ftLabel, $ftDesc] = $ftCopy[$t] ?? [ucfirst($t), '']; @endphp
                        <label style="display:flex; gap:10px; align-items:baseline; padding:9px 12px; border:1px solid var(--line); border-radius:11px; cursor:pointer; background:var(--paper);">
                            <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
                            <span><strong>{{ $ftLabel }}</strong> <span class="r-muted" style="font-size:13px;">— {{ $ftDesc }}</span></span>
                        </label>
                    @endforeach
                </div>
            @endif
            <label class="r-eyebrow" for="phone" style="color:var(--muted);">{{ $mm ? 'ဖုန်း (မထည့်လည်းရ)' : 'Phone (optional)' }}</label>
            <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                   style="width:100%; padding:12px; border:1px solid var(--line); border-radius:11px; margin:8px 0 8px; font:inherit; background:var(--paper);">
            <p class="r-muted" style="font-size:13px; margin:0 0 16px;">{{ $mm ? 'စုစုပေါင်းကို DOEH က checkout တွင် တွက်ပေးသည်။' : 'DOEH calculates your total at checkout.' }}</p>
            <button class="r-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'အော်ဒါ တင်ရန်' : 'Place order' }}</button>
            @unless ($ready)<p class="r-muted" style="font-size:13px; margin:12px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ အော်ဒါတင်နိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>@endunless
        </form>
        <p style="margin-top:16px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? '← မီနူးသို့ ပြန်ရန်' : '← Back to the menu' }}</a></p>
    @endif
@endsection
