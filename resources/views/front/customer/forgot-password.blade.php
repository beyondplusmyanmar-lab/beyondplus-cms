@extends('theme.'.(optional(site_information('theme'))->option_value ?: 'default').'.layouts.app')

@section('title', 'Reset password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-2 text-center">Reset password</h1>
                    <p class="text-muted text-center mb-4">Enter your phone number and the code you received.</p>

                    @include('front.customer.partials.messages')

                    <form method="POST" action="{{ url('customer/forgot-pass') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone number</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" required autofocus>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="activation_code" class="form-label">Verification code</label>
                            <input id="activation_code" type="text" class="form-control @error('activation_code') is-invalid @enderror"
                                   name="activation_code" required>
                            @error('activation_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="verify">Verify</button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        Remembered it? <a href="{{ url('/customer/sign-in') }}">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
