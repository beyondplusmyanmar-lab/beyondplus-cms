@extends('theme.storefront.layouts.app')
@section('title', app()->getLocale() === 'mm' ? 'ဆက်သွယ်ရန်' : 'Contact')
@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $phone = bp_option('sf_phone'); $email = bp_option('sf_email') ?: optional(site_information('admin_email'))->option_value; $address = bp_option('sf_address');
@endphp
<div class="container py-4">
    <div class="row g-3 justify-content-center">
        <div class="col-lg-8">
            <div class="sf-panel">
                <h1 class="h4 text-center mb-2">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact us' }}</h1>
                <p class="sf-muted text-center mb-4">{{ $mm ? 'မေးခွန်း ရှိပါက ကျွန်ုပ်တို့ထံ စာပို့ပါ။' : 'Questions? Send us a message.' }}</p>

                <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 small">
                    @if($phone)<span><i class="bi bi-telephone text-primary"></i> {{ $phone }}</span>@endif
                    @if($email)<span><i class="bi bi-envelope text-primary"></i> {{ $email }}</span>@endif
                    @if($address)<span><i class="bi bi-geo-alt text-primary"></i> {{ $address }}</span>@endif
                </div>

                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

                @if(bp_option('feedback_enabled', 'yes') === 'yes')
                    <form method="POST" action="{{ url('/contact') }}">
                        {{ csrf_field() }}
                        <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">{{ $mm ? 'အမည်' : 'Name' }} <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                            <div class="col-md-6"><label class="form-label">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
                            <div class="col-12"><label class="form-label">{{ $mm ? 'ခေါင်းစဉ်' : 'Subject' }}</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}"></div>
                            <div class="col-12"><label class="form-label">{{ $mm ? 'မက်ဆေ့ချ်' : 'Message' }} <span class="text-danger">*</span></label><textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea></div>
                            <div class="col-12"><button type="submit" class="btn btn-primary px-4">{{ $mm ? 'စာပို့ရန်' : 'Send message' }}</button></div>
                        </div>
                    </form>
                @else
                    <p class="text-center sf-muted">{{ $mm ? 'ဆက်သွယ်ရန် ဖောင်ကို ယာယီ ပိတ်ထားပါသည်။' : 'The contact form is currently unavailable.' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
