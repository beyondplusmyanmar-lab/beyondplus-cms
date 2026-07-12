{{-- Services — card grid. Reads a JSON list from options; ships tasteful
     defaults so a fresh site looks complete. Set biz_services_json to override,
     or set it to "[]" to hide the section entirely. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $default = [
        ['icon' => 'bi-gem',          'name' => $mm ? 'အရည်အသွေး ကုန်ပစ္စည်း' : 'Quality Products',   'desc' => $mm ? 'ယုံကြည်စိတ်ချရသော အရည်အသွေးမြင့် ကုန်ပစ္စည်းများ။' : 'Dependable, high-quality products for every need.'],
        ['icon' => 'bi-people',       'name' => $mm ? 'ကျွမ်းကျင် ဝန်ဆောင်မှု' : 'Professional Service', 'desc' => $mm ? 'အတွေ့အကြုံရှိ အဖွဲ့မှ ဝန်ဆောင်မှု ပေးအပ်ပါသည်။' : 'An experienced team ready to help you succeed.'],
        ['icon' => 'bi-truck',        'name' => $mm ? 'မြန်ဆန်သော ပို့ဆောင်မှု' : 'Fast Delivery',      'desc' => $mm ? 'အချိန်မီ လုံခြုံစွာ ပို့ဆောင်ပေးပါသည်။' : 'On-time, reliable delivery across the country.'],
        ['icon' => 'bi-headset',      'name' => $mm ? 'ပံ့ပိုးကူညီမှု' : 'Ongoing Support',              'desc' => $mm ? 'ရောင်းပြီး ဝန်ဆောင်မှုကို အမြဲ ပံ့ပိုးပါသည်။' : 'Responsive support before and after every sale.'],
    ];
    $items = json_decode(bp_option('biz_services_json', ''), true);
    if (!is_array($items)) { $items = $default; }
@endphp

@if(count($items))
<section id="services" class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'What we do' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_services_title', $mm ? 'ကျွန်ုပ်တို့၏ ဝန်ဆောင်မှုများ' : 'Our Services') }}</h2>
            <p class="bz-muted mb-0">{{ bp_option('biz_services_subtitle', $mm ? 'သင့်လုပ်ငန်း တိုးတက်စေရန် ကျွန်ုပ်တို့ ကူညီပါသည်။' : 'Everything you need, delivered with care.') }}</p>
        </div>
        <div class="row g-4">
            @foreach($items as $s)
                @php $s = (array) $s; @endphp
                <div class="col-lg-3 col-sm-6">
                    <div class="bz-card h-100 p-4">
                        <span class="bz-ico mb-3"><i class="bi {{ $s['icon'] ?? 'bi-check2-circle' }}"></i></span>
                        <h5 class="h6 mb-2">{{ $s['name'] ?? '' }}</h5>
                        <p class="bz-muted small mb-3">{{ $s['desc'] ?? '' }}</p>
                        @if(!empty($s['url']))
                            <a href="{{ $s['url'] }}" class="small fw-semibold">{{ $mm ? 'ဆက်ဖတ်ရန်' : 'Learn more' }} <i class="bi bi-arrow-right"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
