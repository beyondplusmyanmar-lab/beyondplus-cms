@extends('bp-admin.layouts.admin.index')

@section('title', 'Activity log')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                    <div>
                        <h4 class="mb-0">Activity log</h4>
                        <small class="text-muted">Every action taken in the admin panel, newest first.</small>
                    </div>
                    <form method="GET" class="d-flex align-items-center" style="gap:.4rem;">
                        <label class="mb-0 small text-muted">Category</label>
                        <select name="log" class="form-control form-control-sm" style="width:auto;" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($logNames as $ln)
                                <option value="{{ $ln }}" {{ request('log') === $ln ? 'selected' : '' }}>{{ ucfirst($ln) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th style="width:160px">Who</th><th>Action</th><th style="width:110px">Category</th><th style="width:150px">When</th></tr>
                    </thead>
                    <tbody>
                    @forelse($activities as $a)
                        <tr>
                            <td>{{ optional($a->causer)->name ?? optional($a->causer)->email ?? 'System' }}</td>
                            <td>{{ $a->description }}</td>
                            <td><span class="badge badge-light border text-uppercase" style="font-size:.66rem;">{{ $a->log_name }}</span></td>
                            <td class="text-muted small" title="{{ $a->created_at }}">{{ $a->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $activities->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
