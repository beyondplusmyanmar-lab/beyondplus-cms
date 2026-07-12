{{-- Cart + checkout, rendered inside the active theme. Orders are inquiry /
     cash-on-delivery — no payment fields. --}}
@extends('theme.'.$themeSlug.'.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'ခြင်း' : 'Cart')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <h1 class="h3 mb-4">{{ $mm ? 'ခြင်း' : 'Your Cart' }}</h1>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    @if($items->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-cart-x" style="font-size:2.5rem;"></i>
            <p class="mt-3">{{ $mm ? 'ခြင်းတွင် ပစ္စည်း မရှိပါ။' : 'Your cart is empty.' }}</p>
            <a href="{{ url('/shop') }}" class="btn btn-primary">{{ $mm ? 'ဈေးဆက်ဝယ်ရန်' : 'Continue shopping' }}</a>
        </div>
    @else
        <div class="row g-4">
            {{-- Items --}}
            <div class="col-lg-7">
                <form method="post" action="{{ url('/cart/update') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>{{ $mm ? 'ပစ္စည်း' : 'Item' }}</th><th style="width:110px">{{ $mm ? 'အရေအတွက်' : 'Qty' }}</th><th class="text-end" style="width:140px">{{ $mm ? 'ပေါင်း' : 'Total' }}</th></tr></thead>
                            <tbody>
                                @foreach($items as $it)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($it->image)<img src="{{ bp_upload_url($it->image) }}" width="44" height="44" style="object-fit:cover;border-radius:6px;" alt="">@endif
                                                <div><div class="fw-semibold">{{ $it->name }}</div><small class="text-muted">{{ number_format($it->price) }} {{ $currency }}</small></div>
                                            </div>
                                        </td>
                                        <td><input type="number" name="qty[{{ $it->id }}]" value="{{ $it->qty }}" min="1" max="99" class="form-control form-control-sm" style="width:80px"></td>
                                        <td class="text-end fw-semibold">{{ number_format($it->line) }} {{ $currency }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> {{ $mm ? 'ခြင်း ပြင်ဆင်ရန်' : 'Update cart' }}</button>
                </form>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($items as $it)
                        <form method="post" action="{{ url('/cart/remove') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="product_id" value="{{ $it->id }}">
                            <button class="btn btn-sm btn-link text-danger p-0" style="text-decoration:none;"><i class="bi bi-x-circle"></i> {{ $it->name }}</button>
                        </form>
                    @endforeach
                </div>
            </div>

            {{-- Checkout (inquiry / COD) --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ $mm ? 'စုစုပေါင်း' : 'Subtotal' }} ({{ $count }})</span>
                            <strong>{{ number_format($subtotal) }} {{ $currency }}</strong>
                        </div>
                        <p class="small text-muted">{{ $mm ? 'ငွေပေးချေမှုကို ပစ္စည်းရောက်မှ (COD) ဆောင်ရွက်ပါသည်။ အော်ဒါတင်ပြီးနောက် ဆက်သွယ်ပါမည်။' : 'Cash on delivery — no online payment. We will contact you to confirm your order.' }}</p>
                        <hr>
                        <form method="post" action="{{ url('/checkout') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            {{-- Honeypot --}}
                            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                            <div class="mb-2"><label class="form-label small">{{ $mm ? 'အမည်' : 'Name' }} <span class="text-danger">*</span></label><input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required></div>
                            <div class="mb-2"><label class="form-label small">{{ $mm ? 'ဖုန်း' : 'Phone' }} <span class="text-danger">*</span></label><input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required></div>
                            <div class="mb-2"><label class="form-label small">{{ $mm ? 'အီးမေးလ်' : 'Email' }}</label><input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}"></div>
                            <div class="mb-2"><label class="form-label small">{{ $mm ? 'ပို့ဆောင်ရမည့် လိပ်စာ' : 'Delivery address' }} <span class="text-danger">*</span></label><textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea></div>
                            <div class="mb-3"><label class="form-label small">{{ $mm ? 'မှတ်ချက်' : 'Note' }}</label><textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea></div>
                            <button type="submit" class="btn btn-primary w-100">{{ $mm ? 'အော်ဒါတင်ရန်' : 'Place order' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop
