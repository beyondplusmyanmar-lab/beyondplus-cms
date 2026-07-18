{{-- Service / guarantee strip — Shopee-style trust row. Static, honest copy;
     the free-shipping line reflects the shop's own sf_free_shipping_note. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $ship = bp_option('sf_free_shipping_note') ?: ($mm ? 'အိမ်တိုင်ရာရောက် ပို့ဆောင်ပေးသည်' : 'Fast delivery');
    $services = [
        ['bi-truck',          $mm ? 'ပို့ဆောင်ခ' : 'Delivery',       $ship],
        ['bi-cash-coin',      $mm ? 'ငွေချေမှု' : 'Payment',          $mm ? 'အိမ်ရောက်ငွေချေ (COD)' : 'Cash on delivery'],
        ['bi-patch-check',    $mm ? 'အစစ်အမှန်' : 'Authentic',        $mm ? '၁၀၀% မူရင်းပစ္စည်း' : '100% genuine products'],
        ['bi-arrow-repeat',   $mm ? 'ပြန်လဲ' : 'Easy returns',        $mm ? '၇ ရက်အတွင်း ပြန်လဲနိုင်' : '7-day return policy'],
    ];
@endphp
<section class="sf-section pt-0">
    <div class="container">
        <div class="sf-panel">
            <div class="sf-services">
                @foreach($services as $s)
                    <div class="sf-service">
                        <i class="bi {{ $s[0] }}"></i>
                        <span><span class="fw-semibold d-block" style="font-size:.82rem;">{{ $s[1] }}</span><span class="sf-muted" style="font-size:.76rem;">{{ $s[2] }}</span></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
