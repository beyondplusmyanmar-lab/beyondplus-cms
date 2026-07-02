@extends('theme.'.(optional(site_information('theme'))->option_value ?: 'default').'.layouts.app')

@section('title', 'Set a new password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-4 text-center">Set a new password</h1>

                    @include('front.customer.partials.messages')

                    <form method="POST" action="{{ url('customer/new-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New password</label>
                            <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   name="new_password" required autofocus>
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_confirm_password" class="form-label">Confirm new password</label>
                            <input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="login">Update password</button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        <a href="{{ url('/customer/sign-in') }}">Back to sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
