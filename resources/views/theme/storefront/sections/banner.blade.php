{{-- Shopee-style hero: a rotating banner carousel + a stacked promo-tile
     column. The carousel always opens on an on-brand orange promo slide, then
     shows any merchant slider images; the side tiles are static on-brand promos.
     Everything stays in the storefront palette regardless of slider content. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $title    = bp_option('sf_hero_title') ?: $siteName;
    $subtitle = bp_option('sf_hero_subtitle') ?: ($mm ? 'အရည်အသွေးမြင့် ကုန်ပစ္စည်းများ — အိမ်တိုင်ရာရောက် ပို့ဆောင်ပေးသည်။' : 'Quality products, delivered to your door.');
    $ctaLabel = bp_option('sf_hero_cta_label') ?: ($mm ? 'ဈေးဝယ်ရန်' : 'Shop now');
    $ship     = bp_option('sf_free_shipping_note') ?: ($mm ? 'အိမ်တိုင်ရာရောက် ပို့ဆောင်' : 'Fast delivery, COD');
    $sliders  = bp_slider();
    $extra    = bp_apply_filters('business_hero_actions', '');
@endphp
<section class="sf-section pt-3">
    <div class="container">
        <div class="row g-2 g-lg-3">
            {{-- Main carousel --}}
            <div class="col-lg-8">
                <div id="sfHero" class="carousel slide sf-hero-main h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        {{-- Branded orange promo slide (always first) --}}
                        <div class="carousel-item active h-100">
                            <div class="sf-hero-slide h-100">
                                <div style="max-width:560px;">
                                    <h1 class="sf-hero-title mb-2">{{ $title }}</h1>
                                    <p class="mb-3" style="opacity:.95;">{{ $subtitle }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ url('/shop') }}" class="btn btn-light btn-lg fw-bold" style="color:var(--sf-primary);">{{ $ctaLabel }} <i class="bi bi-arrow-right"></i></a>
                                        {!! $extra !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Merchant slider images --}}
                        @foreach($sliders as $slide)
                            <div class="carousel-item h-100">
                                <a href="{{ $slide->slider_url ?: url('/shop') }}" class="d-block h-100">
                                    <div class="sf-hero-slide has-img h-100"
                                         style="background-image:linear-gradient(90deg,rgba(215,50,17,.88),rgba(238,77,45,.35)),url('{{ bp_upload_url($slide->slider_link) }}');">
                                        <div style="max-width:560px;">
                                            <h2 class="sf-hero-title mb-2">{{ $slide->slider_name }}</h2>
                                            @if($slide->slider_description)<p class="mb-0" style="opacity:.95;">{{ \Illuminate\Support\Str::limit($slide->slider_description, 90) }}</p>@endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($sliders->count() > 0)
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#sfHero" data-bs-slide-to="0" class="active" aria-label="1"></button>
                            @foreach($sliders as $i => $s)
                                <button type="button" data-bs-target="#sfHero" data-bs-slide-to="{{ $i + 1 }}" aria-label="{{ $i + 2 }}"></button>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#sfHero" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Prev</span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#sfHero" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span></button>
                    @endif
                </div>
            </div>

            {{-- Side promo tiles (desktop) --}}
            <div class="col-lg-4 d-none d-lg-block">
                <div class="sf-hero-side">
                    <a href="{{ url('/shop') }}" class="sf-hero-tile" style="background:linear-gradient(120deg,var(--sf-primary),var(--sf-primary-dark));">
                        <i class="bi bi-ticket-perforated mb-1"></i>
                        <span class="t">{{ $mm ? 'ဗောက်ချာများ' : 'Vouchers & Deals' }}</span>
                        <span style="opacity:.9;font-size:.82rem;">{{ $mm ? 'သက်သာသော ဈေးနှုန်းများ' : 'Save more, every day' }}</span>
                    </a>
                    <a href="{{ url('/shop') }}" class="sf-hero-tile" style="background:linear-gradient(120deg,var(--sf-accent),#ff8a00);color:#4a2c00;">
                        <i class="bi bi-truck mb-1"></i>
                        <span class="t">{{ $mm ? 'ပို့ဆောင်မှု' : 'Delivery' }}</span>
                        <span style="opacity:.95;font-size:.82rem;">{{ $ship }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
