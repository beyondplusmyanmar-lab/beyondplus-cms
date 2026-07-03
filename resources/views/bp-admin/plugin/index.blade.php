@extends('bp-admin.layouts.admin.index')

@section('title', 'Plugins')

@section('content')
<style>
    .plugin-card { border: 1px solid #e5e7eb; border-radius: 10px; height: 100%; transition: box-shadow .15s ease; }
    .plugin-card:hover { box-shadow: 0 .5rem 1.2rem rgba(0,0,0,.08); }
    .plugin-card.active { border-color: #14b8a6; box-shadow: 0 0 0 2px rgba(20,184,166,.2); }
    .plugin-meta { font-size: .78rem; color: #6b7280; }
    .plugin-empty { border: 1px dashed #d1d5db; border-radius: 8px; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0">Plugins</h4>
                <small class="text-muted">Extend the CMS with add-ons. Drop a plugin folder in <code>/plugins</code>, then activate it here.</small>
            </div>
            <!-- /.box-header -->
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <div class="row">
                    @forelse($plugins as $plugin)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="plugin-card {{ $plugin['active'] ? 'active' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="mb-1"><i class="fa fa-plug text-muted"></i> {{ $plugin['name'] }}</h5>
                                        @if($plugin['active'])
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-2">{{ $plugin['description'] }}</p>
                                    <p class="plugin-meta mb-3">
                                        v{{ $plugin['version'] }}
                                        @if($plugin['author']) &middot; {{ $plugin['author'] }} @endif
                                        &middot; <code>{{ $plugin['slug'] }}</code>
                                        @if($plugin['migrations'])
                                            <span class="badge badge-light border" title="Ships database migrations"><i class="fa fa-database"></i> DB</span>
                                        @endif
                                    </p>
                                    @if($plugin['active'])
                                        <form action="{{ url('bp-admin/plugins/deactivate') }}" method="post" class="d-inline">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Deactivate</button>
                                        </form>
                                    @else
                                        <a href="{{ url('bp-admin/plugins/scan?slug='.$plugin['slug']) }}" class="btn btn-sm btn-outline-info" title="Security scan"><i class="fa fa-shield"></i> Scan</a>
                                        <form action="{{ url('bp-admin/plugins/activate') }}" method="post" class="d-inline">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Activate</button>
                                        </form>
                                        <form action="{{ url('bp-admin/plugins/uninstall') }}" method="post" class="d-inline"
                                              onsubmit="return confirm('Uninstall {{ $plugin['name'] }}? This rolls back its migrations and deletes its data.')">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Uninstall</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="plugin-empty text-center text-muted py-5">
                                <i class="fa fa-plug fa-2x mb-2 d-block"></i>
                                No plugins installed. Add one under <code>/plugins</code> to get started.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
@stop

@push('scripts')
    <script>$(document).ready(function () {});</script>
@endpush
