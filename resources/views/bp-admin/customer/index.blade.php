@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'ဖောက်သည်' : 'Customer')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
    <div class="row">
        <div class="col-md-12 tile">
            <div class="box box-danger">
                <div class="box-header">
                    <div class="row">
                        <div class="col-sm-9">
                            
                            <div class="form-group">
                             {{ Form::open([
                                'method' => 'get'
                                ]) }}
                              <input type="text" name="search"   class="form-control" placeholder="{{ $mm ? 'ရှာရန်' : 'Search' }}" value="{{\Request::get('search')}}">
                             {{ Form::close() }}
                            </div>
                        </div>
                        <div class="col-sm-3 pull-right">
                             

                            <a href="{{ url('bp-admin/user/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-user-plus"></i>
                                {{ $mm ? 'အသစ်' : 'New' }}
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
                            <th>{{ $mm ? 'ဖုန်း' : 'Phone' }}</th>
                            <th>{{ $mm ? 'အီးမေးလ်' : 'Email' }}</th>
                            <th>{{ $mm ? 'ဖန်တီးသည့်ရက်' : 'Created Date' }}</th>
                            <th>{{ $mm ? 'လုပ်ဆောင်ချက်' : 'Actions' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($user as $c)
                        
                        
                        <tr>
                            <td>{{$c->first_name}}</a></td>
                            <td>{{$c->phone}}</td>
                            <td>{{$c->email}}</td>
                            <td>{{$c->created_at}}</td>
                            <td>
        
                                <div style="float:right">
                                <a href="{{ url('bp-admin/user/'.$c->id.'/edit') }}" class="btn btn-xs btn-info">{{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>

                                <a href="{{ url('bp-admin/user/delete',[$c->id]) }}" class="btn btn-delete btn-xs btn-danger">{{ $mm ? 'ဖယ်ရှားရန်' : 'Remove' }}</a>
                                </div>
                             </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                           {{--!! dataPaginator($users, true) !!--}}
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