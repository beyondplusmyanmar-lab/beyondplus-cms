{{-- Rendered into the Business theme's `business_featured_products` slot.
     Emits column items only — the theme provides the section + `.row g-4`
     wrapper. Uses the theme's own .bz-card / --bz-primary tokens so it matches
     whatever colours the site is themed with. --}}
@foreach($products as $p)
    <div class="col-lg-3 col-sm-6">
        <div class="bz-card h-100 overflow-hidden">
            @if($p->image)
                <a href="{{ url('/shop') }}"><img src="{{ bp_upload_url($p->image) }}" class="w-100" style="aspect-ratio:1/1;object-fit:cover;" alt="{{ $p->name }}"></a>
            @else
                <div class="d-flex align-items-center justify-content-center" style="aspect-ratio:1/1;background:var(--bz-surface,#f8fafc);color:var(--bz-muted,#94a3b8);">
                    <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                </div>
            @endif
            <div class="p-3">
                <h6 class="mb-1">{{ $p->name }}</h6>
                <div class="fw-bold" style="color:var(--bz-primary,#2563eb);">
                    {{ number_format((float) $p->price) }} {{ $currency }}
                </div>
                {{-- Extension slot: a checkout plugin can add an "Add to cart" button here. --}}
                {!! bp_apply_filters('commerce_product_actions', '', $p) !!}
            </div>
        </div>
    </div>
@endforeach
