{{-- Promo banner. Uses the first slider image if one exists, else a gradient. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $title = bp_option('sf_hero_title') ?: $siteName;
    $subtitle = bp_option('sf_hero_subtitle') ?: ($mm ? 'အရည်အသွေးမြင့် ကုန်ပစ္စည်းများ — အိမ်တိုင်ရာရောက် ပို့ဆောင်ပေးသည်။' : 'Quality products, delivered to your door.');
    $ctaLabel = bp_option('sf_hero_cta_label') ?: ($mm ? 'ဈေးဝယ်ရန်' : 'Shop now');
    $sliders = bp_slider();
    $bg = $sliders->count() ? bp_upload_url($sliders->first()->slider_link) : null;
    $extra = bp_apply_filters('business_hero_actions', '');
@endphp
<section class="sf-section pt-3">
    <div class="container">
        <div class="rounded-3 overflow-hidden position-relative d-flex align-items-center"
             style="min-height: 260px; padding: 2rem; color:#fff;
                    background: {{ $bg ? "linear-gradient(90deg, rgba(0,0,0,.55), rgba(0,0,0,.15)), url('".$bg."') center/cover" : 'linear-gradient(120deg, var(--sf-primary), var(--sf-accent))' }};">
            <div style="max-width: 560px;">
                <h1 class="fw-bold mb-2" style="font-size: clamp(1.6rem, 4vw, 2.6rem);">{{ $title }}</h1>
                <p class="mb-3" style="opacity:.95;">{{ $subtitle }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url('/shop') }}" class="btn btn-light btn-lg fw-bold" style="color:var(--sf-primary);">{{ $ctaLabel }}</a>
                    {!! $extra !!}
                </div>
            </div>
        </div>
    </div>
</section>
