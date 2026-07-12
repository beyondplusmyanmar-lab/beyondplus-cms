{{-- Why Choose Us — checklist of differentiators. JSON list of strings, or defaults. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $default = $mm
        ? ['အရည်အသွေး ကုန်ပစ္စည်းများ', 'ယုံကြည်ရသော ဝန်ဆောင်မှု', 'မြန်ဆန်သော ပို့ဆောင်မှု', 'ကျွမ်းကျင်သော အဖွဲ့']
        : ['Quality Products', 'Trusted Service', 'Fast Delivery', 'Professional Team'];
    $items = json_decode(bp_option('biz_features_json', ''), true);
    if (!is_array($items) || !count($items)) { $items = $default; }
@endphp

@if(count($items))
<section class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'အားသာချက်များ' : 'Why us' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_features_title', $mm ? 'ကျွန်ုပ်တို့ကို ရွေးချယ်ရသည့် အကြောင်း' : 'Why Choose Us') }}</h2>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach($items as $feature)
                @php $feature = is_array($feature) ? ($feature['name'] ?? '') : $feature; @endphp
                <div class="col-lg-3 col-sm-6">
                    <div class="d-flex align-items-start gap-3">
                        <span class="bz-ico flex-shrink-0" style="width:40px;height:40px;font-size:1.1rem;"><i class="bi bi-check-lg"></i></span>
                        <div class="pt-1"><h6 class="mb-0">{{ $feature }}</h6></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
