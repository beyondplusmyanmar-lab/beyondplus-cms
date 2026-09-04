{{-- Overrides doeh-commerce-storefront::shop — the flow plugin renders this when the
     DOEH Business theme is active, so the shop lives in the theme's own chrome.
     Data (products, cart, ready) comes from the plugin's route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Shop')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <div class="wrap" style="padding-top:42px;">
        <div class="head-row">
            <h1 class="h2">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</h1>
            <span class="muted small">{{ count($products) }} {{ $mm ? 'ပစ္စည်း' : 'items' }}</span>
        </div>
        <hr class="rule">

        @unless ($ready)
            <div class="notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေးပါ — မှာယူမှု ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured, so ordering is paused.' }}</div>
        @endunless

        @if (empty($products))
            <div class="card" style="padding:40px 24px; text-align:center;">
                <p class="h3 serif" style="margin:0 0 6px;">{{ $mm ? 'ကုန်ပစ္စည်း မရှိသေးပါ' : 'Nothing listed yet' }}</p>
                <p class="muted small" style="margin:0 auto; max-width:36ch;">{{ $mm ? 'ပစ္စည်းများ ထည့်ပြီးသည်နှင့် ဤနေရာတွင် ပေါ်လာပါမည်။' : 'Products appear here as soon as they are added in DOEH.' }}</p>
            </div>
        @else
            <div class="grid">
                @foreach ($products as $p)
                    @include('theme.doeh-business.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm])
                @endforeach
            </div>
            <p style="margin-top:26px;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'ခြင်း ကြည့်ရန်' : 'View your cart' }}</a></p>
        @endif
    </div>
@endsection
