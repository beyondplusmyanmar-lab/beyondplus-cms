{{-- Contact — details + form + map. Form posts to the existing /contact route. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $phone   = bp_option('biz_phone');
    $email   = bp_option('biz_email') ?: optional(site_information('admin_email'))->option_value;
    $address = bp_option('biz_address');
    $hours   = bp_option('biz_hours');
    $mapEmbed = bp_option('biz_map_embed') ?: ($address ? 'https://www.google.com/maps?q='.urlencode($address).'&output=embed' : null);
@endphp

<section id="contact" class="bz-section bz-section--alt">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_contact_title', $mm ? 'ကျွန်ုပ်တို့ကို ဆက်သွယ်ပါ' : 'Get in Touch') }}</h2>
            <p class="bz-muted mb-0">{{ bp_option('biz_contact_subtitle', $mm ? 'မေးခွန်း ရှိပါက ကျွန်ုပ်တို့ထံ စာပို့ပါ။' : 'Questions or feedback? We would love to hear from you.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-grid gap-3">
                    @if($phone)
                        <div class="d-flex gap-3"><span class="bz-ico flex-shrink-0"><i class="bi bi-telephone"></i></span>
                            <div><h6 class="mb-0">{{ $mm ? 'ဖုန်း' : 'Phone' }}</h6><a class="bz-muted" href="tel:{{ str_replace([' ', '-', '(', ')'], '', $phone) }}">{{ $phone }}</a></div></div>
                    @endif
                    @if($email)
                        <div class="d-flex gap-3"><span class="bz-ico flex-shrink-0"><i class="bi bi-envelope"></i></span>
                            <div><h6 class="mb-0">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</h6><a class="bz-muted" href="mailto:{{ $email }}">{{ $email }}</a></div></div>
                    @endif
                    @if($address)
                        <div class="d-flex gap-3"><span class="bz-ico flex-shrink-0"><i class="bi bi-geo-alt"></i></span>
                            <div><h6 class="mb-0">{{ $mm ? 'လိပ်စာ' : 'Address' }}</h6><span class="bz-muted">{{ $address }}</span></div></div>
                    @endif
                    @if($hours)
                        <div class="d-flex gap-3"><span class="bz-ico flex-shrink-0"><i class="bi bi-clock"></i></span>
                            <div><h6 class="mb-0">{{ $mm ? 'ဖွင့်ချိန်' : 'Business Hours' }}</h6><span class="bz-muted">{!! nl2br(e($hours)) !!}</span></div></div>
                    @endif
                </div>

                @if($mapEmbed)
                    <div class="mt-4 rounded overflow-hidden" style="border:1px solid var(--bz-border);">
                        <iframe src="{{ $mapEmbed }}" width="100%" height="220" style="border:0;" loading="lazy" title="Map" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @endif
            </div>

            <div class="col-lg-7">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                @if(bp_option('feedback_enabled', 'yes') === 'yes')
                    <form method="POST" action="{{ url('/contact') }}" class="bz-card p-4">
                        {{ csrf_field() }}
                        <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $mm ? 'အမည်' : 'Name' }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ $mm ? 'ခေါင်းစဉ်' : 'Subject' }}</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ $mm ? 'မက်ဆေ့ချ်' : 'Message' }} <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">{{ $mm ? 'စာပို့ရန်' : 'Send message' }}</button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="bz-muted">{{ $mm ? 'ဆက်သွယ်ရန် ဖောင်ကို ယာယီ ပိတ်ထားပါသည်။' : 'The contact form is currently unavailable.' }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
