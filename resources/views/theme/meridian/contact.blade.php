@extends('theme.meridian.layouts.app')

@section('title', 'Contact')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="text-center mb-4">
                <div class="md-kicker mb-1">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Correspondence' }}</div>
                <h1 class="md-serif" style="font-weight:600;letter-spacing:-.01em;">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Write to the desk' }}</h1>
                <p class="md-dek">{{ $mm ? 'မေးခွန်း သို့မဟုတ် အကြံပြုချက် ရှိပါသလား။ ကျွန်ုပ်တို့ထံ စာပို့ပါ။' : 'A question, a tip, or feedback — send it our way.' }}</p>
            </div>

            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

            @if(bp_option('feedback_enabled', 'yes') === 'yes')
                <form method="POST" action="{{ url('/contact') }}" class="p-4" style="border:1px solid var(--md-rule);background:#fff;">
                    {{ csrf_field() }}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    <div class="mb-3">
                        <label class="form-label md-kicker md-kicker--muted">{{ $mm ? 'အမည်' : 'Name' }} *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label md-kicker md-kicker--muted">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="{{ $mm ? 'ပြန်လည်ဖြေကြားနိုင်ရန်' : 'So we can reply' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label md-kicker md-kicker--muted">{{ $mm ? 'ခေါင်းစဉ်' : 'Subject' }}</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label md-kicker md-kicker--muted">{{ $mm ? 'မက်ဆေ့ချ်' : 'Message' }} *</label>
                        <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">{{ $mm ? 'စာပို့ရန်' : 'Send message' }}</button>
                </form>
            @else
                <p class="text-center md-dek py-4">{{ $mm ? 'ဆက်သွယ်ရန် ဖောင်ကို ယာယီ ပိတ်ထားပါသည်။' : 'The contact form is currently unavailable.' }}</p>
            @endif
        </div>
    </div>
</div>
@stop
