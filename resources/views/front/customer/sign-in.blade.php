@extends('theme.'.(optional(site_information('theme'))->option_value ?: 'default').'.layouts.app')

@section('title', 'Sign in')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-4 text-center">Sign in</h1>

                    @include('front.customer.partials.messages')

                    <form method="POST" action="{{ url('customer/sign-in') }}">
                        @csrf
                        @php
                            $regType = bp_option('registration_type', 'phone');
                            $idLabel = $regType === 'email' ? 'Email address' : ($regType === 'both' ? 'Phone or email' : 'Phone number');
                        @endphp
                        <div class="mb-3">
                            <label for="phone" class="form-label">{{ $idLabel }}</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" required autofocus>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="{{ url('/customer/forgot-pass') }}">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="login">Sign in</button>
                    </form>

                    @if(bp_option('registration_enabled', 'yes') === 'yes')
                    <p class="text-center text-muted mt-4 mb-0">
                        Don't have an account? <a href="{{ url('/customer/sign-up') }}">Sign up</a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
