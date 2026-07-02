@extends('theme.'.(optional(site_information('theme'))->option_value ?: 'default').'.layouts.app')

@section('title', 'Create an account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-4 text-center">Create an account</h1>

                    @include('front.customer.partials.messages')

                    <form method="POST" action="{{ url('customer/sign-up') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Name</label>
                            <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror"
                                   name="firstname" value="{{ old('firstname') }}" required autofocus>
                            @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <input id="lastname" type="hidden" name="lastname" value="{{ old('lastname') }}">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone number</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirm password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create account</button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        Already have an account? <a href="{{ url('/customer/sign-in') }}">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
