@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'ပလပ်အင်များ' : 'Plugins')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .plugin-card { border: 1px solid #e5e7eb; border-radius: 10px; height: 100%; transition: box-shadow .15s ease; }
    .plugin-card:hover { box-shadow: 0 .5rem 1.2rem rgba(0,0,0,.08); }
    .plugin-card.active { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,.2); }
    .plugin-meta { font-size: .78rem; color: #6b7280; }
    .plugin-empty { border: 1px dashed #d1d5db; border-radius: 8px; }
    .plugin-category { text-transform: uppercase; letter-spacing: .06em; font-size: .76rem; font-weight: 700; color: #64748b; margin: 6px 0 14px; padding-bottom: 8px; border-bottom: 1px solid #eef0f3; }
    .plugin-category span { color: #94a3b8; font-weight: 500; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                <div>
                    <h4 class="mb-0">{{ $mm ? 'ပလပ်အင်များ' : 'Plugins' }}</h4>
                    <small class="text-muted">
                        {{ $mm ? 'CMS ကို add-on များဖြင့် တိုးချဲ့ပါ။ plugin ဖိုလ်ဒါကို' : 'Extend the CMS with add-ons. Drop a plugin folder in' }}
                        <code>/plugins</code>
                        {{ $mm ? 'အောက်တွင် ထည့်ပြီး ဤနေရာတွင် activate လုပ်ပါ။' : ', then activate it here.' }}
                    </small>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap:.6rem;">
                    <a href="{{ url('bp-admin/configuration/flow') }}" class="btn btn-sm btn-outline-secondary" title="{{ $mm ? 'ဤပလပ်အင်များ ဝန်ဆောင်မှုများကို မည်သို့ လမ်းကြောင်းချသည်ကို ကြည့်ရန်' : 'See how these plugins route services' }}"><i class="fa fa-sitemap"></i> {{ $mm ? 'စနစ် ဆက်စပ်ပုံ' : 'System flow' }}</a>
                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" id="pluginSearch" class="form-control" placeholder="{{ $mm ? 'ပလပ်အင် ရှာရန်…' : 'Search plugins…' }}" autocomplete="off">
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                @if(!empty($failures))
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-life-ring"></i> {{ $mm ? 'ပြန်လည်ကယ်ဆယ်ရေး mode —' : 'Recovery mode:' }}</strong>
                        {{ $mm ? 'load မအောင်မြင်၍ အောက်ပါ plugin များကို အလိုအလျောက် ပိတ်ထားသည် —' : 'the following plugin(s) were auto-disabled after failing to load —' }}
                        @foreach($failures as $slug => $reason)
                            <div class="small mt-1"><code>{{ $slug }}</code> — {{ $reason }}</div>
                        @endforeach
                    </div>
                @endif

                <div id="pluginNone" class="text-center text-muted py-4" style="display:none;">{{ $mm ? 'သင့်ရှာဖွေမှုနှင့် ကိုက်ညီသော ပလပ်အင် မရှိပါ။' : 'No plugins match your search.' }}</div>

                @forelse($grouped as $category => $categoryPlugins)
                    <div class="plugin-group">
                    <h6 class="plugin-category">{{ $category }} <span>· {{ count($categoryPlugins) }}</span></h6>
                    <div class="row">
                    @foreach($categoryPlugins as $plugin)
                        <div class="col-md-4 col-sm-6 mb-4 plugin-col" data-search="{{ strtolower($plugin['name'].' '.$plugin['description'].' '.$plugin['category'].' '.$plugin['slug']) }}">
                            <div class="plugin-card {{ $plugin['active'] ? 'active' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="mb-1"><a href="{{ url('bp-admin/plugins/view?slug='.$plugin['slug']) }}" class="text-dark"><i class="fa fa-plug text-muted"></i> {{ $plugin['name'] }}</a></h5>
                                        <span>
                                            @if($plugin['active'])
                                                <span class="badge badge-success">{{ $mm ? 'အသုံးပြုဆဲ' : 'Active' }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $mm ? 'ပိတ်ထား' : 'Inactive' }}</span>
                                            @endif
                                            @if($plugin['tampered'])
                                                <span class="badge badge-danger" title="{{ $mm ? 'activate လုပ်ပြီးနောက် ဖိုင်များ ပြောင်းလဲထားသည်' : 'Files changed since activation' }}"><i class="fa fa-exclamation-triangle"></i> {{ $mm ? 'ပြင်ဆင်ထား' : 'Modified' }}</span>
                                            @endif
                                            @if($plugin['update_available'])
                                                <span class="badge badge-warning" title="{{ $mm ? 'ဗားရှင်းအသစ် ရရှိနိုင်သည်' : 'A newer version is available' }}"><i class="fa fa-arrow-up"></i> {{ $mm ? 'အဆင့်မြှင့်' : 'Update' }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $plugin['description'] }}</p>
                                    <p class="plugin-meta mb-3">
                                        v{{ $plugin['version'] }}
                                        @if($plugin['author']) &middot; {{ $plugin['author'] }} @endif
                                        &middot; <code>{{ $plugin['slug'] }}</code>
                                        @if($plugin['migrations'])
                                            <span class="badge badge-light border" title="{{ $mm ? 'database migration များ ပါဝင်သည်' : 'Ships database migrations' }}"><i class="fa fa-database"></i> DB</span>
                                        @endif
                                        @if($plugin['minCmsVersion']) &middot; {{ $mm ? 'CMS လိုအပ်ချက်' : 'needs CMS' }} &ge; {{ $plugin['minCmsVersion'] }}@endif
                                    </p>
                                    <div class="d-flex flex-wrap align-items-center mt-2" style="gap:.4rem;">
                                    @if($plugin['settings'])
                                        <a href="{{ url('bp-admin/plugins/settings?slug='.$plugin['slug']) }}" class="btn btn-sm btn-outline-primary" title="{{ $mm ? 'ချိန်ညှိရန်' : 'Configure' }}"><i class="fa fa-cog"></i> {{ $mm ? 'ဆက်တင်' : 'Settings' }}</a>
                                    @endif
                                    @if($plugin['active'])
                                        @if($plugin['update_available'])
                                            <form action="{{ url('bp-admin/plugins/update') }}" method="post" class="d-inline">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                <button type="submit" class="btn btn-sm btn-warning" title="{{ $mm ? 'အဆင့်မြှင့်' : 'Update' }} {{ $plugin['installed_version'] }} → {{ $plugin['version'] }}"><i class="fa fa-arrow-up"></i> {{ $plugin['version'] }} {{ $mm ? 'သို့ အဆင့်မြှင့်ရန်' : 'Update to' }}</button>
                                            </form>
                                        @endif
                                        <form action="{{ url('bp-admin/plugins/deactivate') }}" method="post" class="d-inline">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">{{ $mm ? 'ပိတ်ရန်' : 'Deactivate' }}</button>
                                        </form>
                                    @else
                                        <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}" class="btn btn-sm btn-outline-info" title="{{ $mm ? 'လုံခြုံရေး စစ်ဆေးရန်' : 'Security scan' }}"><i class="fa fa-shield"></i> {{ $mm ? 'စကင်ဖတ်ရန်' : 'Scan' }}</a>
                                        <form action="{{ url('bp-admin/plugins/activate') }}" method="post" class="d-inline">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> {{ $mm ? 'ဖွင့်ရန်' : 'Activate' }}</button>
                                        </form>
                                        <form action="{{ url('bp-admin/plugins/uninstall') }}" method="post" class="d-inline"
                                              onsubmit="return confirm('{{ $mm ? $plugin['name'].' ကို ဖြုတ်မှာ သေချာပါသလား။ ၎င်း၏ migration များ ပြန်ရုပ်သိမ်းပြီး data များ ဖျက်ပါမည်။' : 'Uninstall '.$plugin['name'].'? This rolls back its migrations and deletes its data.' }}')">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ $mm ? 'ဖြုတ်ရန်' : 'Uninstall' }}</button>
                                        </form>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    </div>
                @empty
                    <div class="plugin-empty text-center text-muted py-5">
                        <i class="fa fa-plug fa-2x mb-2 d-block"></i>
                        {{ $mm ? 'ပလပ်အင် မရှိသေးပါ။ စတင်ရန်' : 'No plugins installed. Add one under' }} <code>/plugins</code> {{ $mm ? 'အောက်တွင် တစ်ခု ထည့်ပါ။' : 'to get started.' }}
                    </div>
                @endforelse
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(function () {
    var $q = $('#pluginSearch'), $none = $('#pluginNone');
    $q.on('input', function () {
        var term = $(this).val().toLowerCase().trim(), any = false;
        $('.plugin-group').each(function () {
            var $g = $(this), shown = false;
            $g.find('.plugin-col').each(function () {
                var match = !term || String($(this).data('search')).indexOf(term) !== -1;
                $(this).toggle(match);
                if (match) shown = true;
            });
            $g.toggle(shown);
            if (shown) any = true;
        });
        $none.toggle(!!term && !any);
    });
});
</script>
@endpush
