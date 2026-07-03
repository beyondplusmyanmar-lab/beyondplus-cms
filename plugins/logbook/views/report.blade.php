@extends('bp-admin.layouts.admin.index')

@section('title', 'Logbook')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-book"></i> Logbook</h4>
                <small class="text-muted">Page views recorded by the Logbook plugin — a plugin-owned admin page, view and table.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th style="width:80px">#</th><th>Event</th><th>When</th></tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>{{ $entry->id }}</td>
                                <td><span class="badge badge-secondary">{{ $entry->event }}</span></td>
                                <td>{{ $entry->created_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No entries yet — open the front-end to record a page view.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
