@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'စာမျက်နှာ' : 'Page')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-7">
                            <h4 class="mb-0">{{ $mm ? 'စာမျက်နှာများ' : 'Pages' }}</h4>
                            <small class="text-muted">{{ $mm ? 'menu များမှ ချိတ်ဆက်ထားသော သီးခြား စာမျက်နှာများ။' : 'Standalone pages linked from menus.' }}</small>
                        </div>
                        <div class="col-sm-5 pull-right">
                            <a href="{{ url('bp-admin/page/create') }}" class="btn btn-success  pull-right ml-2">
                                <i class="fa fa-plus"></i>
                                {{ $mm ? 'စာမျက်နှာ အသစ်' : 'New page' }}
                            </a>
                            <a href="{{ url('bp-admin/user-guide') }}" class="btn btn-secondary  pull-right">
                                <i class="fa fa-book"></i>
                                {{ $mm ? 'အသုံးပြုသူ လမ်းညွှန်' : 'User Guide' }}
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br />
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
                        @foreach ($page as $c)
                        <tr>
                            <td>
                                <a href="{{ url('bp-admin/page/'.$c->id.'/edit') }}" >{{$c->title}}</a>
                            </td>
                            <td>@isset($c->translate)
                                    <a href="{{ url('bp-admin/page/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> | <a href="{{ url('bp-admin/page/'.$c->translate->id.'/edit') }}" >{{ langauge($c->translate->lang) }}</a>
                                @else
                                     <a href="{{ url('bp-admin/page/'.$c->id.'/edit') }}" >{{langauge($c->lang)}}</a> 
                                @endisset
                            </td>
                            <td>
                                <a href="{{ url('bp-admin/page/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">{{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>
                                <a href="{{ url('bp-admin/page/delete', [$c->id]) }}" class="btn btn-delete btn-xs btn-danger" onclick="return confirm('{{ $mm ? 'ဤ စာမျက်နှာကို ဖျက်မှာလား။' : 'Delete this page?' }}')">{{ $mm ? 'ဖျက်ရန်' : 'Delete' }}</a>
                            </td>
                            
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $page->links() }} </div>
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