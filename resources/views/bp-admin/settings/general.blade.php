@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'အထွေထွေ ဆက်တင်များ' : 'General Settings')

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $val = fn ($k, $d = '') => $options[$k] ?? $d;
    $themes = collect(glob(resource_path('views/theme/*'), GLOB_ONLYDIR))->map(fn ($p) => basename($p))->values();
@endphp
<div class="row">
    <div class="col-md-12 tile">
        {{ Form::open(['url' => 'bp-admin/general/add', 'method' => 'post', 'files' => 'true']) }}
        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="mb-0">{{ $mm ? 'အထွေထွေ ဆက်တင်များ' : 'General settings' }}</h4>
                        <small class="text-muted">{{ $mm ? 'ဆိုက် အမည်၊ URL များ နှင့် အသုံးပြုနေသော theme။' : 'Site identity, URLs and the active theme.' }}</small>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" name="save" class="btn btn-success pull-right">
                            <i class="fa fa-save"></i> {{ $mm ? 'ပြောင်းလဲမှု သိမ်းရန်' : 'Save changes' }}
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @component('bp-admin.inc.alert')@endcomponent

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="blogname">{{ $mm ? 'ဆိုက် အမည်' : 'Site name' }}</label>
                        <input type="text" name="blogname" id="blogname" class="form-control" value="{{ $val('blogname') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="blogdescription">{{ $mm ? 'ဆောင်ပုဒ်' : 'Tagline' }}</label>
                        <input type="text" name="blogdescription" id="blogdescription" class="form-control" value="{{ $val('blogdescription') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="siteurl">{{ $mm ? 'ဆိုက် URL' : 'Site URL' }}</label>
                        <input type="text" name="siteurl" id="siteurl" class="form-control" value="{{ $val('siteurl') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="home">{{ $mm ? 'ပင်မ URL' : 'Home URL' }}</label>
                        <input type="text" name="home" id="home" class="form-control" value="{{ $val('home') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="admin_email">{{ $mm ? 'Admin အီးမေးလ်' : 'Admin email' }}</label>
                        <input type="email" name="admin_email" id="admin_email" class="form-control" value="{{ $val('admin_email') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="theme">{{ $mm ? 'အသုံးပြုနေသော theme' : 'Active theme' }}</label>
                        <select name="theme" id="theme" class="form-control">
                            @foreach($themes as $t)
                                <option value="{{ $t }}" @if($val('theme') === $t) selected @endif>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="crawler_text">{{ $mm ? 'Robots / crawler meta' : 'Robots / crawler meta' }}</label>
                        <input type="text" name="crawler_text" id="crawler_text" class="form-control" value="{{ $val('crawler_text') }}" placeholder="index, follow">
                    </div>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="fa fa-info-circle"></i>
                    {!! $mm ? 'အကောင့်ဖွင့် နည်းလမ်း၊ API၊ SMS နှင့် email ဆက်တင်များကို <a href="'.url('bp-admin/configuration').'">Configuration</a> စာမျက်နှာတွင် စီမံသည်။' : 'Registration method, API, SMS and email settings are managed on the <a href="'.url('bp-admin/configuration').'">Configuration</a> page.' !!}
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        {{ Form::close() }}
    </div>
</div>
@stop

@push('scripts')
    <script>
        $(document).ready(function () {
        });
    </script>
@endpush
