{{-- Overrides doeh-commerce-storefront::cart. Data: lines, ready. --}}
@extends('theme.doeh-retail.layouts.app')
@section('title', 'Your bag')

@section('content')
    @php $mm = app()->getLocale() === 'mm'; @endphp
    <h1 style="font-size:30px; margin-bottom:18px;">{{ $mm ? 'သင့်ခြင်း' : 'Your bag' }}</h1>

    @if (empty($lines))
        <div class="rt-card rt-muted" style="padding:24px; text-align:center;">
            {{ $mm ? 'ခြင်း ဗလာဖြစ်နေသည်။' : 'Your bag is empty.' }}
            <a href="{{ url('/store') }}">{{ $mm ? 'ဈေးဆိုင်သို့ →' : 'Start shopping →' }}</a>
        </div>
    @else
        <div style="display:grid; grid-template-columns: 1fr; gap:20px;">
            <div class="rt-card" style="padding:6px 20px;">
                @foreach ($lines as $l)
                    <div style="display:flex; align-items:center; gap:14px; padding:16px 0; {{ ! $loop->last ? 'border-bottom:1px solid var(--line);' : '' }}">
                        <div class="swatch" style="width:52px; height:52px; aspect-ratio:auto; border-radius:12px; font-size:22px; flex:0 0 auto; background:linear-gradient(135deg,var(--brand-tint),#fff); display:grid; place-items:center; font-family:'Space Grotesk',serif; font-weight:700; color:var(--brand);">{{ mb_strtoupper(mb_substr($l['name'], 0, 1)) }}</div>
                        <div style="flex:1 1 auto;">
                            <div style="font-weight:600; font-family:'Space Grotesk',system-ui,sans-serif;">{{ $l['name'] }}</div>
                            <div class="rt-muted" style="font-size:13px;">{{ $mm ? 'အရေအတွက်' : 'Qty' }} {{ $l['qty'] }}@if($l['price_hint']) · {{ $l['price_hint'] }}@endif</div>
                        </div>
                        <form method="POST" action="{{ url('/store/cart/remove') }}">
                            @csrf
                            <input type="hidden" name="sku" value="{{ $l['sku'] }}">
                            <button class="rt-btn ghost sm" type="submit">{{ $mm ? 'ဖယ်ရန်' : 'Remove' }}</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ url('/store/checkout') }}" class="rt-card" style="padding:22px 24px;">
                @csrf
                <div class="rt-display" style="font-size:18px; margin-bottom:12px;">{{ $mm ? 'အော်ဒါ အတည်ပြုရန်' : 'Review & order' }}</div>
                <label class="rt-muted" for="phone" style="font-size:13px;">{{ $mm ? 'ဖုန်း (မထည့်လည်းရ)' : 'Phone (optional)' }}</label>
                <input id="phone" name="phone" type="tel" placeholder="+95912345678"
                       style="width:100%; padding:12px; border:1px solid var(--line); border-radius:11px; margin:6px 0 10px; font:inherit; background:var(--bg);">
                <p class="rt-muted" style="font-size:13px; margin:0 0 16px;">{{ $mm ? 'စုစုပေါင်းကို DOEH က checkout တွင် တွက်ပေးသည်။' : 'DOEH calculates your total at checkout.' }}</p>
                <button class="rt-btn block" type="submit" @unless($ready) disabled @endunless>{{ $mm ? 'အော်ဒါ တင်ရန်' : 'Place order' }}</button>
                @unless ($ready)<p class="rt-muted" style="font-size:13px; margin:12px 0 0; text-align:center;">{{ $mm ? 'DOEH Commerce ချိန်ညှိပြီးမှ အော်ဒါတင်နိုင်သည်။' : 'Ordering opens once DOEH Commerce is configured.' }}</p>@endunless
            </form>
        </div>
        <p style="margin-top:16px;"><a href="{{ url('/store') }}">{{ $mm ? '← ဆက်ဝယ်ရန်' : '← Keep shopping' }}</a></p>
    @endif
@endsection
