@extends('bp-admin.layouts.app')

@section('content')
<!-- START MAIN CONTENT -->
<div class="main_content">
     <!-- START LOGIN SECTION -->
    <div class="login_register_wrap section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-md-10">
                    <div class="login_wrap">
                        <div class="padding_eight_all bg-white">
                            <div class="heading_s1">
                                <h3>New Password</h3>
                            </div>

                            @if(Session::has('msg'))

                                @if(Session::get('msg')['status'] == "Success")
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        {{ Session::get('msg')['message'] }}
                                    </div>
                                @else

                                    <div class="alert alert-danger">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        {{ Session::get('msg')['message']  }}
                                    </div>
                                @endif

                            @endif

                            @if (Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif
                            @if (Session::has('flash_danger'))
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ Session::get('flash_danger') }}
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ url('customer/new-password') }}">
                                {{ csrf_field() }}
                                
                            <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-12 control-label">New Password</label>

                                <div class="col-md-12">
                                    <input id="password" type="password" class="form-control" name="new_password" required>

                                    @if ($errors->has('new_password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('new_password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('new_confirm_password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-12 control-label">Re-type Password</label>

                                <div class="col-md-12">
                                    <input id="password" type="password" class="form-control" name="new_confirm_password" required>

                                    @if ($errors->has('new_confirm_password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('new_confirm_password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                                <div class="login_footer form-group">
                                   <!--  <div class="chek-form">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox1" value="">
                                            <label class="form-check-label" for="exampleCheckbox1"><span>Remember me</span></label>
                                        </div>
                                    </div> -->
                                    <a href="{{ url('/customer/forgot-pass') }}">Forgot password?</a>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-fill-out btn-block" name="login">Log in</button>
                                </div>
                            </form>
                            <!-- <div class="different_login">
                                <span> or</span>
                            </div>
                            <ul class="btn-login list_none text-center">
                                <li><a href="#" class="btn btn-facebook"><i class="ion-social-facebook"></i>Facebook</a></li>
                                <li><a href="#" class="btn btn-google"><i class="ion-social-googleplus"></i>Google</a></li>
                            </ul> -->
                            <div class="form-note text-center">Don't Have an Account? <a href="{{ url('/customer/sign-up') }}">Sign up now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END LOGIN SECTION -->
</div>
@endsection
