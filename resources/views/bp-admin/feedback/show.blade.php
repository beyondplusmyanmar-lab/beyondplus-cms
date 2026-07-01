@extends('bp-admin.layouts.admin.index')

@section('title', 'Page')

@section('content')

<div class="row tile">
    <div class="col-md-12"> 
    </div>
    <div class="col-md-9">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-7">
                        <h4> </h4>
                    </div>
                    <div class="col-sm-5 text-right">
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-sm-10">

                    {{--  --}}
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Title</label>:
                        {{ $page->name }}
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Description</label>:
                        {{ $page->message }}
                        
                    </div> 
                    
                    
                    {{--  --}}
                </div>{{-- end of form wrapper div --}}
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-3">
       
    </div>  


</div>

@stop


@section('scripts')

<script src="{{ asset('ckeditor/ckeditor.js')}}"></script>
<script>CKEDITOR.replace('textarea');</script>

@stop