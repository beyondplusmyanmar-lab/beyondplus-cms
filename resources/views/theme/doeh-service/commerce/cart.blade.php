{{-- Overrides doeh-commerce-storefront::cart — "your requests". Data: lines, ready. --}}
@extends('theme.doeh-service.layouts.app')
@section('title', 'Your requests')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <header style="text-align:center; margin-bottom:24px;">
        <div class="sv-eyebrow" style="margin-bottom:10px;">{{ $mm ? 'တောင်းဆိုမှု' : 'Your requests' }}</div>
        <h1 style="font-size:32px;">{{ $mm ? 'ချိန်းဆိုရန်' : 'Request an appointment' }}</h1>
    </header>

    @if (empty($lines))
        <div class="sv-card sv-muted" style="padding:24px; text-align:center;">
            {{ $mm ? 'တောင်းဆိုမှု မရှိသေးပါ။' : 'No services selected yet.' }}
            <a href="{{ url('/store') }}">{{ $mm ? 'ဝန်ဆောင်မှုများသို့ →' : 'Browse services →' }}</a>
        </div>
    @else
        <div class="sv-card" style="padding:6px 28px; margin-bottom:18px;">
            @foreach ($lines as $l)
                <div class="sv-svc">
                    <div class="s-mid">
                        <div class="s-name">{{ $l['name'] }}</div>
                        <div class="s-note">{{ $mm ? 'အရေအတွက်' : 'Qty' }} {{ $l['qty'] }} · {{ $l['sku'] }}</div>
                    </div>
                    @if ($l['price_hint'])<span class="sv-price s-price">{{ $l['price_hint'] }}</span>@endif
                    <form method="POST" action="{{ url('/store/cart/remove') }}">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                        <button class="sv-btn ghost sm" type="submit">{{ $mm ? 'ဖယ်ရန်' : 'Remove' }}</button>
                    </form>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ url('/store/checkout') }}" class="sv-card" style="padding:24px 26px;">
            @csrf
            <label class="sv-eyebrow" for="phone" style="color:var(--muted);">{{ $mm ? 'ဆက်သွယ်ရန် ဖုန်း' : 'Contact phone' }}</label>
            <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                   style="width:100%; padding:12px; border:1px solid var(--line); border-radius:12px; margin:8px 0 8px; font:inherit; background:var(--bg);">
            <p class="sv-muted" style="font-size:13px; margin:0 0 16px;">{{ $mm ? 'သင့်တောင်းဆိုမှုကို လက်ခံပြီး အချိန်ဇယား အတည်ပြုပေးပါမည်။' : "We'll receive your request and confirm a time with you." }}</p>
            <button class="sv-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'တောင်းဆိုမှု တင်ရန်' : 'Send request' }}</button>
            @unless ($ready)<p class="sv-muted" style="font-size:13px; margin:12px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ တောင်းဆိုနိုင်သည်။' : 'Requests open once DOEH Commerce is configured.' }}</p>@endunless
        </form>
        <p style="margin-top:16px; text-align:center;"><a href="{{ url('/store') }}">{{ $mm ? '← ဝန်ဆောင်မှုများသို့' : '← Back to services' }}</a></p>
    @endif
@endsection
