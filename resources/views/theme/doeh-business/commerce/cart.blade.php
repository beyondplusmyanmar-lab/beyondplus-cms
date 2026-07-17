{{-- Overrides doeh-commerce-demo::cart. Data (lines, ready) from the plugin route. --}}
@extends('theme.doeh-business.layouts.app')
@section('title', 'Cart')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <h1 style="font-size:26px; margin:0 0 6px;">{{ $mm ? 'ခြင်း' : 'Your cart' }}</h1>
    <p class="muted" style="margin:0 0 22px;">{{ $mm ? 'DOEH က checkout တွင် စစ်မှန်သော စုစုပေါင်းကို တွက်ချက်ပေးသည်။' : 'DOEH computes the real total at checkout.' }}</p>

    @if (empty($lines))
        <div class="card muted" style="padding:20px;">
            {{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်။' : 'Your cart is empty.' }}
            <a href="{{ url('/doeh-demo') }}">{{ $mm ? 'ဈေးဆိုင်သို့ →' : 'Back to the shop →' }}</a>
        </div>
    @else
        <div class="card" style="padding:8px 20px; margin-bottom:16px;">
            @foreach ($lines as $l)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 0; {{ ! $loop->last ? 'border-bottom:1px solid var(--line);' : '' }}">
                    <div>
                        <div class="serif" style="font-weight:700;">{{ $l['name'] }}</div>
                        <div class="muted" style="font-size:13px;">{{ $mm ? 'ကုဒ်' : 'SKU' }} {{ $l['sku'] }} · {{ $mm ? 'အရေအတွက်' : 'qty' }} {{ $l['qty'] }}@if($l['price_hint']) · {{ $l['price_hint'] }}@endif</div>
                    </div>
                    <form method="POST" action="{{ url('/doeh-demo/cart/remove') }}">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                        <button class="btn sec" type="submit">{{ $mm ? 'ဖယ်ရှားရန်' : 'Remove' }}</button>
                    </form>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ url('/doeh-demo/checkout') }}" class="card" style="padding:20px;">
            @csrf
            <label class="muted" for="phone" style="font-size:14px;">{{ $mm ? 'ဖုန်းနံပါတ် (မထည့်လည်းရ)' : 'Customer phone (optional)' }}</label>
            <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                   style="width:100%; padding:11px; border:1px solid var(--line); border-radius:10px; margin:6px 0 16px; font:inherit;">
            <button class="btn big" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'မှာယူရန်' : 'Place order' }}</button>
            @unless ($ready)<p class="muted" style="font-size:13px; margin:10px 0 0;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ မှာယူနိုင်သည်။' : 'Ordering is disabled until DOEH Commerce is configured.' }}</p>@endunless
        </form>
        <p style="margin-top:14px;"><a href="{{ url('/doeh-demo') }}">{{ $mm ? '← ဆက်ကြည့်ရန်' : '← Keep shopping' }}</a></p>
    @endif
@endsection
