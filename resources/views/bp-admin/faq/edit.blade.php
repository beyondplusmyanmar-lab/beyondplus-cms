@extends('bp-admin.layouts.admin.index')

@section('title', 'Page')

@section('content')

<div class="row tile">
    <div class="col-md-12">
      {{ Form::model($page, [
        'url' => ['bp-admin/faq', $page->id],
        'method' => 'put',
        'files' => 'true'
        ]) }}
    </div>
    <div class="col-md-9">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-7">
                        <h4>Form </h4>
                    </div>
                    <div class="col-sm-5 text-right">
                        <b><i>{{ langauge($page->translate_id) }}</i></b>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
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
                        {{  Form::textarea('content', null, ['class'=>'form-control','id' => 'textarea' ])  }}

                    </div>
                  <!--   <div class="form-group">
                        <label class="control-label">Weight</label>
                        {{ Form::text('post_weight', null,['class'=>'form-control']) }}

                    </div> -->
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
                </div>{{-- end of form wrapper div --}}
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-3">
        <div class="box box-danger">
            <div class="box-body">
                <!-- <div class="col-md-12 form-group">
                    <div class="row">
                    {{ Form::label('Template Name') }}<br />
                    {{ Form::text('post_template', null,['class'=>'form-control']) }}
                    </div>
                </div> -->
            </div>
        </div>
    <!--     <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Language') }}<br />
                    <div class="col-md-12 form-group">

                        <div class="row">
                                {{ Form::select('lang',langauge(),$page->lang,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div>
        <div class="box box-danger">
                <div class="box-body">
                    {{ Form::label('Translate') }}<a href="{{url('/bp-admin/faq/'.$page->id.'/translate')}}"> Translate</a><br />
                    <div class="col-md-12 form-group">

                        <div class="row">
                                {{ Form::select('translate_id',bp_select_pages(),$page->translate_id,array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>
            </div> -->
    </div>
    {{ Form::close() }}


</div>

@stop

@push('scripts')

<script src="{{ asset('ckeditor/ckeditor.js')}}"></script>
<script>CKEDITOR.replace('textarea');</script>

@endpush
