{{-- Overrides doeh-commerce-storefront::shop — the product grid. Data: products, cart, ready. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Shop')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $pickup = (bp_option('rt_pickup', 'yes') ?: 'yes') === 'yes';
    @endphp

    <header style="display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
        <h1 style="font-size:30px;">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</h1>
        <span class="rt-muted">{{ count($products) }} {{ $mm ? 'ပစ္စည်း' : 'items' }}</span>
    </header>

    @unless ($ready)
        <div class="rt-notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေး — ဝယ်ယူမှု ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured — ordering is paused.' }}</div>
    @endunless

    @if (empty($products))
        <div class="rt-card rt-muted" style="padding:24px; text-align:center;">{{ $mm ? 'ပစ္စည်းများ မကြာမီ ရောက်လာမည်။' : 'Products coming soon.' }}</div>
    @else
        <div class="rt-grid{{ bp_option('rt_grid', 'comfortable') === 'compact' ? ' rt-compact' : '' }}">
            @foreach ($products as $p)
                @include('theme.doeh-retail.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])
            @endforeach
        </div>
        <p style="margin-top:24px;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'ခြင်း ကြည့်ရန် →' : 'View your bag →' }}</a></p>
    @endif
@endsection
