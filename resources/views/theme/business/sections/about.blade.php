{{-- About — company intro with mission/vision. Text from options with defaults. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $title = bp_option('biz_about_title') ?: ($mm ? 'ကျွန်ုပ်တို့အကြောင်း' : 'About Our Company');
    $body  = bp_option('biz_about_body') ?: ($mm
        ? 'ကျွန်ုပ်တို့သည် ဖောက်သည်များအား အရည်အသွေးမြင့် ကုန်ပစ္စည်းများနှင့် ယုံကြည်စိတ်ချရသော ဝန်ဆောင်မှုများ ပေးအပ်ရန် ကတိကဝတ်ပြုထားပါသည်။'
        : 'We are committed to delivering quality products and dependable service to every customer, building lasting relationships through trust and consistency.');
    $years   = bp_option('biz_about_years');
    $mission = bp_option('biz_about_mission') ?: ($mm ? 'ဖောက်သည်တိုင်းအတွက် တန်ဖိုးရှိသော အတွေ့အကြုံ ဖန်တီးရန်။' : 'To create real value for every customer we serve.');
    $vision  = bp_option('biz_about_vision')  ?: ($mm ? 'ဒေသတွင်း ယုံကြည်ရဆုံး လုပ်ငန်း ဖြစ်လာရန်။' : 'To be the most trusted business in our region.');
    $image   = bp_option('biz_about_image') ? bp_upload_url(bp_option('biz_about_image')) : null;
@endphp

<section id="about" class="bz-section bz-section--alt">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="bz-eyebrow">{{ $mm ? 'အကြောင်း' : 'About us' }}</span>
                <h2 class="mt-2 mb-3">{{ $title }}</h2>
                <p class="bz-muted">{{ $body }}</p>
                <div class="row g-4 mt-1">
                    <div class="col-sm-6">
                        <h6 class="mb-1"><i class="bi bi-bullseye text-primary me-1"></i> {{ $mm ? 'ရည်မှန်းချက်' : 'Mission' }}</h6>
                        <p class="bz-muted small mb-0">{{ $mission }}</p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="mb-1"><i class="bi bi-eye text-primary me-1"></i> {{ $mm ? 'အနာဂတ်မျှော်မှန်း' : 'Vision' }}</h6>
                        <p class="bz-muted small mb-0">{{ $vision }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                @if($image)
                    <img src="{{ $image }}" class="img-fluid rounded shadow-sm w-100" style="object-fit:cover; max-height:420px;" alt="{{ $siteName }}">
                @else
                    <div class="bz-card p-5 text-center">
                        @if($years)
                            <div class="bz-stat__num">{{ $years }}+</div>
                            <p class="bz-muted mb-0">{{ $mm ? 'နှစ်ပေါင်း လုပ်ငန်း အတွေ့အကြုံ' : 'Years in business' }}</p>
                        @else
                            <i class="bi bi-buildings text-primary" style="font-size:3.5rem;"></i>
                            <p class="bz-muted mt-3 mb-0">{{ $siteName }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
