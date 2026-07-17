@extends('bp-admin.layouts.admin.index')

@section('title', 'DOEH Setup')

@section('content')
@php
    $steps = [1 => 'Plugins', 2 => 'Theme', 3 => 'Branding', 4 => 'Commerce key', 5 => 'Customer sign-in', 6 => 'Done'];
    $done = [
        1 => $state['plugins'],
        2 => $state['theme'],
        3 => $state['brand'],
        4 => $state['commerce'],
        5 => $state['identity'] || $state['identity_skipped'],
        6 => false,
    ];
@endphp
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-magic"></i> DOEH Setup</h4>
                <small class="text-muted">From empty CMS to a storefront taking real DOEH orders. Re-run any step at any time.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent
                @if ($errors->any())
                    <div class="alert alert-warning">{{ $errors->first() }}</div>
                @endif

                <ul class="nav nav-pills mb-4" style="gap:.25rem;">
                    @foreach ($steps as $n => $label)
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ $n === $step ? 'active' : '' }}" href="{{ url('bp-admin/doeh-setup?step='.$n) }}">
                                {{ $n }}. {{ $label }} @if($done[$n]) <i class="fa fa-check text-success"></i> @endif
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- ── Step 1: plugins ─────────────────────────────────────── --}}
                @if ($step === 1)
                    <p>A DOEH storefront needs three plugins: <strong>DOEH Identity</strong> (customer sign-in — configuring it is optional, but the theme requires the plugin), <strong>DOEH Commerce</strong> (the Orders API connector) and <strong>DOEH Commerce Storefront</strong> (the shop → cart → checkout flow).</p>
                    @if ($state['plugins'])
                        <div class="alert alert-success">All three are active.</div>
                        <a class="btn btn-primary btn-sm" href="{{ url('bp-admin/doeh-setup?step=2') }}">Continue →</a>
                    @else
                        <div class="alert alert-info">Missing: {{ implode(', ', $state['missing_plugins']) }}</div>
                        <form method="POST" action="{{ url('bp-admin/doeh-setup/plugins') }}">@csrf
                            <button class="btn btn-primary btn-sm" type="submit">Activate the DOEH plugins</button>
                        </form>
                    @endif

                {{-- ── Step 2: theme ───────────────────────────────────────── --}}
                @elseif ($step === 2)
                    <p>The theme is your <strong>business model</strong> — it decides the storefront's shape and which fulfilment options customers see. You can restyle everything later in <em>Themes → Customize</em>.</p>
                    <form method="POST" action="{{ url('bp-admin/doeh-setup/theme') }}">@csrf
                        <div class="row g-3 mb-3">
                            @foreach ($themes as $t)
                                <div class="col-md-6">
                                    <label class="d-block border rounded p-3 h-100" style="cursor:pointer; {{ $t['active'] ? 'border-color:#0d6efd; background:#f6f9ff;' : '' }}">
                                        <div class="d-flex align-items-center" style="gap:.5rem;">
                                            <input type="radio" name="theme" value="{{ $t['slug'] }}" @checked($t['active'])>
                                            <strong>{{ $t['name'] }}</strong>
                                            @if ($t['active'])<span class="badge bg-primary">current</span>@endif
                                        </div>
                                        <div class="small text-muted mt-1" style="max-height:60px; overflow:hidden;">{{ \Illuminate\Support\Str::limit($t['description'], 140) }}</div>
                                        <div class="mt-2">
                                            @forelse ($t['fulfillment'] as $ft)
                                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_',' ',$ft)) }}</span>
                                            @empty
                                                <span class="badge bg-light text-dark border">Appointment-style (no fulfilment)</span>
                                            @endforelse
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">Use this theme →</button>
                    </form>

                {{-- ── Step 3: branding ────────────────────────────────────── --}}
                @elseif ($step === 3)
                    @php $brandFields = array_values(array_filter(\App\Support\Theme::settingsSchema($state['theme_slug']), fn ($f) => ($f['group'] ?? '') === 'Brand')); @endphp
                    <p>Brand the <strong>{{ $state['theme_slug'] }}</strong> theme. Everything else (hero copy, sections, grid) lives in <a href="{{ url('bp-admin/themes') }}">Themes → Customize</a>.</p>
                    <form method="POST" action="{{ url('bp-admin/doeh-setup/brand') }}" style="max-width:520px;">@csrf
                        @foreach ($brandFields as $f)
                            @php $name = $f['name']; $val = bp_option($name, $f['default'] ?? ''); $type = $f['type'] ?? 'text'; @endphp
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">{{ $f['label'] ?? $name }}</label>
                                @if ($type === 'color')
                                    <input type="color" name="{{ $name }}" value="{{ $val ?: '#000000' }}" class="form-control form-control-color">
                                @elseif ($type === 'image')
                                    <input type="text" name="{{ $name }}" value="{{ $val }}" class="form-control form-control-sm" placeholder="{{ $f['placeholder'] ?? 'uploads path or full URL' }}">
                                @else
                                    <input type="text" name="{{ $name }}" value="{{ $val }}" class="form-control form-control-sm" placeholder="{{ $f['placeholder'] ?? '' }}">
                                @endif
                                @if (! empty($f['help']))<small class="form-text text-muted">{{ $f['help'] }}</small>@endif
                            </div>
                        @endforeach
                        <button class="btn btn-primary btn-sm" type="submit">Save branding →</button>
                    </form>

                {{-- ── Step 4: commerce key ────────────────────────────────── --}}
                @elseif ($step === 4)
                    <p>Paste this shop's <strong>merchant secret key</strong> from the DOEH developer portal. The wizard proves it against the live Orders API before saving — a key that fails is never stored. It stays server-side and is never shown again.</p>
                    @if ($state['commerce'])
                        <div class="alert alert-success">A key is configured ({{ $state['commerce_env'] }}). Submitting a new one replaces it.</div>
                    @endif
                    <form method="POST" action="{{ url('bp-admin/doeh-setup/commerce') }}" style="max-width:520px;">@csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Environment</label>
                            <select name="environment" class="form-select form-select-sm">
                                <option value="sandbox" @selected($state['commerce_env'] !== 'production')>Sandbox (sk_test_…)</option>
                                <option value="production" @selected($state['commerce_env'] === 'production')>Production (sk_live_…)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Merchant secret key</label>
                            <input type="password" name="secret_key" class="form-control form-control-sm" placeholder="sk_…" autocomplete="off">
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">Verify &amp; save →</button>
                        @if ($state['commerce'])
                            <a class="btn btn-outline-secondary btn-sm" href="{{ url('bp-admin/doeh-setup?step=5') }}">Keep current key →</a>
                        @endif
                    </form>

                {{-- ── Step 5: identity (optional) ─────────────────────────── --}}
                @elseif ($step === 5)
                    <p><strong>Optional.</strong> Customer sign-in (DOEH Identity) adds accounts and a live rewards balance to the storefront. Commerce works fine without it — guests can order. You need a <strong>client id</strong> (app_…) and <strong>publishable key</strong> (pk_…) from the DOEH developer portal.</p>
                    @if ($state['identity'])
                        <div class="alert alert-success">Customer sign-in is configured. Verify it with a real sign-in on your storefront.</div>
                    @endif
                    <form method="POST" action="{{ url('bp-admin/doeh-setup/identity') }}" style="max-width:520px;">@csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Environment</label>
                            <select name="environment" class="form-select form-select-sm">
                                <option value="sandbox" @selected($state['commerce_env'] !== 'production')>Sandbox (pk_test_…)</option>
                                <option value="production" @selected($state['commerce_env'] === 'production')>Production (pk_live_…)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Client id</label>
                            <input type="text" name="client_id" class="form-control form-control-sm" placeholder="app_…" value="{{ bp_plugin_option('doeh-identity', 'client_id') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Publishable key</label>
                            <input type="text" name="publishable_key" class="form-control form-control-sm" placeholder="pk_…" value="{{ bp_plugin_option('doeh-identity', 'publishable_key') }}">
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit" name="action" value="save">Save →</button>
                        <button class="btn btn-outline-secondary btn-sm" type="submit" name="action" value="skip">Skip for now →</button>
                    </form>

                {{-- ── Step 6: done ────────────────────────────────────────── --}}
                @else
                    @php $allCore = $state['plugins'] && $state['theme'] && $state['commerce']; @endphp
                    @if ($allCore)
                        <div class="alert alert-success"><strong>Your storefront is live.</strong> Orders placed on it are real DOEH orders — manage them in the Orders dashboard.</div>
                    @else
                        <div class="alert alert-warning">Not finished yet — the checklist below shows what is left.</div>
                    @endif
                    <ul class="list-unstyled" style="line-height:2;">
                        <li>{!! $state['plugins'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!} DOEH plugins active</li>
                        <li>{!! $state['theme'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!} Storefront theme: <strong>{{ $state['theme_slug'] }}</strong></li>
                        <li>{!! $state['brand'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-muted"></i>' !!} Branding saved</li>
                        <li>{!! $state['commerce'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!} Merchant key verified ({{ $state['commerce_env'] }})</li>
                        <li>{!! $state['identity'] ? '<i class="fa fa-check text-success"></i> Customer sign-in configured' : '<i class="fa fa-minus text-muted"></i> Customer sign-in '.($state['identity_skipped'] ? 'skipped (add it any time from this wizard)' : 'not configured') !!}</li>
                    </ul>
                    <div class="mt-3" style="display:flex; gap:.5rem; flex-wrap:wrap;">
                        <a class="btn btn-primary btn-sm" href="{{ url('/') }}" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Open your storefront</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ url('/store') }}" target="_blank" rel="noopener">Open the shop page</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ url('bp-admin/doeh-orders') }}">Orders dashboard</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ url('bp-admin/themes') }}">Customize the theme</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
