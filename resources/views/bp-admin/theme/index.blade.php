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
                                        @if($theme['slug'] === $active)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-2">{{ $theme['description'] }}</p>
                                    <p class="theme-meta mb-3">
                                        <i class="fa fa-code-fork"></i> v{{ $theme['version'] }}
                                        @if($theme['author']) &middot; {{ $theme['author'] }} @endif
                                        &middot; <code>{{ $theme['slug'] }}</code>
                                    </p>
                                    @if($theme['slug'] === $active)
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Currently active</button>
                                    @else
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
                    <h5 class="mb-1">Plugins are coming soon</h5>
                    <p class="text-muted mb-2">
                        Beyond Plus CMS will support installable plugins, including
                        <strong>DoehPOS</strong> integrations.
                    </p>
                    <a href="https://developers.doehpos.com/" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                        <i class="fa fa-external-link"></i> DoehPOS developer docs
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
