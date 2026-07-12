{{-- Testimonials — social proof. JSON list of {quote,name,role,rating}.
     Set biz_testimonials_json to "[]" to hide. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $default = [
        ['quote' => $mm ? 'ဝန်ဆောင်မှု အလွန်ကောင်းပါသည်။ အကြံပြုလိုပါသည်။' : 'Excellent service and great quality. Highly recommended.', 'name' => $mm ? 'ဖောက်သည်' : 'Aung Aung', 'role' => $mm ? 'ပုံမှန်ဖောက်သည်' : 'Regular customer', 'rating' => 5],
        ['quote' => $mm ? 'ပို့ဆောင်မှု မြန်ဆန်ပြီး အဖွဲ့သားများ ဖော်ရွေပါသည်။' : 'Fast delivery and a friendly, professional team.', 'name' => $mm ? 'ဖောက်သည်' : 'Su Su', 'role' => $mm ? 'စီးပွားရေးလုပ်ငန်းရှင်' : 'Business owner', 'rating' => 5],
        ['quote' => $mm ? 'တန်ဖိုးနှင့် အရည်အသွေး ကိုက်ညီပါသည်။' : 'Great value for money — exactly as described.', 'name' => $mm ? 'ဖောက်သည်' : 'Ko Min', 'role' => $mm ? 'ဖောက်သည်' : 'Customer', 'rating' => 5],
    ];
    $items = json_decode(bp_option('biz_testimonials_json', ''), true);
    if (!is_array($items)) { $items = $default; }
@endphp

@if(count($items))
<section class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'သုံးသပ်ချက်များ' : 'Testimonials' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_testimonials_title', $mm ? 'ဖောက်သည်များ ပြောသည်' : 'What Our Customers Say') }}</h2>
        </div>
        <div class="row g-4">
            @foreach($items as $t)
                @php $t = (array) $t; $rating = (int) ($t['rating'] ?? 5); @endphp
                <div class="col-lg-4">
                    <div class="bz-card h-100 p-4">
                        <div class="mb-2" style="color: var(--bz-accent);" aria-label="{{ $rating }} star rating">
                            @for($i = 0; $i < 5; $i++)<i class="bi {{ $i < $rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor
                        </div>
                        <p class="mb-3">{{ $t['quote'] ?? '' }}</p>
                        <div class="d-flex align-items-center gap-2">
                            <span class="bz-ico" style="width:40px;height:40px;font-size:1rem;"><i class="bi bi-person"></i></span>
                            <div>
                                <strong class="d-block small">{{ $t['name'] ?? '' }}</strong>
                                @if(!empty($t['role']))<span class="bz-muted small">{{ $t['role'] }}</span>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
