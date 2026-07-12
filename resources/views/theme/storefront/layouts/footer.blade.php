@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
    $phone = bp_option('sf_phone'); $email = bp_option('sf_email') ?: optional(site_information('admin_email'))->option_value; $address = bp_option('sf_address');
    $socials = ['sf_social_facebook' => 'bi-facebook', 'sf_social_instagram' => 'bi-instagram', 'sf_social_tiktok' => 'bi-tiktok'];
@endphp
<footer class="sf-footer mt-4">
    <div class="container py-4">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h6 class="mb-2"><i class="bi bi-shop text-primary"></i> {{ $siteName }}</h6>
                <p class="small mb-3">{{ optional(site_information('blogdescription'))->option_value ?: ($mm ? 'အွန်လိုင်း ဈေးဆိုင်။' : 'Your online shop.') }}</p>
                <div class="sf-social d-flex gap-2">
                    @foreach($socials as $key => $icon)
                        @if(bp_option($key))<a href="{{ bp_option($key) }}" target="_blank" rel="noopener"><i class="bi {{ $icon }}"></i></a>@endif
                    @endforeach
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">{{ $mm ? 'ဈေးဝယ်ရန်' : 'Shopping' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/shop') }}">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</a></li>
                    <li><a href="{{ url('/cart') }}">{{ $mm ? 'ခြင်း' : 'Cart' }}</a></li>
                    <li><a href="{{ url('/faq') }}">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ကုမ္ပဏီ' : 'Company' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/about') }}">{{ $mm ? 'အကြောင်း' : 'About' }}</a></li>
                    <li><a href="{{ url('/shipping') }}">{{ $mm ? 'ပို့ဆောင်မှု' : 'Shipping & Returns' }}</a></li>
                    <li><a href="{{ url('/contact') }}">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    @if($phone)<li><i class="bi bi-telephone text-primary me-1"></i> {{ $phone }}</li>@endif
                    @if($email)<li><i class="bi bi-envelope text-primary me-1"></i> {{ $email }}</li>@endif
                    @if($address)<li><i class="bi bi-geo-alt text-primary me-1"></i> {{ $address }}</li>@endif
                </ul>
            </div>
        </div>
    </div>
    <div class="border-top">
        <div class="container py-3 text-center small">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ $mm ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}
        </div>
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
