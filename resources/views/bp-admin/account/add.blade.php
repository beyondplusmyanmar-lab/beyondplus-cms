@extends('bp-admin.layouts.admin.index')

@section('title', 'Account')

@section('content')
 <div class="row">

        <div class="col-md-12 tile">
            <div class="box box-danger">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-5">

                            {{ Form::open([
                                'url' => 'bp-admin/account/store',
                                'method' => 'post',
                                'files' => 'true',
                                ]) }}
                          
                            @component('bp-admin.inc.alert')
                            @endcomponent

                                                        
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{ Form::text('name', null,['class'=>'form-control']) }}
                            </div>
                            <!-- <div class="form-group">
                                <label class="control-label">Picture</label>
                                {{ Form::file('pictures',null,array('class'=>'form-control')) }}
                            </div> -->
                            <div class="form-group">
                                <label class="control-label">
                                    Select Role
                                </label>
                                {{ Form::select('role',role_type(),1, ['class'=>'form-control'])}}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                {{ Form::text('email', null,['class'=>'form-control']) }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">New Password</label>
                                {{ Form::password('password',['class'=>'form-control']) }}
                               
                            </div>

                            

                            <div class="">
                                <button type="submit" class="pull-right btn btn-success">Create</button>
                            </div>
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