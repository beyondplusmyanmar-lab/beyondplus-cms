{{-- Hero — always visible. Uses the first slider image as background if one exists. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $eyebrow  = bp_option('biz_hero_eyebrow', $mm ? 'ကြိုဆိုပါသည်' : 'Welcome');
    $title    = bp_option('biz_hero_title') ?: $siteName;
    $subtitle = bp_option('biz_hero_subtitle') ?: (optional(site_information('blogdescription'))->option_value
                ?: ($mm ? 'အရည်အသွေးမြင့် ကုန်ပစ္စည်းများနှင့် ကျွမ်းကျင်သော ဝန်ဆောင်မှုများ ပေးအပ်ပါသည်။'
                        : 'We provide quality products and professional services you can rely on.'));
    $cta1Label = bp_option('biz_hero_cta_label', $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch');
    $cta1Url   = bp_option('biz_hero_cta_url') ?: url('/contact');
    $cta2Label = bp_option('biz_hero_cta2_label', $mm ? 'ဝန်ဆောင်မှုများ' : 'Our services');
    $cta2Url   = bp_option('biz_hero_cta2_url') ?: url('/#services');

    $sliders = bp_slider();
    $bgImage = $sliders->count() ? bp_upload_url($sliders->first()->slider_link) : null;
    // Commerce plugins can inject extra actions here (Shop Now, Book Now, Order Online…).
    $extraActions = bp_apply_filters('business_hero_actions', '');
@endphp

<section class="bz-hero {{ $bgImage ? 'bz-hero--img' : '' }}" @if($bgImage) style="background-image:url('{{ $bgImage }}');" @endif>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if($eyebrow)<span class="bz-eyebrow" style="color:#fff; opacity:.85;">{{ $eyebrow }}</span>@endif
                <h1 class="mt-2 mb-3">{{ $title }}</h1>
                <p class="lead mb-4">{{ $subtitle }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ $cta1Url }}" class="btn btn-primary btn-lg">{{ $cta1Label }}</a>
                    <a href="{{ $cta2Url }}" class="btn btn-outline-light btn-lg">{{ $cta2Label }}</a>
                    {!! $extraActions !!}
                </div>
            </div>
        </div>
    </div>
</section>
