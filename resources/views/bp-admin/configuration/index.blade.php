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
                        <small class="form-text text-muted">Which provider sends the verification code. Enable and configure it in its box below. Falls back to the log if unavailable.</small>
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
        </div>

        {{-- SMS + Email --}}
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">SMS Gateway</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <select class="form-control" name="sms_enabled">
                            <option value="yes" {{ $config['sms_enabled'] === 'yes' ? 'selected' : '' }}>Enabled</option>
                            <option value="no" {{ $config['sms_enabled'] === 'no' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Provider</label>
                        <select class="form-control" name="sms_provider">
                            <option value="smspoh" {{ $config['sms_provider'] === 'smspoh' ? 'selected' : '' }}>SMSPoh</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sender name</label>
                        <input type="text" class="form-control" name="sms_sender" value="{{ $config['sms_sender'] }}">
                    </div>
                    <div class="form-group">
                        <label class="control-label">API token</label>
                        <input type="password" class="form-control" name="sms_api_token" autocomplete="new-password"
                               placeholder="{{ $config['sms_api_token'] ? '•••••••• (leave blank to keep)' : 'Not set' }}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">Send a test SMS <small class="text-muted">(uses saved credentials)</small></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="sms_test_to" placeholder="Phone number">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" id="sms_test_btn">Send test</button>
                            </div>
                        </div>
                        <small id="sms_test_result" class="form-text"></small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">Email (Mailgun)</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <select class="form-control" name="mail_enabled">
                            <option value="yes" {{ $config['mail_enabled'] === 'yes' ? 'selected' : '' }}>Enabled</option>
                            <option value="no" {{ $config['mail_enabled'] === 'no' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Provider</label>
                        <select class="form-control" name="mail_provider">
                            <option value="mailgun" {{ $config['mail_provider'] === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mailgun domain</label>
                        <input type="text" class="form-control" name="mailgun_domain" value="{{ $config['mailgun_domain'] }}" placeholder="mg.example.com">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mailgun secret</label>
                        <input type="password" class="form-control" name="mailgun_secret" autocomplete="new-password"
                               placeholder="{{ $config['mailgun_secret'] ? '•••••••• (leave blank to keep)' : 'Not set' }}">
                    </div>
                    <div class="form-group">
                        <label class="control-label">From address</label>
                        <input type="email" class="form-control" name="mail_from" value="{{ $config['mail_from'] }}" placeholder="no-reply@example.com">
                    </div>
                    <div class="form-group mb-0">
                        <label class="control-label">Send a test email <small class="text-muted">(uses saved credentials)</small></label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="mail_test_to" placeholder="you@example.com">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" id="mail_test_btn">Send test</button>
                            </div>
                        </div>
                        <small id="mail_test_result" class="form-text"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-4">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save configuration</button>
    </div>
</form>

@push('scripts')
<script>
$(function () {
    function runTest(url, toId, resultId) {
        var to = $('#' + toId).val();
        var $r = $('#' + resultId).text('Sending…').css('color', '');
        $.post(url, { to: to }, function (res) {
            $r.text(res.message).css('color', res.ok ? '#2e7d32' : '#c62828');
        }).fail(function () {
            $r.text('Request failed.').css('color', '#c62828');
        });
    }
    $('#sms_test_btn').on('click', function () {
        runTest('{{ url("bp-admin/configuration/test-sms") }}', 'sms_test_to', 'sms_test_result');
    });
    $('#mail_test_btn').on('click', function () {
        runTest('{{ url("bp-admin/configuration/test-email") }}', 'mail_test_to', 'mail_test_result');
    });
});
</script>
@endpush
@stop
