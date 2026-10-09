{{-- Overrides doeh-commerce-storefront::cart. Data: lines, ready, fulfillment_types.
     Field names (fulfillment, addr_street, addr_township, addr_city, phone) are the
     storefront plugin's checkout contract — same as every DOEH theme. --}}
@extends('theme.doeh-mart.layouts.app')
@section('title', 'Cart')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $count = array_sum(array_map(fn ($l) => (int) ($l['qty'] ?? 0), (array) $lines));
        $ftTypes = $fulfillment_types ?? [];
        $ftCopy = [
            'pickup'   => [$mm ? 'လာယူမည်' : 'Pickup', $mm ? 'ဆိုင်မှာ လာယူပါ' : 'Collect at the shop', 'bi-shop'],
            'dine_in'  => [$mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in', $mm ? 'စားပွဲသို့ ပို့ပေးပါမည်' : 'We’ll bring it to your table', 'bi-cup-hot'],
            'delivery' => [$mm ? 'အိမ်အရောက် ပို့မည်' : 'Delivery', $mm ? 'သင့်လိပ်စာသို့ ပို့ပေးပါမည်' : 'Delivered to your address', 'bi-truck'],
        ];
        $ftOffersDelivery = in_array('delivery', $ftTypes, true);
        $flagSku = session('doeh_store_flag_sku');
    @endphp

    <div class="container pt-3">
        <div class="d-flex align-items-baseline justify-content-between mb-2">
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-cart3" style="color:var(--mt-primary);"></i> {{ $mm ? 'သင့်ခြင်း' : 'Your cart' }}</h1>
            @if (! empty($lines))<span class="small mt-muted">{{ $count }} {{ $mm ? 'ခု' : ($count === 1 ? 'item' : 'items') }}</span>@endif
        </div>

        @if (empty($lines))
            <div class="mt-panel text-center py-5">
                <i class="bi bi-cart-x mt-muted" style="font-size:2.2rem;"></i>
                <p class="fw-semibold mb-1 mt-2">{{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်' : 'Your cart is empty' }}</p>
                <p class="small mt-muted mb-3">{{ $mm ? 'ပစ္စည်းရွေးပြီး ဤနေရာတွင် ပြန်ကြည့်ပါ။' : 'Pick something from the shop and it will show up here.' }}</p>
                <a class="btn btn-primary" href="{{ url('/store') }}">{{ $mm ? 'ဈေးဝယ်ရန်' : 'Start shopping' }}</a>
            </div>
        @else
            <div class="row g-3 align-items-start">
                <div class="col-lg-7">
                    <div class="mt-panel py-1">
                        @foreach ($lines as $l)
                            @php $flagged = $flagSku === $l['sku']; @endphp
                            <div class="d-flex align-items-center py-3 {{ ! $loop->last ? 'border-bottom' : '' }}" style="gap:.9rem;{{ $flagged ? 'background:var(--mt-wash);margin:0 -.75rem;padding-left:.75rem;padding-right:.75rem;' : '' }}">
                                <div class="mt-thumb-sm" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="fw-semibold text-truncate">{{ $l['name'] }}</div>
                                    @if ($l['price_hint'])<div class="small mt-money" style="color:var(--mt-primary);">{{ $l['price_hint'] }}</div>@endif
                                    @if ($flagged)<div class="small fw-semibold" style="color:var(--mt-primary-dark);">{{ $mm ? 'အရေအတွက်ကို လျှော့ပေးပါ' : 'Lower this quantity' }}</div>@endif
                                </div>
                                <div class="mt-qty" role="group" aria-label="{{ $mm ? 'အရေအတွက်' : 'Quantity' }} {{ $l['name'] }}">
                                    <form method="POST" action="{{ url('/store/cart/update') }}">
                                        @csrf
                                        <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                        <input type="hidden" name="qty" value="{{ $l['qty'] - 1 }}">
                                        <button type="submit" aria-label="{{ $l['qty'] <= 1 ? ($mm ? 'ဖယ်ရန်' : 'Remove') : ($mm ? 'တစ်ခု လျှော့ရန်' : 'One fewer') }}"><i class="bi bi-dash"></i></button>
                                    </form>
                                    <span class="mt-money" aria-live="polite">{{ $l['qty'] }}</span>
                                    <form method="POST" action="{{ url('/store/cart/update') }}">
                                        @csrf
                                        <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                        <input type="hidden" name="qty" value="{{ $l['qty'] + 1 }}">
                                        <button type="submit" @disabled($l['qty'] >= 99) aria-label="{{ $mm ? 'တစ်ခု တိုးရန်' : 'One more' }}"><i class="bi bi-plus"></i></button>
                                    </form>
                                </div>
                                <form method="POST" action="{{ url('/store/cart/remove') }}">
                                    @csrf
                                    <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="{{ $mm ? 'ဖယ်ရန်' : 'Remove' }} {{ $l['name'] }}"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 mb-0"><a href="{{ url('/store') }}"><i class="bi bi-arrow-left"></i> {{ $mm ? 'ဆက်လက် ဝယ်ယူရန်' : 'Keep shopping' }}</a></p>
                </div>

                <div class="col-lg-5">
                    <form method="POST" action="{{ url('/store/checkout') }}" class="mt-panel">
                        @csrf
                        <h2 class="h6 fw-bold mb-3">{{ $mm ? 'အော်ဒါ အတည်ပြုရန်' : 'Review and order' }}</h2>

                        @if (count($ftTypes) > 1)
                            <div class="small fw-semibold mb-2">{{ $mm ? 'ဘယ်လို ရယူမလဲ' : 'How would you like it?' }}</div>
                            <div class="d-grid gap-2 mb-3">
                                @foreach ($ftTypes as $t)
                                    @php [$ftLabel, $ftDesc, $ftIcon] = $ftCopy[$t] ?? [ucfirst($t), '', 'bi-bag']; @endphp
                                    <label class="mt-choice">
                                        <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
                                        <span><i class="bi {{ $ftIcon }}" style="color:var(--mt-primary);"></i> <strong>{{ $ftLabel }}</strong> <span class="mt-muted small">— {{ $ftDesc }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif (count($ftTypes) === 1)
                            @php [$ftLabel, $ftDesc, $ftIcon] = $ftCopy[$ftTypes[0]] ?? [ucfirst($ftTypes[0]), '', 'bi-bag']; @endphp
                            <p class="small mb-3"><i class="bi {{ $ftIcon }}" style="color:var(--mt-primary);"></i> <strong>{{ $ftLabel }}</strong> <span class="mt-muted">— {{ $ftDesc }}</span></p>
                        @endif

                        @if ($ftOffersDelivery)
                            {{-- Visible by default: the script below hides it for a collected order. If the
                                 script never runs, the customer sees a field they can ignore, never a box
                                 they cannot open. The installation refuses a delivery without an address. --}}
                            <div id="mt-delivery-fields">
                                <label for="addr_street" class="form-label small fw-semibold">{{ $mm ? 'ပို့ဆောင်မည့်လိပ်စာ' : 'Delivery address' }}</label>
                                <input id="addr_street" name="addr_street" type="text" autocomplete="street-address" class="form-control mb-2"
                                       placeholder="{{ $mm ? 'လမ်း / အမှတ် / အခန်း' : 'Street, house or unit number' }}">
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><input id="addr_township" name="addr_township" type="text" class="form-control" placeholder="{{ $mm ? 'မြို့နယ်' : 'Township' }}"></div>
                                    <div class="col-6"><input id="addr_city" name="addr_city" type="text" class="form-control" placeholder="{{ $mm ? 'မြို့' : 'City' }}"></div>
                                </div>
                                <p class="small mt-muted mb-3">{{ $mm ? 'ပို့ဆောင်ခကို ဆိုင်မှ အတည်ပြုချိန်တွင် သတ်မှတ်ပါမည်။' : 'The shop sets the delivery charge when it confirms your order.' }}</p>
                            </div>
                        @endif

                        <label for="phone" class="form-label small fw-semibold">{{ $mm ? 'ဖုန်း' : 'Phone' }} <span class="mt-muted fw-normal">{{ $mm ? '(မဖြစ်မနေ)' : '(required)' }}</span></label>
                        <input id="phone" name="phone" type="tel" required class="form-control mb-2" placeholder="+95 9 123 456 78">
                        <p class="small mt-muted mb-3">{{ $mm ? 'စုစုပေါင်းကို ဆိုင်မှ တွက်ပေးသည်။' : 'The shop works out your total.' }}</p>

                        <button class="btn btn-primary w-100 py-2" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'အော်ဒါ တင်ရန်' : 'Place order' }}</button>
                        @unless ($ready)
                            <p class="small mt-muted text-center mt-2 mb-0">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ အော်ဒါတင်နိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>
                        @endunless
                    </form>
                </div>
            </div>
        @endif
    </div>

    @if ($ftOffersDelivery && count($ftTypes) > 1)
        @push('scripts')
        <script>
            (function () {
                var box = document.getElementById('mt-delivery-fields');
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
        @endpush
    @endif
@endsection
