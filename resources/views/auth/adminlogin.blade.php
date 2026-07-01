@extends('bp-admin.layouts.app')

@section('content')

<div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 login-box">
                <br />
                <div class="col-lg-12 login-key pt-10">

                    <i class="fa fa-key" aria-hidden="true"></i>
                </div>
                <div class="col-lg-12 login-title">
                    ADMIN PANEL
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/bp-admin/login') }}">
                            {!! csrf_field() !!}

                            @isset ($match)
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                       <li>{{$match}}</li>
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="form-control-label">E-Mail Address  @if(Session::has('msg'))
                                {{ Session::get('msg')}}
                            @endif</label>

                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="form-control-label">Password</label>

                                    <input type="password" class="form-control" name="password">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label class="text-white">
                                            <input type="checkbox" name="remember" > Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-4 text-right">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <!-- <i class="fa fa-btn fa-sign-in"></i> -->Login
                                    </button>

                                </div>
                            </div>

                            <div class="form-group">
                                 <!-- <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a> -->
                            </div>
                        </form>
                    </div>
                </div>

             
            </div>
            <div class="col-lg-3 col-md-2"></div>
            
        </div>
</div>
<!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/bp-admin/login') }}">
                        {!! csrf_field() !!}

                        @isset ($match)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                   <li>{{$match}}</li>
                                </ul>
                            </div>
                        @endif

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="form-control-label">E-Mail Address  @if(Session::has('msg'))
                            {{ Session::get('msg')}}
                        @endif</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                            
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i>Login
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection
