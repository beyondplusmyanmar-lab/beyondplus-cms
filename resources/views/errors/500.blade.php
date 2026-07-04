@extends('errors.layout')

@section('code', '500')
@section('icon', 'bi-bug')

@php
    // Show the real error to developers only — a signed-in admin, or a request
    // from an allow-listed developer IP (managed in Configuration). Never to the
    // public, even in production where debug is off and this branded page shows.
    $bpIsDeveloper = false;
    try {
        $bpIsDeveloper = auth()->guard('admins')->check()
            || bp_ip_allowed(request()->ip(), bp_option('developer_ips', ''));
    } catch (\Throwable $e) {}
    // In production Laravel wraps the original throwable in an HttpException(500);
    // unwrap it so the log shows the actual cause.
    $bpError = isset($exception) ? ($exception->getPrevious() ?: $exception) : null;
@endphp

@if($bpIsDeveloper && $bpError)
    @section('log')
        <div class="bp-log">
            <div class="bp-log__head">
                <i class="bi bi-terminal"></i> {{ config('app.name') }} — developer log
                <span class="badge ms-auto">{{ class_basename($bpError) }}</span>
            </div>
            <div class="bp-log__body">
                <div class="bp-log__msg">{{ $bpError->getMessage() ?: '(no message)' }}</div>
                <div class="bp-log__loc">{{ $bpError->getFile() }}:{{ $bpError->getLine() }}</div>
                <pre class="bp-log__trace">{{ \Illuminate\Support\Str::limit($bpError->getTraceAsString(), 4000) }}</pre>
            </div>
        </div>
    @endsection
@endif
