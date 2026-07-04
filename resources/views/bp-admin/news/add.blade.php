@extends('bp-admin.layouts.admin.index')

@section('title', 'News')

@section('content')
 <div class="row tile">
    <div class="col-md-12"> 
        {{Form::open([
            'url' => 'bp-admin/news',
            'method' => 'post',
            'files' => 'true',
            ])}}
    </div>
        <div class="col-md-9">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-7">
                            <h4>Title</h4>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            @component('bp-admin.inc.alert')
                            @endcomponent
                            
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{Form::text('title', null,['class'=>'form-control'])}}
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                {{ Form::textarea('body', null, ['class'=>'form-control', 'id' => 'textarea']) }}
                               
                            </div> 
                        
                            <div class="form-group">
                                <label class="control-label">Weight</label>
                                {{Form::text('post_weight', 0,['class'=>'form-control'])}}
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label">Post Type</label>
                                <select class="form-control" name="post_type">
                                    <option value="news">News</option>
                                    <option value="event">Event</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Event date &amp; time</label>
                                <input type="datetime-local" name="event_at" id="start_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                                <small class="form-text text-muted">Used for events (ignored for plain news).</small>
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
                    
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Categories') }}<br />
                    <div class="col-md-12 form-group scrollbar">
                        
                        <div class="row">
                            @foreach($taxes as $tax)
                            <div class="col-md-2">
                                {{ Form::checkbox('taxes[]' , $tax->tax_id ) }}
                            </div>
                            <div class="col-md-10">
                                <label for="{{$tax->tax_name}}">{{$tax->tax_name}}</label>
                            </div>
                            @endforeach
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
                                {{ Form::select('translate_id',bp_select_posts(),0,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>


            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Featured Image') }}<br />
                    <div class="col-md-12 form-group">
                        
                        <div class="row">
                                {{ Form::file('featured_img',array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="box-body">
                    <div class="col-md-12">
                            <img src="{{bp_upload_url('default.jpg')}}" class="img-responsive">
                    </div>  
                </div>
            </div>

        </div>  
        {{ Form::close() }}
    </div>        
@stop

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js')}}"></script>
    <script>CKEDITOR.replace('textarea');</script>
    <script>

        $(document).ready(function () {
            var height = $('.scrollbar').height();
            if (height > 158) {
                $('.scrollbar').addClass('overflow-y');
            } else {
                $('.scrollbar').removeClass('overflow-y');
            }

            $('#start_date').datepicker({  format : 'yyyy-mm-dd',autoclose: true,
            todayHighlight: true});

        });
        
      
    </script>
@endpush