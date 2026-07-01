@extends('bp-admin.layouts.admin.index')

@section('title', 'Menu')

@section('content')
 <div class="row">

        <div class="col-md-12 tile">
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
                        <div class="col-sm-5">
                            {{ Form::open([
                                'url' => 'bp-admin/menu',
                                'method' => 'post',
                                'files' => 'true',
                                ]) }}
                          
                            @component('bp-admin.inc.alert')
                            @endcomponent
                            {{--  --}}
                            
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {{ Form::text('menu_name', null,['class'=>'form-control']) }}
                                {{ Form::hidden('post_id', 0,['class'=>'form-control']) }}
                               
                            </div>

                            <div class="form-group">
                                <label class="control-label">Link</label>
                                {{ Form::text('menu_link', null,['class'=>'form-control', 'placeholder' => '#link']) }}
                               
                            </div>
                            

                            <div class="form-group">
                                <label class="control-label">Show Home</label>
                                <select class="form-control" name="category_dash">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ Form::label('Translate') }}</label>
                                
                                        {{ Form::select('translate_id',bp_select_menus(),1,array('class'=>'form-control')) }}
                                
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">{{ Form::label('Language') }}</label>
                                    {{ Form::select('lang',langauge(),1,array('class'=>'form-control')) }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Weight</label>
                                {{ Form::text('menu_weight', 0,['class'=>'form-control']) }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Menu Type</label>
                                {{ Form::select('menu_type', [
                                    'refer' => 'Refer',
                                    'default' => 'Default',
                                    'block' => 'Block',
                                ],  'default', ['class'=> 'form-control']) }}
                            </div>
                       
                            <div class="">
                                <button type="submit" class="pull-right btn btn-success">Create</button>
                            </div>
                            {{ Form::close() }}
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
           /// alert("aa");
        });
    </script>
@endpush