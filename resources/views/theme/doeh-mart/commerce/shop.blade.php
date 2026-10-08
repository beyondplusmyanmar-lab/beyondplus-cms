{{-- Overrides doeh-commerce-storefront::shop — the product grid. Data: products, cart, ready.
     ?q= narrows the list by name or SKU (the header search and the category tiles). --}}
@extends('theme.doeh-mart.layouts.app')
@section('title', 'Shop')

@section('content')
    @php
        $mm = app()->getLocale() === 'mm';
        $types = function_exists('doeh_storefront_fulfillment_types') ? doeh_storefront_fulfillment_types() : ['pickup'];
        $pickup = in_array('pickup', $types, true);
        $q = trim((string) request('q'));
        $shown = $q === '' ? $products : array_values(array_filter($products, fn ($p) =>
            mb_stripos($p['name'], $q) !== false || mb_stripos($p['sku'], $q) !== false));
    @endphp

    <div class="container pt-3">
        <div class="mt-panel">
            <div class="d-flex align-items-baseline justify-content-between flex-wrap mb-3" style="gap:.5rem 1rem;">
                <h1 class="h5 fw-bold mb-0">
                    @if ($q !== '')
                        {{ $mm ? 'ရှာဖွေမှု' : 'Results for' }} “{{ $q }}”
                    @else
                        {{ $mm ? 'ကုန်ပစ္စည်း အားလုံး' : 'All products' }}
                    @endif
                </h1>
                <span class="small mt-muted">
                    {{ count($shown) }} {{ $mm ? 'ခု' : (count($shown) === 1 ? 'item' : 'items') }}
                    @if ($q !== '') · <a href="{{ url('/store') }}">{{ $mm ? 'အားလုံး ပြရန်' : 'Show all' }}</a>@endif
                </span>
            </div>

            @unless ($ready)
                <div class="alert alert-warning small">{{ $mm ? 'DOEH Commerce မချိန်ညှိရသေး — ဝယ်ယူမှု ခဏ ပိတ်ထားသည်။' : 'DOEH Commerce is not configured, so ordering is paused.' }}</div>
            @endunless

            @if (empty($products))
                <div class="text-center mt-muted py-5">
                    <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                    <p class="fw-semibold mb-1 mt-2 text-body">{{ $mm ? 'ပစ္စည်းများ မကြာမီ ရောက်လာမည်' : 'Nothing on the shelves yet' }}</p>
                    <p class="small mb-0">{{ $mm ? 'ပစ္စည်းများ ထည့်ပြီးသည်နှင့် ဤနေရာတွင် ပေါ်လာပါမည်။' : 'Products appear here as soon as they are added.' }}</p>
                </div>
            @elseif (empty($shown))
                <div class="text-center mt-muted py-5">
                    <i class="bi bi-search" style="font-size:1.8rem;"></i>
                    <p class="mb-2 mt-2">{{ $mm ? 'ကိုက်ညီသော ပစ္စည်း မရှိပါ။' : 'No products match that.' }}</p>
                    <a class="btn btn-primary btn-sm" href="{{ url('/store') }}">{{ $mm ? 'အားလုံး ကြည့်ရန်' : 'See all products' }}</a>
                </div>
            @else
                <div class="row g-2 g-md-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
                    @foreach ($shown as $p)
                        <div class="col">@include('theme.doeh-mart.partials.card', ['p' => $p, 'ready' => $ready, 'mm' => $mm, 'pickup' => $pickup])</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
