{{-- Overrides doeh-commerce-storefront::shop — the product grid. Data: products, cart, ready. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Shop')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $pickup = (bp_option('rt_pickup', 'yes') ?: 'yes') === 'yes';
    @endphp

    <div class="rt-wrap" style="padding-top:44px;">
        <div class="rt-head-row">
            <h1 class="rt-h2">{{ $mm ? 'ဈေးဆိုင်' : 'Shop' }}</h1>
            <span class="rt-muted rt-small">{{ count($products) }} {{ $mm ? 'ပစ္စည်း' : 'items' }}</span>
        </div>
        <hr class="rt-shelf-rule">

        @unless ($ready)
            <div class="rt-notice err">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေး — ဝယ်ယူမှု ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured, so ordering is paused.' }}</div>
        @endunless

        @if (empty($products))
            <div class="rt-panel" style="padding:44px 24px; text-align:center;">
                <p class="rt-h3" style="margin:0 0 6px;">{{ $mm ? 'ပစ္စည်းများ မကြာမီ ရောက်လာမည်' : 'Nothing on the shelves yet' }}</p>
                <p class="rt-muted rt-small" style="margin:0 auto; max-width:36ch;">{{ $mm ? 'ပစ္စည်းများ ထည့်ပြီးသည်နှင့် ဤနေရာတွင် ပေါ်လာပါမည်။' : 'Products appear here as soon as they are added in DOEH.' }}</p>
            </div>
        @else
            <div class="rt-grid{{ bp_option('rt_grid', 'comfortable') === 'compact' ? ' rt-compact' : '' }}">
                @foreach ($products as $p)
                    @include('theme.doeh-retail.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])
                @endforeach
            </div>
            <p style="margin-top:28px;"><a href="{{ url('/store/cart') }}">{{ $mm ? 'ခြင်း ကြည့်ရန်' : 'View your bag' }}</a></p>
        @endif
    </div>
@endsection
