@php $siteName = optional(site_information('blogname'))->option_value ?: config('app.name'); @endphp
<footer class="bp-footer mt-5">
    <div class="container py-5">
        <div class="row gy-4">
            <div class="col-md-6">
                <h5 class="text-white">{{ $siteName }}</h5>
                <p class="mb-0 text-secondary">
                    {{ optional(site_information('blogdescription'))->option_value ?: 'A modern, multi-language content-management system.' }}
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Links</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ url('/bp-admin') }}">Admin</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Follow</h6>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top border-secondary-subtle">
        <div class="container py-3 text-center small text-secondary">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ app()->getLocale() === 'mm' ? 'မူပိုင်ခွင့် အားလုံး ရယူထားသည်။' : 'All rights reserved.' }}
        </div>
        {{-- Plugin hook: active plugins can render into the footer here. --}}
        @php bp_do_action('theme_footer') @endphp
    </div>
</footer>
