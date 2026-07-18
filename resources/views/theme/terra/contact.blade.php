@extends('theme.terra.layouts.app')

@section('title', 'Contact')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="tr-label mb-3">{{ $mm ? 'ဆက်သွယ်ရန်' : 'contact' }}</div>
            <h1 class="tr-display mb-2" style="font-size:clamp(2rem,5vw,3rem);">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Say hello' }}</h1>
            <p class="tr-muted mb-4">{{ $mm ? 'မေးခွန်း သို့မဟုတ် အကြံပြုချက် ရှိပါသလား။ ကျွန်ုပ်တို့ထံ စာပို့ပါ။' : 'Have a question or feedback? Send us a message.' }}</p>

            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

            @if(bp_option('feedback_enabled', 'yes') === 'yes')
                <form method="POST" action="{{ url('/contact') }}">
                    {{ csrf_field() }}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    <div class="mb-3">
                        <label class="form-label tr-cat">{{ $mm ? 'အမည်' : 'Name' }} *</label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label tr-cat">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label>
                        <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" placeholder="{{ $mm ? 'ပြန်လည်ဖြေကြားနိုင်ရန်' : 'So we can reply' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label tr-cat">{{ $mm ? 'ခေါင်းစဉ်' : 'Subject' }}</label>
                        <input type="text" name="subject" class="form-control form-control-lg" value="{{ old('subject') }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label tr-cat">{{ $mm ? 'မက်ဆေ့ချ်' : 'Message' }} *</label>
                        <textarea name="message" class="form-control form-control-lg" rows="5" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-tr">{{ $mm ? 'စာပို့ရန်' : 'Send message' }}</button>
                </form>
            @else
                <p class="tr-muted py-4">{{ $mm ? 'ဆက်သွယ်ရန် ဖောင်ကို ယာယီ ပိတ်ထားပါသည်။' : 'The contact form is currently unavailable.' }}</p>
            @endif
        </div>
    </div>
</div>
@stop
