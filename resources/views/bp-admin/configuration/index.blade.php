@extends('bp-admin.layouts.admin.index')
@section('title', 'Configuration')
@section('content')

@if (Session::has('flash_message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ Session::get('flash_message') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<form method="POST" action="{{ url('bp-admin/configuration') }}">
    {{ csrf_field() }}

    <div class="row">
        {{-- Registration + API --}}
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">Registration</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">Customer registration</label>
                        <select class="form-control" name="registration_enabled">
                            <option value="yes" {{ $config['registration_enabled'] === 'yes' ? 'selected' : '' }}>Open — anyone can sign up</option>
                            <option value="no" {{ $config['registration_enabled'] === 'no' ? 'selected' : '' }}>Closed — no new sign-ups</option>
                        </select>
                        <small class="form-text text-muted">Turn off to stop new customer registrations.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Customer registration method</label>
                        <select class="form-control" name="registration_type">
                            @foreach (['phone' => 'Phone only', 'email' => 'Email only', 'both' => 'Phone &amp; Email'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['registration_type'] === $val ? 'selected' : '' }}>{!! $label !!}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Which identifier new customers register with.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">OTP delivery</label>
                        <select class="form-control" name="otp_channel">
                            @foreach (['auto' => 'Automatic (SMS, then email)', 'sms' => 'SMS (SMSPoh)', 'email' => 'Email (Mailgun)'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['otp_channel'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Which provider sends the verification code. Activate and configure the provider on its plugin page. Falls back to the log if unavailable.</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">API &amp; App (headless / SPA)</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">JSON API</label>
                        <select class="form-control" name="api_enabled">
                            <option value="yes" {{ $config['api_enabled'] === 'yes' ? 'selected' : '' }}>Enabled</option>
                            <option value="no" {{ $config['api_enabled'] === 'no' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        <small class="form-text text-muted">Powers the mobile / SPA app. Interactive docs at
                            <a href="{{ url('api/documentation') }}" target="_blank">/api/documentation</a>.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Front-end mode</label>
                        <select class="form-control" name="frontend_mode">
                            @foreach (['theme' => 'Server theme', 'spa' => 'Redirect to SPA', 'headless' => 'Headless (API only)'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['frontend_mode'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">What <code>/</code> serves: the server theme, or redirect visitors to the SPA.</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">App (SPA) URL</label>
                        <input type="text" class="form-control" name="spa_url" value="{{ $config['spa_url'] }}"
                               placeholder="https://app.example.com">
                        <small class="form-text text-muted">Where your headless / SPA front-end is hosted (used for links and redirects).</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">Allowed API origins (CORS)</label>
                        <textarea class="form-control" name="cors_origins" rows="2"
                                  placeholder="https://app.example.com, https://admin.example.com">{{ $config['cors_origins'] }}</textarea>
                        <small class="form-text text-muted">Comma or line separated. Leave blank to allow all origins (<code>*</code>).</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">Admin login security</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">Hardened login path</label>
                        <div class="input-group">
                            <span class="input-group-text">/bp-admin/</span>
                            <input type="text" class="form-control" name="admin_login_path" value="{{ $config['admin_login_path'] }}" placeholder="secret-door" autocomplete="off">
                        </div>
                        @if($config['admin_login_path'])
                            <small class="form-text text-success"><i class="fa fa-shield"></i> Real login: <code>{{ url('bp-admin/'.$config['admin_login_path']) }}</code>. <code>/bp-admin/login</code> is now a decoy that always rejects — <strong>bookmark the real URL</strong>.</small>
                        @else
                            <small class="form-text text-muted">Move the real admin login to a secret slug. <code>/bp-admin/login</code> keeps its UI but always returns “invalid credentials”. Blank = disabled. (Letters, numbers, dashes.)</small>
                        @endif
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">Developer IP allow-list</label>
                        <textarea class="form-control" name="developer_ips" rows="2" placeholder="203.0.113.4, 10.0.0.0/24">{{ $config['developer_ips'] }}</textarea>
                        <small class="form-text text-muted">IPs / IPv4 CIDR ranges allowed to see the detailed error (developer log) on a <code>500</code> page — in addition to signed-in admins. Comma or line separated. Your current IP: <code>{{ request()->ip() }}</code>.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- SMS & email providers are configured on their own plugin pages now --}}
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">Providers</h3>
                <div class="tile-body">
                    <p class="text-muted mb-3">SMS and email are delivered by <strong>provider plugins</strong>. Configure and test each one on its own page (Plugins &rarr; Settings).</p>
                    <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-plug"></i> Open Plugins</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-4">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save configuration</button>
    </div>
</form>

@stop
