@extends('bp-admin.layouts.admin.index')

@section('title', 'Themes')

@section('content')
<style>
    .theme-card { border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; transition: box-shadow .15s ease; height: 100%; }
    .theme-card:hover { box-shadow: 0 .6rem 1.4rem rgba(0,0,0,.10); }
    .theme-card.active { border-color: #14b8a6; box-shadow: 0 0 0 2px rgba(20,184,166,.25); }
    .theme-thumb { height: 180px; background: #f3f4f6 center/cover no-repeat; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
    .theme-thumb img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
    .theme-meta { font-size: .78rem; color: #6b7280; }
    .plugin-card { border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0">Themes</h4>
                <small class="text-muted">The active theme controls how the front-end of your site looks.</small>
            </div>
            <!-- /.box-header -->
            <div class="box-body pt-3" style="border-top: 1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <div class="row">
                    @foreach($themes as $theme)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="theme-card {{ $theme['slug'] === $active ? 'active' : '' }}">
                                <div class="theme-thumb">
                                    @if($theme['preview'])
                                        <img src="{{ asset($theme['preview']) }}" alt="{{ $theme['name'] }} preview">
                                    @else
                                        <i class="fa fa-paint-brush fa-2x"></i>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="mb-1">{{ $theme['name'] }}</h5>
                                        <span>
                                            @if($theme['slug'] === $active)
                                                <span class="badge badge-success">Active</span>
                                            @endif
                                            @if($theme['tampered'])
                                                <span class="badge badge-danger" title="Files changed since activation"><i class="fa fa-exclamation-triangle"></i> Modified</span>
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $theme['description'] }}</p>
                                    <p class="theme-meta mb-3">
                                        <i class="fa fa-code-fork"></i> v{{ $theme['version'] }}
                                        @if($theme['author']) &middot; {{ $theme['author'] }} @endif
                                        &middot; <code>{{ $theme['slug'] }}</code>
                                        @if($theme['minCmsVersion']) &middot; needs CMS &ge; {{ $theme['minCmsVersion'] }}@endif
                                    </p>
                                    @if($theme['slug'] === $active)
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Currently active</button>
                                    @else
                                        <a href="{{ url('bp-admin/themes/scan?theme='.$theme['slug']) }}" class="btn btn-sm btn-outline-info" title="Security scan"><i class="fa fa-shield"></i> Scan</a>
                                        <form action="{{ url('bp-admin/themes/activate') }}" method="post" class="d-inline">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="theme" value="{{ $theme['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-check"></i> Activate
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0">Plugins</h4>
                <small class="text-muted">Extend the CMS with add-ons.</small>
            </div>
            <div class="box-body pt-3" style="border-top: 1px solid #eef0f3;">
                <div class="plugin-card p-4 text-center">
                    <i class="fa fa-plug fa-2x text-muted mb-2 d-block"></i>
                    <h5 class="mb-1">Manage plugins</h5>
                    <p class="text-muted mb-2">
                        Install hook-based plugins under <code>/plugins</code> and activate them —
                        future <strong>DoehPOS</strong> integrations plug in here too.
                    </p>
                    <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plug"></i> Open Plugins
                    </a>
                    <a href="https://developers.doehpos.com/" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-external-link"></i> DoehPOS docs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
        });
    </script>
@endpush
