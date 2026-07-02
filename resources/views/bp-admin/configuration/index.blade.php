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
                        <label class="control-label">Customer registration method</label>
                        <select class="form-control" name="registration_type">
                            @foreach (['phone' => 'Phone only', 'email' => 'Email only', 'both' => 'Phone &amp; Email'] as $val => $label)
                                <option value="{{ $val }}" {{ $config['registration_type'] === $val ? 'selected' : '' }}>{!! $label !!}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Which identifier new customers register with.</small>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">API</h3>
                <div class="tile-body">
                    <div class="form-group">
                        <label class="control-label">JSON API</label>
                        <select class="form-control" name="api_enabled">
                            <option value="yes" {{ $config['api_enabled'] === 'yes' ? 'selected' : '' }}>Enabled</option>
                            <option value="no" {{ $config['api_enabled'] === 'no' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        <small class="form-text text-muted">Toggle the token-protected JSON API.</small>
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
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-4">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save configuration</button>
    </div>
</form>
@stop
