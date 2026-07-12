{{-- Statistics — counter row. JSON list of {num,label}; set biz_stats_json to
     "[]" to hide. Ships defaults so the section looks complete out of the box. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $default = [
        ['num' => '1200+', 'label' => $mm ? 'ဖောက်သည်များ' : 'Customers'],
        ['num' => '15',    'label' => $mm ? 'နှစ်ပေါင်း အတွေ့အကြုံ' : 'Years Experience'],
        ['num' => '500+',  'label' => $mm ? 'ကုန်ပစ္စည်းများ' : 'Products'],
        ['num' => '98%',   'label' => $mm ? 'ဖောက်သည် ကျေနပ်မှု' : 'Satisfaction'],
    ];
    $items = json_decode(bp_option('biz_stats_json', ''), true);
    if (!is_array($items)) { $items = $default; }
@endphp

@if(count($items))
<section class="bz-section bz-section--alt">
    <div class="container">
        <div class="row g-4">
            @foreach($items as $stat)
                @php $stat = (array) $stat; @endphp
                <div class="col-6 col-lg-3">
                    <div class="bz-stat">
                        <div class="bz-stat__num">{{ $stat['num'] ?? '' }}</div>
                        <div class="bz-stat__label">{{ $stat['label'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
