{{-- Overrides doeh-commerce-storefront::cart. Data: lines, ready. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Your bag')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $count = array_sum(array_map(fn ($l) => (int) ($l['qty'] ?? 0), (array) $lines));
    @endphp

    <div class="rt-wrap" style="padding-top:44px;">
        <div class="rt-head-row">
            <h1 class="rt-h2">{{ $mm ? 'သင့်ခြင်း' : 'Your bag' }}</h1>
            @if (! empty($lines))
                <span class="rt-muted rt-small">{{ $count }} {{ $mm ? 'ခု' : ($count === 1 ? 'item' : 'items') }}</span>
            @endif
        </div>
        <hr class="rt-shelf-rule">

        @if (empty($lines))
            <div class="rt-panel" style="padding:48px 24px; text-align:center;">
                <p class="rt-h3" style="margin:0 0 8px;">{{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်' : 'Your bag is empty' }}</p>
                <p class="rt-muted rt-small" style="margin:0 0 20px;">{{ $mm ? 'ပစ္စည်းရွေးပြီး ဤနေရာတွင် ပြန်ကြည့်ပါ။' : 'Pick something from the shelves and it will show up here.' }}</p>
                <a class="rt-btn" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင် ကြည့်ရန်' : 'Start shopping' }}</a>
            </div>
        @else
            <div class="rt-two-col">
                <div class="rt-panel" style="padding:4px 22px;">
                    @foreach ($lines as $l)
                        <div style="display:flex; align-items:center; gap:15px; padding:18px 0; {{ ! $loop->last ? 'border-bottom:1px solid var(--rule-soft);' : '' }}">
                            <div aria-hidden="true" style="width:54px; height:54px; flex:0 0 auto; border-radius:13px; display:grid; place-items:center;
                                        background:linear-gradient(168deg, var(--brand-wash), #fff 62%); box-shadow: var(--inset-niche);
                                        font-weight:700; font-size:22px; letter-spacing:-.03em; color:color-mix(in srgb, var(--brand) 30%, #fff);">{{ mb_strtoupper(mb_substr($l['name'], 0, 1)) }}</div>
                            <div style="flex:1 1 auto; min-width:0;">
                                <div style="font-weight:600; letter-spacing:-.012em;">{{ $l['name'] }}</div>
                                <div class="rt-muted rt-micro" style="margin-top:2px;">
                                    <span style="font-variant-numeric:tabular-nums;">{{ $mm ? 'အရေအတွက်' : 'Qty' }} {{ $l['qty'] }}</span>
                                    @if ($l['price_hint'])<span aria-hidden="true"> &nbsp;·&nbsp; </span><span class="rt-money" style="font-weight:600;">{{ $l['price_hint'] }}</span>@endif
                                </div>
                            </div>
                            <form method="POST" action="{{ url('/store/cart/remove') }}">
                                @csrf
                                <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                <button class="rt-btn ghost sm" type="submit">{{ $mm ? 'ဖယ်ရန်' : 'Remove' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ url('/store/checkout') }}" class="rt-panel" style="padding:24px 26px;">
                    @csrf
                    @if ($errors->any())
                        {{-- Every checkout refusal comes back as a flashed error. Without this the
                             customer is bounced to the cart with no idea what went wrong, and just
                             presses the button again. --}}
                        <div role="alert" style="margin:0 0 16px; padding:12px 14px; border:1px solid #b42318; border-radius:12px; background:rgba(180,35,24,.07); color:#b42318; display:grid; gap:4px;">
                            @foreach ($errors->all() as $storefrontError)
                                <p class="rt-small" style="margin:0;">{{ $storefrontError }}</p>
                            @endforeach
                        </div>
                    @endif

                    <h2 class="rt-h3" style="margin-bottom:16px;">{{ $mm ? 'အော်ဒါ အတည်ပြုရန်' : 'Review and order' }}</h2>

                    @php
                        $ftTypes = $fulfillment_types ?? [];
                        $ftCopy = [
                            'pickup'   => [$mm ? 'လာယူမည်' : 'Pickup', $mm ? 'ဆိုင်မှာ လာယူပါ' : 'Collect at the shop'],
                            'dine_in'  => [$mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in', $mm ? 'စားပွဲသို့ ပို့ပေးပါမည်' : 'We’ll bring it to your table'],
                            'delivery' => [$mm ? 'အိမ်အရောက် ပို့မည်' : 'Delivery', $mm ? 'သင့်လိပ်စာသို့ ပို့ပေးပါမည်' : 'Delivered to your address'],
                        ];
                        $ftOffersDelivery = in_array('delivery', $ftTypes, true);
                        $ftDeliveryOnly = $ftOffersDelivery && count($ftTypes) === 1;
                    @endphp

                    @if (count($ftTypes) > 1)
                        <div class="rt-small" style="font-weight:600; margin-bottom:8px;">{{ $mm ? 'ဘယ်လို ရယူမလဲ' : 'How would you like it?' }}</div>
                        <div style="display:grid; gap:6px; margin:0 0 16px;">
                            @foreach ($ftTypes as $t)
                                @php [$ftLabel, $ftDesc] = $ftCopy[$t] ?? [ucfirst($t), '']; @endphp
                                <label style="display:flex; gap:10px; align-items:baseline; padding:9px 12px; border:1px solid var(--rule); border-radius:11px; cursor:pointer; background:var(--paper);">
                                    <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
                                    <span><strong>{{ $ftLabel }}</strong> <span class="rt-muted" style="font-size:13px;">— {{ $ftDesc }}</span></span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @if ($ftOffersDelivery)
                        {{-- Shown only when the customer is actually having it delivered. The
                             installation refuses a delivery with no address, so this is a
                             courtesy, not the guard. --}}
                        <div id="rt-delivery-fields" @unless($ftDeliveryOnly) hidden @endunless>
                            <label for="addr_street" class="rt-small" style="display:block; font-weight:600; margin-bottom:6px;">{{ $mm ? 'ပို့ဆောင်မည့်လိပ်စာ' : 'Delivery address' }}</label>
                            <input id="addr_street" name="addr_street" type="text" autocomplete="street-address"
                                   placeholder="{{ $mm ? 'လမ်း / အမှတ် / အခန်း' : 'Street, house or unit number' }}"
                                   style="width:100%; padding:12px 14px; border:1px solid var(--rule); border-radius:12px; margin-bottom:8px;
                                          font:inherit; background:var(--paper); color:var(--ink);">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:14px;">
                                <input id="addr_township" name="addr_township" type="text"
                                       placeholder="{{ $mm ? 'မြို့နယ်' : 'Township' }}"
                                       style="padding:12px 14px; border:1px solid var(--rule); border-radius:12px; font:inherit; background:var(--paper); color:var(--ink);">
                                <input id="addr_city" name="addr_city" type="text"
                                       placeholder="{{ $mm ? 'မြို့' : 'City' }}"
                                       style="padding:12px 14px; border:1px solid var(--rule); border-radius:12px; font:inherit; background:var(--paper); color:var(--ink);">
                            </div>
                            <p class="rt-muted rt-small" style="margin:-6px 0 14px;">{{ $mm ? 'ပို့ဆောင်ခကို ဆိုင်မှ အတည်ပြုချိန်တွင် သတ်မှတ်ပါမည်။' : 'The shop sets the delivery charge when it confirms your order.' }}</p>
                        </div>
                    @endif

                    <label for="phone" class="rt-small" style="display:block; font-weight:600; margin-bottom:6px;">{{ $mm ? 'ဖုန်း' : 'Phone' }}
                        <span class="rt-muted" style="font-weight:400;">{{ $mm ? '(မဖြစ်မနေ)' : '(required)' }}</span>
                    </label>
                    <input id="phone" name="phone" type="tel" required placeholder="+95 9 123 456 78"
                           style="width:100%; padding:12px 14px; border:1px solid var(--rule); border-radius:12px; margin-bottom:14px;
                                  font:inherit; background:var(--paper); color:var(--ink);">
                    <p class="rt-muted rt-small" style="margin:0 0 18px;">{{ $mm ? 'စုစုပေါင်းကို DOEH က checkout တွင် တွက်ပေးသည်။' : 'DOEH works out your total at checkout.' }}</p>

                    <button class="rt-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'အော်ဒါ တင်ရန်' : 'Place order' }}</button>
                    @unless ($ready)
                        <p class="rt-muted rt-small" style="margin:14px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ အော်ဒါတင်နိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>
                    @endunless
                </form>
            </div>
            <p style="margin-top:22px;"><a href="{{ url('/store') }}">{{ $mm ? 'ဆက်လက် ဝယ်ယူရန်' : 'Keep shopping' }}</a></p>
        @endif
    </div>
@endsection

@if (in_array('delivery', $fulfillment_types ?? [], true) && count($fulfillment_types ?? []) > 1)
    <script>
        (function () {
            var box = document.getElementById('rt-delivery-fields');
            if (!box) { return; }
            var radios = document.querySelectorAll('input[name="fulfillment"]');
            function sync() {
                var picked = document.querySelector('input[name="fulfillment"]:checked');
                var on = !!picked && picked.value === 'delivery';
                box.hidden = !on;
                Array.prototype.forEach.call(box.querySelectorAll('input'), function (i) { i.disabled = !on; });
            }
            Array.prototype.forEach.call(radios, function (r) { r.addEventListener('change', sync); });
            sync();
        })();
    </script>
@endif
