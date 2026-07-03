@extends('bp-admin.layouts.admin.index')

@section('title', 'Page')

@section('content')
 <div class="row tile">

        <div class="col-md-9">
            <div class="box box-danger">
                <div class="box-header">
                        <div class="col-sm-7">
                            <h4>Title</h4>
                        </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                        <div class="col-sm-10">
                            {{ Form::open([
                                'url' => 'bp-admin/feedback/store',
                                'method' => 'post',
                                'files' => 'true',
                                ]) }}
                          
                            @component('bp-admin.inc.alert')
                            @endcomponent
                            {{--  --}}
                            
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{ Form::text('name', null,['class'=>'form-control']) }}
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                {{ Form::textarea('message', null, ['class'=>'form-control', 'id' => 'textarea']) }}
                               
                            </div>

                            <div class="form-group">
                                <label class="control-label">Active</label>
                                <select class="form-control" name="post_active">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                       
                            <div class="">
                                <button type="submit" class="pull-right btn btn-success">Create</button>
                            </div>
                            {{--  --}}
                        </div>{{-- end of form wrapper div --}}
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="col-md-12 form-group">
                        <div class="row">
                            {{ Form::label('Template Name') }}<br />
                            @include('bp-admin.inc.template-select', ['selected' => 'default'])
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Language') }}<br />
                    <div class="col-md-12 form-group">
                        
                        <div class="row">
                                {{ Form::select('lang',langauge(),1,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Translate') }}<br />
                    <div class="col-md-12 form-group">
                        <div class="row">
                                {{ Form::select('translate_id',bp_select_pages(),0,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
@endsection

@section('scripts')
    <!-- <script src="{{url('/vendor/unisharp/laravel-ckeditor/ckeditor.js')}}"></script> -->
    <script src="{{ asset('ckeditor/ckeditor.js')}}"></script>
    <!-- <script src="{{ asset('ckeditor/adapters/jquery.js')}}"></script> -->
    <!-- <script src="{{url('/vendor/unisharp/laravel-ckeditor/adapters/jquery.js')}}"></script> -->
    <script>CKEDITOR.replace('textarea');</script>
   <!--  <script>
        $('textarea').ckeditor();
        
    </script> -->
@stop