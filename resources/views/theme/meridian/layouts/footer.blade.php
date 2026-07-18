@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="md-footer mt-5">
    <div class="container py-5">
        <div class="row gy-4 align-items-start">
            <div class="col-md-5">
                <div class="md-wordmark" style="font-size:1.6rem;">{{ $siteName }}</div>
                <p class="mt-2 mb-0" style="max-width:26rem;">
                    {{ optional(site_information('blogdescription'))->option_value ?: 'Reporting, features and updates — published with the Beyond Plus CMS.' }}
                </p>
            </div>
            <div class="col-6 col-md-3">
                <div class="md-kicker mb-2" style="color:#b8893b;">{{ $mm ? 'လမ်းညွှန်' : 'Sections' }}</div>
                <ul class="list-unstyled mb-0 lh-lg">
                    <li><a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>
                    <li><a href="{{ url('/events') }}">{{ $mm ? 'ပွဲများ' : 'Events' }}</a></li>
                    <li><a href="{{ url('/contact') }}">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <div class="md-kicker mb-2" style="color:#b8893b;">{{ $mm ? 'နောက်ဆုံးရ ပို့ရန်' : 'Follow' }}</div>
                <div class="d-flex gap-3 fs-5 mb-3">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
                <a href="{{ url('/bp-admin') }}" class="btn btn-md-ghost" style="border-color:#c9c0b3;color:#e9e2d6;">{{ $mm ? 'အက်ဒမင်' : 'Admin' }}</a>
            </div>
        </div>
    </div>
    <div class="border-top" style="border-color:rgba(255,255,255,.12)!important;">
        <div class="container py-3 d-flex flex-wrap justify-content-between small">
            <span>&copy; {{ date('Y') }} {{ $siteName }}. {{ $mm ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}</span>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
        </div>
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
