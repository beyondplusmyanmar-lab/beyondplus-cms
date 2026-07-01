@extends('bp-admin.layouts.admin.index')

@section('title', 'User')

@section('content')
  <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-5">
                            {{Form::model($user, [
                                'url' => ['bp-admin/user', $user->id],
                                'method' => 'put',
                                'files' => 'true'
                                ])}}
                                
                            @component('bp-admin.inc.alert')
                            @endcomponent
                            
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{Form::text('first_name', null,['class'=>'form-control'])}}
                            </div>
                            <div class="form-group">
                                <label class="control-label">
                                    Select Role
                                </label>
                                {{ Form::select('customer_types_id',role_type(),$user->role, ['class'=>'form-control'])}}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                {{ Form::text('email',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Password</label>
                                {{ Form::password('password',['class'=>'form-control']) }}
                            </div>
                            
                            <div>
                                <button type="submit" class="pull-right btn btn-success">Update</button>
                            </div>
                            {{Form::close()}}
                            {{--  --}}
                        </div>{{-- end of form wrapper div --}}
                    </div>
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