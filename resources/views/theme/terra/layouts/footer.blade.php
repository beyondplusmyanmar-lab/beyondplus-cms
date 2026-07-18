@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="tr-footer mt-5">
    <div class="container py-5">
        <div class="row gy-4 align-items-start">
            <div class="col-md-6">
                <div class="tr-display fs-4 mb-2">{{ $siteName }}</div>
                <p class="tr-muted mb-0" style="max-width:24rem;">
                    {{ optional(site_information('blogdescription'))->option_value ?: 'Words and ideas, published simply — with the Beyond Plus CMS.' }}
                </p>
            </div>
            <div class="col-6 col-md-3">
                <div class="tr-label mb-3">{{ $mm ? 'လမ်းညွှန်' : 'menu' }}</div>
                <ul class="list-unstyled mb-0 lh-lg">
                    <li><a href="{{ url('/') }}" class="tr-ul">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>
                    <li><a href="{{ url('/events') }}" class="tr-ul">{{ $mm ? 'ပွဲများ' : 'Events' }}</a></li>
                    <li><a href="{{ url('/contact') }}" class="tr-ul">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <div class="tr-label mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'elsewhere' }}</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top" style="border-color:var(--tr-line)!important;">
        <div class="container py-3 d-flex flex-wrap justify-content-between small tr-muted">
            <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
            <a href="{{ url('/bp-admin') }}" class="tr-muted">{{ $mm ? 'အက်ဒမင်' : 'Admin' }}</a>
        </div>
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
