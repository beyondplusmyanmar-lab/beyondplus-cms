@extends('bp-admin.layouts.admin.index')
@section('title', app()->getLocale() === 'mm' ? 'ချိန်ညှိမှု' : 'Configuration')
@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp

@if (Session::has('flash_message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ Session::get('flash_message') }}
        <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
    </div>
@endif

<div class="d-flex justify-content-end mb-3" style="gap:.4rem;">
    <a href="{{ url('bp-admin/configuration/system') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa fa-info-circle"></i> {{ $mm ? 'စနစ် နှင့် အပ်ဒိတ်များ' : 'System & updates' }}
    </a>
    <a href="{{ url('bp-admin/configuration/flow') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa fa-sitemap"></i> {{ $mm ? 'စနစ် ဆက်စပ်ပုံ ကြည့်ရန်' : 'View system flow' }}
    </a>
</div>

<form method="POST" action="{{ url('bp-admin/configuration') }}">
    {{ csrf_field() }}

    <div class="row">
        {{-- Registration + API --}}
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">{{ $mm ? 'အကောင့်ဖွင့်ခြင်း' : 'Registration' }}</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'ဖောက်သည် အကောင့်ဖွင့်ခြင်း' : 'Customer registration' }}</label>
                        <select class="form-control" name="registration_enabled">
                            <option value="yes" {{ $config['registration_enabled'] === 'yes' ? 'selected' : '' }}>{{ $mm ? 'ဖွင့် — မည်သူမဆို အကောင့်ဖွင့်နိုင်' : 'Open — anyone can sign up' }}</option>
                            <option value="no" {{ $config['registration_enabled'] === 'no' ? 'selected' : '' }}>{{ $mm ? 'ပိတ် — အကောင့်အသစ် မဖွင့်နိုင်' : 'Closed — no new sign-ups' }}</option>
                        </select>
                        <small class="form-text text-muted">{{ $mm ? 'ဖောက်သည်အသစ် အကောင့်ဖွင့်ခြင်းကို ရပ်ရန် ပိတ်ပါ။' : 'Turn off to stop new customer registrations.' }}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'ဖောက်သည် အကောင့်ဖွင့် နည်းလမ်း' : 'Customer registration method' }}</label>
                        <select class="form-control" name="registration_type">
                            @foreach (['phone' => $mm ? 'ဖုန်းဖြင့်သာ' : 'Phone only', 'email' => $mm ? 'အီးမေးလ်ဖြင့်သာ' : 'Email only', 'both' => $mm ? 'ဖုန်း &amp; အီးမေးလ်' : 'Phone &amp; Email'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['registration_type'] === $val ? 'selected' : '' }}>{!! $label !!}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ $mm ? 'ဖောက်သည်အသစ်များ မည်သည့်အရာဖြင့် အကောင့်ဖွင့်မည်။' : 'Which identifier new customers register with.' }}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'OTP ပို့ဆောင်မှု' : 'OTP delivery' }}</label>
                        <select class="form-control" name="otp_channel">
                            @foreach (['auto' => $mm ? 'အလိုအလျောက် (SMS ပြီးမှ email)' : 'Automatic (SMS, then email)', 'sms' => 'SMS (SMSPoh)', 'email' => 'Email (Mailgun)'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['otp_channel'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ $mm ? 'အတည်ပြုကုဒ်ကို မည်သည့် provider က ပို့မည်။ provider ကို ၎င်း၏ plugin စာမျက်နှာတွင် activate လုပ်၍ ချိန်ညှိပါ။ မရနိုင်ပါက log သို့ ပြန်ကျသည်။' : 'Which provider sends the verification code. Activate and configure the provider on its plugin page. Falls back to the log if unavailable.' }}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'အများသုံး FAQ စာမျက်နှာ' : 'Public FAQ page' }}</label>
                        <select class="form-control" name="faq_enabled">
                            <option value="yes" {{ $config['faq_enabled'] === 'yes' ? 'selected' : '' }}>{{ $mm ? 'ဖွင့် — /faq ပြသည်' : 'Enabled — show /faq' }}</option>
                            <option value="no" {{ $config['faq_enabled'] === 'no' ? 'selected' : '' }}>{{ $mm ? 'ပိတ်' : 'Disabled' }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">{{ $mm ? 'Contact ဖောင်' : 'Contact form' }}</label>
                        <select class="form-control" name="feedback_enabled">
                            <option value="yes" {{ $config['feedback_enabled'] === 'yes' ? 'selected' : '' }}>{{ $mm ? 'ဖွင့် — /contact တွင် ဖောင် ပြသည်' : 'Enabled — show the form on /contact' }}</option>
                            <option value="no" {{ $config['feedback_enabled'] === 'no' ? 'selected' : '' }}>{{ $mm ? 'ပိတ်' : 'Disabled' }}</option>
                        </select>
                        <small class="form-text text-muted">{{ $mm ? 'FAQ များနှင့် မက်ဆေ့ချ်များကို FAQ / Feedback admin စာမျက်နှာများမှ စီမံပါ။' : 'Manage FAQs and read messages from the FAQ / Feedback admin pages.' }}</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">{{ $mm ? 'API နှင့် App (headless / SPA)' : 'API & App (headless / SPA)' }}</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">JSON API</label>
                        <select class="form-control" name="api_enabled">
                            <option value="yes" {{ $config['api_enabled'] === 'yes' ? 'selected' : '' }}>{{ $mm ? 'ဖွင့်' : 'Enabled' }}</option>
                            <option value="no" {{ $config['api_enabled'] === 'no' ? 'selected' : '' }}>{{ $mm ? 'ပိတ်' : 'Disabled' }}</option>
                        </select>
                        <small class="form-text text-muted">{{ $mm ? 'mobile / SPA app ကို လုပ်ဆောင်ပေးသည်။ interactive docs —' : 'Powers the mobile / SPA app. Interactive docs at' }}
                            <a href="{{ url('api/documentation') }}" target="_blank">/api/documentation</a>.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'ရှေ့ဆုံး mode' : 'Front-end mode' }}</label>
                        <select class="form-control" name="frontend_mode">
                            @foreach (['theme' => $mm ? 'Server theme' : 'Server theme', 'spa' => $mm ? 'SPA သို့ redirect' : 'Redirect to SPA', 'headless' => $mm ? 'Headless (API သာ)' : 'Headless (API only)'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['frontend_mode'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{!! ($mm ? '<code>/</code> က ဘာ ပြသမည်: server theme သို့မဟုတ် ဧည့်သည်များကို SPA သို့ redirect။' : 'What <code>/</code> serves: the server theme, or redirect visitors to the SPA.') !!}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'App (SPA) URL' : 'App (SPA) URL' }}</label>
                        <input type="text" class="form-control" name="spa_url" value="{{ $config['spa_url'] }}"
                               placeholder="https://app.example.com">
                        <small class="form-text text-muted">{{ $mm ? 'သင့် headless / SPA ရှေ့ဆုံးကို မည်သည့်နေရာတွင် host လုပ်ထားသည် (link နှင့် redirect အတွက် သုံးသည်)။' : 'Where your headless / SPA front-end is hosted (used for links and redirects).' }}</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">{{ $mm ? 'ခွင့်ပြု API origins (CORS)' : 'Allowed API origins (CORS)' }}</label>
                        <textarea class="form-control" name="cors_origins" rows="2"
                                  placeholder="https://app.example.com, https://admin.example.com">{{ $config['cors_origins'] }}</textarea>
                        <small class="form-text text-muted">{!! ($mm ? 'ကော်မာ သို့မဟုတ် စာကြောင်းဖြင့် ခွဲပါ။ origin အားလုံး ခွင့်ပြုရန် ဗလာ ထားပါ (<code>*</code>)။' : 'Comma or line separated. Leave blank to allow all origins (<code>*</code>).') !!}</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">{{ $mm ? 'Admin login လုံခြုံရေး' : 'Admin login security' }}</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'ခိုင်မာသော login လမ်းကြောင်း' : 'Hardened login path' }}</label>
                        <div class="input-group">
                            <span class="input-group-text">/bp-admin/</span>
                            <input type="text" class="form-control" name="admin_login_path" value="{{ $config['admin_login_path'] }}" placeholder="secret-door" autocomplete="off">
                        </div>
                        @if($config['admin_login_path'])
                            <small class="form-text text-success">{!! ($mm ? '<i class="fa fa-shield"></i> တကယ့် login: <code>'.url('bp-admin/'.$config['admin_login_path']).'</code>။ <code>/bp-admin/login</code> သည် ယခု အမြဲ ငြင်းပယ်သော အထင်အမြင်လွဲ (decoy) ဖြစ်သည် — <strong>တကယ့် URL ကို bookmark လုပ်ပါ</strong>။' : '<i class="fa fa-shield"></i> Real login: <code>'.url('bp-admin/'.$config['admin_login_path']).'</code>. <code>/bp-admin/login</code> is now a decoy that always rejects — <strong>bookmark the real URL</strong>.') !!}</small>
                        @else
                            <small class="form-text text-muted">{!! ($mm ? 'တကယ့် admin login ကို လျှို့ဝှက် slug သို့ ရွှေ့ပါ။ <code>/bp-admin/login</code> သည် UI ကို ဆက်ထားသော်လည်း အမြဲ "invalid credentials" ပြန်ပေးသည်။ ဗလာ = ပိတ်။ (စာလုံး၊ ဂဏန်း၊ dash)' : 'Move the real admin login to a secret slug. <code>/bp-admin/login</code> keeps its UI but always returns “invalid credentials”. Blank = disabled. (Letters, numbers, dashes.)') !!}</small>
                        @endif
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">{{ $mm ? 'Developer IP ခွင့်ပြုစာရင်း' : 'Developer IP allow-list' }}</label>
                        <textarea class="form-control" name="developer_ips" rows="2" placeholder="203.0.113.4, 10.0.0.0/24">{{ $config['developer_ips'] }}</textarea>
                        <small class="form-text text-muted">{!! ($mm ? 'IP / IPv4 CIDR ranges — <code>500</code> စာမျက်နှာတွင် အသေးစိတ် error (developer log) မြင်ခွင့် (signed-in admin များအပြင်)။ ကော်မာ သို့မဟုတ် စာကြောင်းဖြင့် ခွဲပါ။ သင့်လက်ရှိ IP: <code>'.request()->ip().'</code>။' : 'IPs / IPv4 CIDR ranges allowed to see the detailed error (developer log) on a <code>500</code> page — in addition to signed-in admins. Comma or line separated. Your current IP: <code>'.request()->ip().'</code>.') !!}</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">{{ $mm ? 'Core အပ်ဒိတ်များ' : 'Core updates' }}</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">{{ $mm ? 'Core အပ်ဒိတ် စစ်ဆေးရန်' : 'Check for core updates' }}</label>
                        <select class="form-control" name="update_check">
                            <option value="yes" {{ $config['update_check'] === 'yes' ? 'selected' : '' }}>{{ $mm ? 'ဖွင့်' : 'Enabled' }}</option>
                            <option value="no" {{ $config['update_check'] === 'no' ? 'selected' : '' }}>{{ $mm ? 'ပိတ်' : 'Disabled' }}</option>
                        </select>
                        <small class="form-text text-muted">{{ $mm ? "project ၏ GitHub releases တွင် ဗားရှင်းအသစ် ရှိမရှိ စစ်ဆေးသည်။" : "Check the project's GitHub releases for a newer version." }}</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">{{ $mm ? 'အပ်ဒိတ် repository' : 'Update repository' }}</label>
                        <input type="text" class="form-control" name="update_repo" value="{{ $config['update_repo'] }}" placeholder="beyondplusmyanmar-lab/beyondplus-cms">
                        <small class="form-text text-muted">{!! ($mm ? 'စစ်ဆေးရန် GitHub <code>owner/repo</code>။ ဗလာ = default project repo။ <a href="'.url('bp-admin/configuration/system').'">စနစ် နှင့် အပ်ဒိတ်များ</a> ကို ကြည့်ပါ။' : 'GitHub <code>owner/repo</code> to check. Blank = the default project repo. See <a href="'.url('bp-admin/configuration/system').'">System &amp; updates</a>.') !!}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- SMS & email providers are configured on their own plugin pages now --}}
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">{{ $mm ? 'Providers' : 'Providers' }}</h3>
                <div class="tile-body">
                    <p class="text-muted mb-3">{!! ($mm ? 'SMS နှင့် email ကို <strong>provider plugin</strong> များက ပို့ဆောင်သည်။ တစ်ခုစီကို ၎င်း၏ စာမျက်နှာ (Plugins &rarr; Settings) တွင် ချိန်ညှိ၍ စမ်းသပ်ပါ။' : 'SMS and email are delivered by <strong>provider plugins</strong>. Configure and test each one on its own page (Plugins &rarr; Settings).') !!}</p>
                    <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-plug"></i> {{ $mm ? 'ပလပ်အင်များ ဖွင့်ရန်' : 'Open Plugins' }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-4">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ $mm ? 'ချိန်ညှိမှု သိမ်းရန်' : 'Save configuration' }}</button>
    </div>
</form>

@stop
