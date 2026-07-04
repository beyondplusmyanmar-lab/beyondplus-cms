@extends('theme.default.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="bp-section-title text-center mb-2">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Contact us' }}</h1>
            <p class="text-muted text-center mb-4">{{ $mm ? 'မေးခွန်း သို့မဟုတ် အကြံပြုချက် ရှိပါသလား။ ကျွန်ုပ်တို့ထံ စာပို့ပါ။' : 'Have a question or feedback? Send us a message.' }}</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            @if(bp_option('feedback_enabled', 'yes') === 'yes')
                <form method="POST" action="{{ url('/contact') }}" class="card border-0 shadow-sm p-4">
                    {{ csrf_field() }}
                    {{-- Honeypot: hidden from humans; bots that fill it are dropped. --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    <div class="mb-3">
                        <label class="form-label">{{ $mm ? 'အမည်' : 'Name' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="{{ $mm ? 'ပြန်လည်ဖြေကြားနိုင်ရန်' : 'So we can reply' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $mm ? 'ခေါင်းစဉ်' : 'Subject' }}</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $mm ? 'မက်ဆေ့ချ်' : 'Message' }} <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">{{ $mm ? 'စာပို့ရန်' : 'Send message' }}</button>
                </form>
            @else
                <p class="text-center text-muted py-4">{{ $mm ? 'ဆက်သွယ်ရန် ဖောင်ကို ယာယီ ပိတ်ထားပါသည်။' : 'The contact form is currently unavailable.' }}</p>
            @endif
        </div>
    </div>
</div>
@stop
