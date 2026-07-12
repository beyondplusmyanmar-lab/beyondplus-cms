@extends('theme.'.$themeSlug.'.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'ကျေးဇူးတင်ပါသည်' : 'Order received')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
            <h1 class="h3 mt-3">{{ $mm ? 'အော်ဒါ လက်ခံရရှိပါပြီ' : 'Order received' }}</h1>
            <p class="text-muted">{{ $message }}</p>

            @if($order)
                <div class="card border-0 shadow-sm text-start mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $mm ? 'အော်ဒါနံပါတ်' : 'Order number' }}</span>
                            <strong>{{ $order->order_number }}</strong>
                        </div>
                        <hr>
                        @foreach($items as $it)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $it->name }} &times; {{ $it->qty }}</span>
                                <span>{{ number_format($it->line_total) }} {{ $currency }}</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>{{ $mm ? 'စုစုပေါင်း' : 'Total' }}</strong>
                            <strong>{{ number_format($order->subtotal) }} {{ $currency }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ url('/shop') }}" class="btn btn-primary mt-4">{{ $mm ? 'ဈေးဆက်ဝယ်ရန်' : 'Continue shopping' }}</a>
        </div>
    </div>
</div>
@stop
