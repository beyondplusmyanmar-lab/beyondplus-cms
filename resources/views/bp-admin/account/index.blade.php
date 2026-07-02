@extends('bp-admin.layouts.admin.index')

@section('title', 'Staff Accounts')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="mb-0">Staff accounts</h4>
                        <small class="text-muted">Admin and staff users who can sign in to the dashboard.</small>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ url('bp-admin/account/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-user-plus"></i> New account
                        </a>
                    </div>
                </div>
                <form action="{{ url('/bp-admin/account') }}" method="get">
                    <div class="row pt-3">
                        <div class="col-md-6">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Search by name or email"
                                   autocomplete="off" value="{{ Request::get('name') }}">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info"><span class="fa fa-search"></span> Search</button>
                            <a href="{{ url('/bp-admin/account') }}" class="btn btn-primary" title="Reset"><span class="fa fa-refresh"></span></a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adminaccounts as $adminaccount)
                            <tr>
                                <td>{{ $adminaccount->name }}</td>
                                <td><span class="badge badge-info">{{ role_type($adminaccount->role) }}</span></td>
                                <td>{{ $adminaccount->email }}</td>
                                <td>{{ optional($adminaccount->created_at)->format('Y-m-d') }}</td>
                                <td class="text-right">
                                    <a href="{{ url('bp-admin/account/'.$adminaccount->id.'/edit') }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <a href="{{ url('bp-admin/account/delete', [$adminaccount->id]) }}" class="btn btn-sm btn-danger btn-delete"
                                       onclick="return confirm('Remove this account?')">
                                        <i class="fa fa-trash"></i> Remove
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No staff accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if(method_exists($adminaccounts, 'links'))
                    <div class="row">
                        <div class="col-sm-12">{{ $adminaccounts->links() }}</div>
                    </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
        });
    </script>
@endpush
