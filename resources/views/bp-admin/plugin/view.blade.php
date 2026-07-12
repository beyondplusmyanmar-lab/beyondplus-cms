@extends('bp-admin.layouts.admin.index')

@section('title', $plugin['name'])

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .pv-meta th { color:#64748b; font-weight:600; white-space:nowrap; width:170px; padding:8px 12px 8px 0; vertical-align:top; }
    .pv-meta td { padding:8px 0; color:#1e293b; }
    .pv-cap { display:inline-flex; align-items:center; gap:6px; padding:.3rem .7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; color:#334155; margin:0 6px 6px 0; }
    .pv-cap.on { border-color:#c7d2fe; background:#eef2ff; color:#4338ca; }
    .pv-perm { font-family:monospace; font-size:.8rem; background:#f1f5f9; border-radius:6px; padding:.15rem .45rem; margin-right:6px; }
</style>

<a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="fa fa-arrow-left"></i> {{ $mm ? 'ပလပ်အင်အားလုံး' : 'All plugins' }}</a>

<div class="row">
    <div class="col-lg-8 tile">
        <div class="box box-danger">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h3 class="mb-0"><i class="fa fa-plug text-muted"></i> {{ $plugin['name'] }}</h3>
                    <span>
                        @if($plugin['active'])<span class="badge badge-success">{{ $mm ? 'အသုံးပြုဆဲ' : 'Active' }}</span>@else<span class="badge badge-secondary">{{ $mm ? 'ပိတ်ထား' : 'Inactive' }}</span>@endif
                        @if($plugin['tampered'])<span class="badge badge-danger"><i class="fa fa-exclamation-triangle"></i> {{ $mm ? 'ပြင်ဆင်ထား' : 'Modified' }}</span>@endif
                        @if($plugin['update_available'])<span class="badge badge-warning"><i class="fa fa-arrow-up"></i> {{ $mm ? 'အဆင့်မြှင့်' : 'Update' }} {{ $plugin['installed_version'] }} → {{ $plugin['version'] }}</span>@endif
                    </span>
                </div>
                <p class="text-muted">{{ $plugin['description'] }}</p>

                @if($failure)
                    <div class="alert alert-warning"><strong>{{ $mm ? 'ပြန်လည်ကယ်ဆယ်ရေး mode —' : 'Recovery mode:' }}</strong> {{ $mm ? 'load မအောင်မြင်၍ အလိုအလျောက် ပိတ်ထားသည် —' : 'auto-disabled after failing to load —' }} {{ $failure }}</div>
                @endif

                <h6 class="text-uppercase text-muted mt-4" style="letter-spacing:.06em; font-size:.75rem;">{{ $mm ? 'အသေးစိတ်' : 'Details' }}</h6>
                <table class="pv-meta w-100 mb-4">
                    <tr><th>{{ $mm ? 'ဗားရှင်း' : 'Version' }}</th><td>{{ $plugin['version'] }}</td></tr>
                    <tr><th>{{ $mm ? 'ရေးသားသူ' : 'Author' }}</th><td>{{ $plugin['author'] ?: '—' }}</td></tr>
                    <tr><th>{{ $mm ? 'အမျိုးအစား' : 'Category' }}</th><td>{{ $plugin['category'] }}</td></tr>
                    @if($plugin['homepage'])<tr><th>{{ $mm ? 'ဝဘ်စာမျက်နှာ' : 'Homepage' }}</th><td><a href="{{ $plugin['homepage'] }}" target="_blank" rel="noopener">{{ $plugin['homepage'] }} <i class="fa fa-external-link"></i></a></td></tr>@endif
                    <tr><th>{{ $mm ? 'လိုင်စင်' : 'License' }}</th><td>{{ $plugin['license'] ?: '—' }}</td></tr>
                    <tr><th>Slug</th><td><code>{{ $plugin['slug'] }}</code></td></tr>
                    <tr><th>{{ $mm ? 'လိုအပ်ချက်' : 'Requires' }}</th><td>
                        CMS &ge; {{ $plugin['minCmsVersion'] ?: '—' }}
                        @if(!empty($meta['requires']['php'])) · PHP &ge; {{ $meta['requires']['php'] }} @endif
                        @if(!empty($meta['requires']['extensions'])) · ext: {{ implode(', ', $meta['requires']['extensions']) }} @endif
                    </td></tr>
                    <tr><th>{{ $mm ? 'ခွင့်ပြုချက်များ' : 'Permissions' }}</th><td>
                        @forelse(($meta['permissions'] ?? []) as $perm)<span class="pv-perm">{{ $perm }}</span>@empty<span class="text-muted">{{ $mm ? 'ကြေညာထားခြင်း မရှိပါ' : 'none declared' }}</span>@endforelse
                    </td></tr>
                </table>

                <h6 class="text-uppercase text-muted" style="letter-spacing:.06em; font-size:.75rem;">{{ $mm ? 'စွမ်းဆောင်ရည်များ' : 'Capabilities' }}</h6>
                <div class="mb-4">
                    <span class="pv-cap {{ $plugin['migrations'] ? 'on' : '' }}"><i class="fa fa-database"></i> {{ $mm ? 'Database migration များ' : 'Database migrations' }} {{ $plugin['migrations'] ? '✓' : '—' }}</span>
                    <span class="pv-cap {{ $plugin['settings'] ? 'on' : '' }}"><i class="fa fa-sliders"></i> {{ $mm ? 'ဆက်တင်' : 'Settings' }} {{ $plugin['settings'] ? '✓' : '—' }}</span>
                    <span class="pv-cap {{ !empty($meta['test']) ? 'on' : '' }}"><i class="fa fa-paper-plane"></i> {{ $mm ? 'စမ်းသပ်ချက်' : 'Test action' }} {{ !empty($meta['test']) ? '✓' : '—' }}</span>
                    <span class="pv-cap {{ !empty($meta['admin_menu']) ? 'on' : '' }}"><i class="fa fa-list"></i> {{ $mm ? 'Admin စာမျက်နှာ' : 'Admin page' }} {{ !empty($meta['admin_menu']) ? '✓' : '—' }}</span>
                </div>

                <h6 class="text-uppercase text-muted" style="letter-spacing:.06em; font-size:.75rem;">{{ $mm ? 'လုံခြုံရေး စစ်ဆေးမှု' : 'Security scan' }}</h6>
                @if(!empty($scan['critical']))
                    <div class="alert alert-danger py-2 mb-2"><i class="fa fa-ban"></i> {{ count($scan['critical']) }} {{ $mm ? 'ခု ပြင်းထန် — activation ကို ပိတ်ပင်ထားသည်။' : 'critical — activation is blocked.' }} <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}">{{ $mm ? 'အစီရင်ခံစာ ကြည့်ရန်' : 'View report' }}</a></div>
                @elseif(!empty($scan['warning']))
                    <div class="alert alert-warning py-2 mb-2"><i class="fa fa-exclamation-triangle"></i> {{ count($scan['warning']) }} {{ $mm ? 'ခု သတိပေးချက်။' : 'warning(s).' }} <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}">{{ $mm ? 'အစီရင်ခံစာ ကြည့်ရန်' : 'View report' }}</a></div>
                @else
                    <div class="alert alert-success py-2 mb-2"><i class="fa fa-check"></i> {{ $mm ? 'အန္တရာယ်ရှိသော ပုံစံ မတွေ့ပါ။' : 'No risky patterns found.' }} <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}">{{ $mm ? 'အစီရင်ခံစာ ကြည့်ရန်' : 'View report' }}</a></div>
                @endif
                @if($requirements)
                    <div class="alert alert-danger py-2"><strong>{{ $mm ? 'မတွဲဘက်နိုင်ပါ —' : 'Incompatible:' }}</strong> {{ implode('; ', $requirements) }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 tile">
        <div class="box box-danger">
            <div class="box-body">
                <h6 class="text-uppercase text-muted mb-3" style="letter-spacing:.06em; font-size:.75rem;">{{ $mm ? 'လုပ်ဆောင်ချက်များ' : 'Actions' }}</h6>
                @component('bp-admin.inc.alert')@endcomponent

                @if($plugin['update_available'])
                    <form action="{{ url('bp-admin/plugins/update') }}" method="post">
                        {{ csrf_field() }}<input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button class="btn btn-sm btn-warning btn-block mb-2"><i class="fa fa-arrow-up"></i> {{ $plugin['version'] }} {{ $mm ? 'သို့ အဆင့်မြှင့်ရန်' : 'Update to' }}</button>
                    </form>
                @endif
                @if($plugin['settings'])
                    <a href="{{ url('bp-admin/plugins/settings?slug='.$plugin['slug']) }}" class="btn btn-sm btn-outline-primary btn-block mb-2"><i class="fa fa-cog"></i> {{ $mm ? 'ဆက်တင်' : 'Settings' }}</a>
                @endif
                <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}" class="btn btn-sm btn-outline-info btn-block mb-2"><i class="fa fa-shield"></i> {{ $mm ? 'လုံခြုံရေး စစ်ဆေးရန်' : 'Security scan' }}</a>

                @if($plugin['active'])
                    <form action="{{ url('bp-admin/plugins/deactivate') }}" method="post">
                        {{ csrf_field() }}<input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button class="btn btn-sm btn-outline-secondary btn-block mb-2">{{ $mm ? 'ပိတ်ရန်' : 'Deactivate' }}</button>
                    </form>
                @else
                    <form action="{{ url('bp-admin/plugins/activate') }}" method="post">
                        {{ csrf_field() }}<input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button class="btn btn-sm btn-success btn-block mb-2"><i class="fa fa-check"></i> {{ $mm ? 'ဖွင့်ရန်' : 'Activate' }}</button>
                    </form>
                    <form action="{{ url('bp-admin/plugins/uninstall') }}" method="post" onsubmit="return confirm('{{ $mm ? $plugin['name'].' ကို ဖြုတ်မှာ သေချာပါသလား။ ၎င်း၏ migration များ ပြန်ရုပ်သိမ်းပြီး data များ ဖျက်ပါမည်။' : 'Uninstall '.$plugin['name'].'? This rolls back its migrations and deletes its data.' }}')">
                        {{ csrf_field() }}<input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button class="btn btn-sm btn-outline-danger btn-block">{{ $mm ? 'ဖြုတ်ရန်' : 'Uninstall' }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
