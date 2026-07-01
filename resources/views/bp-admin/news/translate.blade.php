
@extends('bp-admin.layouts.admin.index')

@section('title', 'News')

@section('content')
  <div class="row">
        <div class="col-md-12">
            {{Form::model($post,[
            'url' => 'bp-admin/news',
            'method' => 'post',
            'files' => 'true',
            ])}}
        </div>
        <div class="col-md-9 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-7">
                            <h4>Form </h4>
                        </div>
                        <div class="col-sm-5 text-right">
                            <b><i>{{ langauge($post->lang) }}</i></b>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-10">
                            
                            @component('bp-admin.inc.alert')
                            @endcomponent
                            
                            {{--  --}}
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{ Form::text('title', null,['class'=>'form-control']) }}
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                {{  Form::textarea('body', null, ['class'=>'form-control', 'id' => 'textarea'])  }}
                               
                            </div> 
                            <div class="form-group">
                                <label class="control-label">Weight</label>
                                {{ Form::text('post_weight', null,['class'=>'form-control']) }}
                               
                            </div>
                            <div class="form-group">
                                <label class="control-label">Post Type </label>

                                @if($post->post_type == "event")
                                    <select class="form-control" name="post_type">
                                        <option value="news" >News</option>
                                        <option value="event" selected="">Event</option>
                                    </select>
                                @else
                                    <select class="form-control" name="post_type">
                                        <option value="news" selected="">News</option>
                                        <option value="event" >Event</option>
                                    </select>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="control-label">Event Date</label>
                                {{ Form::text('event_at', null,['class'=>'form-control', 'id' => 'start_date']) }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Active</label>
                                <select class="form-control" name="post_active">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                          
                            <div>
                                <button type="submit" class="pull-right btn btn-success">Update</button>
                            </div>
                            
                            {{--  --}}
                        </div>
                      
                        
                        {{-- end of form wrapper div --}}
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-3 tile">
            <div class="box box-danger">
                <div class="box-body">
                    {{  Form::label('Categories')  }}<br />
                    <div class="col-md-12 form-group scrollbar">
                        <ul>
                            @foreach($taxes as $tax)
                            <li>   
                                
                                {{ Form::checkbox('taxes[]' , $tax->tax_id, in_array($tax->tax_id,     $tax_type) ) }}

                                <label for="{{$tax->tax_name}}">{{$tax->tax_name}}</label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Language') }}<br />
                    <div class="col-md-12 form-group">
                        
                        <div class="row">
                                {{ Form::select('lang',langauge(),$post->lang,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Translate') }}<a href="{{url('/bp-admin/page/'.$post->id.'/translate')}}"> Translate</a><br />
                    <div class="col-md-12 form-group">
                        @if(isset($translate_id)) 
                            @php $post->translate_id = $translate_id;  @endphp
                        @endif
                        <div class="row">
                                {{ Form::select('translate_id',bp_select_news(),$post->translate_id,array('class'=>'form-control')) }}
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
                        @if($post->featured_img)
                            <img src="{{url('uploads/'.$post->featured_img)}}" class="img-responsive">
                        @else 
                            <img src="{{url('uploads/default.jpg')}}" class="img-responsive">
                            
                        @endif
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

        // $('.textarea').ckeditor(); // if class is prefered.
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