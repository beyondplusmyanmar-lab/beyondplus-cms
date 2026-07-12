{{-- Front catalogue. Renders inside whatever theme is active by extending its
     app layout (every theme ships layouts/app.blade.php with @yield('content')).
     Uses plain Bootstrap card classes so it looks right on any theme. --}}
@extends('theme.'.$themeSlug.'.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'ဈေးဆိုင်' : 'Shop')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="mb-4">
        <h1 class="h3 mb-1">{{ $mm ? 'ကုန်ပစ္စည်းများ' : 'Shop' }}</h1>
        <p class="text-muted mb-0">{{ $mm ? 'ကျွန်ုပ်တို့၏ ကုန်ပစ္စည်းများ ကို ကြည့်ရှုပါ။' : 'Browse our products.' }}</p>
    </div>

    <div class="row g-4">
        @forelse($products as $p)
            <div class="col-lg-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    @if($p->image)
                        <img src="{{ bp_upload_url($p->image) }}" class="card-img-top" style="aspect-ratio:1/1;object-fit:cover;" alt="{{ $p->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="aspect-ratio:1/1;"><i class="bi bi-box-seam" style="font-size:2rem;"></i></div>
                    @endif
                    <div class="card-body">
                        <h6 class="card-title mb-1">{{ $p->name }}</h6>
                        @if($p->description)
                            <p class="card-text text-muted small mb-2">{{ \Illuminate\Support\Str::limit($p->description, 70) }}</p>
                        @endif
                        <div class="fw-bold text-primary">{{ number_format((float) $p->price) }} {{ $currency }}</div>
                        {!! bp_apply_filters('commerce_product_actions', '', $p) !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">{{ $mm ? 'ကုန်ပစ္စည်း မရှိသေးပါ။' : 'No products yet.' }}</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@stop
