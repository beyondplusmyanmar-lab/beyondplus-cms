@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'ပို့စ်' : 'Post')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-8">
                            <h4 class="mb-0">{{ $mm ? 'ပို့စ်များ' : 'Posts' }}</h4>
                            <small class="text-muted">{{ $mm ? 'ဆိုက်တွင် ပြသသော ဘလော့ဂ် ပို့စ်များ။' : 'Blog posts shown on the site.' }}</small>
                        </div>
                        <div class="col-sm-4 pull-right">
                            <a href="{{ url('bp-admin/post/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-plus"></i>
                                {{ $mm ? 'ပို့စ် အသစ်' : 'New post' }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table  class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{{ $mm ? 'အမည်' : 'Name' }}</th>
                            <th>{{ $mm ? 'ဘာသာစကား' : 'Language' }}</th>
                            <th>{{ $mm ? 'လုပ်ဆောင်ချက်' : 'Action' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($post as $c)
                        <tr>
                            <td>
                                <a href="{{ url('bp-admin/post/'.$c->id.'/edit') }}" >{{$c->title}}</a> <br>
                            </td>
                            <td>
                                @isset($c->translate)
                                    <a href="{{ url('bp-admin/post/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> | <a href="{{ url('bp-admin/post/'.$c->translate->id.'/edit') }}" >{{ langauge($c->translate->lang) }}</a>
                                @else
                                     <a href="{{ url('bp-admin/post/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> 
                                @endisset
                            </td>
                            <td>
                                <a href="{{ url('bp-admin/post/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">{{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>
                                    <a href="{{ url('bp-admin/post/delete', [$c->id]) }}" class="btn btn-delete btn-xs btn-danger" onclick="return confirm('{{ $mm ? 'ဤ ပို့စ်ကို ဖျက်မှာလား။' : 'Delete this post?' }}')">{{ $mm ? 'ဖျက်ရန်' : 'Delete' }}</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $post->links() }} </div>
                        </div>
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