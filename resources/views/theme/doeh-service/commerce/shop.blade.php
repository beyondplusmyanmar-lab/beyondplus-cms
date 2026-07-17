{{-- Overrides doeh-commerce-storefront::shop — the service list. Data: products, cart, ready. --}}
@extends('theme.doeh-service.layouts.app')
@section('title', 'Services')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $note = trim((string) bp_option('sv_note')) ?: ($mm ? 'စျေးနှုန်းများကို ချိန်းဆိုချိန်တွင် အတည်ပြုသည်။' : 'Prices are confirmed at booking.');
    @endphp

    <header style="text-align:center; margin-bottom:26px;">
        <div class="sv-eyebrow" style="margin-bottom:12px;">{{ $mm ? 'ချိန်းဆို၍' : 'By appointment' }}</div>
        <h1 style="font-size:36px;">{{ $mm ? 'ဝန်ဆောင်မှုများ' : 'Services' }}</h1>
        <p class="sv-muted" style="margin:8px 0 0;">{{ $note }}</p>
    </header>

    @unless ($ready)
        <div class="sv-notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေး — တောင်းဆိုမှု ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured — requests are paused.' }}</div>
    @endunless

    @if (empty($products))
        <div class="sv-card sv-muted" style="padding:24px; text-align:center;">{{ $mm ? 'ဝန်ဆောင်မှုများ မကြာမီ ရောက်လာမည်။' : 'Services coming soon.' }}</div>
    @else
        <div class="sv-card" style="padding:6px 28px;">
            @foreach ($products as $p)
                @include('theme.doeh-service.partials.service', ['p' => $p, 'ready' => $ready, 'mm' => $mm])
            @endforeach
        </div>
        <p style="margin-top:24px; text-align:center;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'တောင်းဆိုမှုများ ကြည့်ရန် →' : 'Review your requests →' }}</a></p>
    @endif
@endsection
