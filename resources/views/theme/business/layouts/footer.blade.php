@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $siteDesc = optional(site_information('blogdescription'))->option_value ?: 'Quality products and professional services.';
    $mm = app()->getLocale() === 'mm';
    $phone   = bp_option('biz_phone');
    $email   = bp_option('biz_email') ?: optional(site_information('admin_email'))->option_value;
    $address = bp_option('biz_address');
    $socialMap = [
        'biz_social_facebook'  => 'bi-facebook',
        'biz_social_twitter'   => 'bi-twitter-x',
        'biz_social_instagram' => 'bi-instagram',
        'biz_social_youtube'   => 'bi-youtube',
        'biz_social_linkedin'  => 'bi-linkedin',
        'biz_social_tiktok'    => 'bi-tiktok',
    ];
@endphp
<footer class="bz-footer mt-auto">
    <div class="container py-5">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h6 class="mb-2">{{ $siteName }}</h6>
                <p class="mb-3 small" style="color:#94a3b8; max-width:30ch;">{{ $siteDesc }}</p>
                <div class="bz-social d-flex gap-2 fs-6">
                    @foreach($socialMap as $key => $icon)
                        @if(bp_option($key))
                            <a href="{{ bp_option($key) }}" target="_blank" rel="noopener" aria-label="{{ str_replace('biz_social_', '', $key) }}"><i class="bi {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'အမြန်လင့်များ' : 'Quick Links' }}</h6>
                <ul class="list-unstyled small mb-0 d-grid gap-2">
                    <li><a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>
                    <li><a href="{{ url('/#services') }}">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'Services' }}</a></li>
                    <li><a href="{{ url('/#about') }}">{{ $mm ? 'အကြောင်း' : 'About' }}</a></li>
                    <li><a href="{{ url('/blog') }}">{{ $mm ? 'သတင်း' : 'News' }}</a></li>
                    <li><a href="{{ url('/contact') }}">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</h6>
                <ul class="list-unstyled small mb-0 d-grid gap-2" style="color:#94a3b8;">
                    @if($phone)<li><i class="bi bi-telephone me-1"></i> <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $phone) }}">{{ $phone }}</a></li>@endif
                    @if($email)<li><i class="bi bi-envelope me-1"></i> <a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                    @if($address)<li><i class="bi bi-geo-alt me-1"></i> {{ $address }}</li>@endif
                </ul>
            </div>

            <div class="col-lg-2">
                <h6 class="mb-3">{{ $mm ? 'တရားဝင်' : 'Legal' }}</h6>
                <ul class="list-unstyled small mb-0 d-grid gap-2">
                    <li><a href="{{ bp_option('biz_privacy_url') ?: url('/privacy-policy') }}">{{ $mm ? 'ကိုယ်ရေးမူဝါဒ' : 'Privacy Policy' }}</a></li>
                    <li><a href="{{ bp_option('biz_terms_url') ?: url('/terms') }}">{{ $mm ? 'စည်းကမ်းချက်များ' : 'Terms' }}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="border-top" style="border-color: rgba(255,255,255,.08) !important;">
        <div class="container py-3 text-center small" style="color:#94a3b8;">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ $mm ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}
        </div>
        {{-- Plugin hook: active plugins (analytics, chat widgets, POS scripts) render here. --}}
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
