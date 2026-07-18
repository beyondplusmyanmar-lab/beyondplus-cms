@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $mm = app()->getLocale() === 'mm';
@endphp
<footer class="nc-footer mt-5">
    <div class="container py-5">
        <div class="row gy-4">
            <div class="col-md-6">
                <h5 class="nc-gradient-text mb-2"><span class="nc-brand-dot"></span>{{ $siteName }}</h5>
                <p class="mb-0 nc-muted" style="max-width:26rem;">
                    {{ optional(site_information('blogdescription'))->option_value ?: 'A modern, multi-language content platform — built with the Beyond Plus CMS.' }}
                </p>
            </div>
            <div class="col-6 col-md-3">
                <div class="nc-eyebrow mb-2">{{ $mm ? 'လမ်းညွှန်' : 'Explore' }}</div>
                <ul class="list-unstyled mb-0 lh-lg">
                    <li><a href="{{ url('/') }}">{{ $mm ? 'ပင်မ' : 'Home' }}</a></li>
                    <li><a href="{{ url('/events') }}">{{ $mm ? 'ပွဲများ' : 'Events' }}</a></li>
                    <li><a href="{{ url('/bp-admin') }}">{{ $mm ? 'အက်ဒမင်' : 'Admin' }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <div class="nc-eyebrow mb-2">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Connect' }}</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top" style="border-color:var(--nc-border)!important;">
        <div class="container py-3 text-center small nc-muted">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ $mm ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}
        </div>
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
