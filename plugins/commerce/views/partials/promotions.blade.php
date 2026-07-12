{{-- Rendered into the Business theme's `business_promotions` slot. Emits column
     items; the theme supplies the section + `.row g-4` wrapper. --}}
@foreach($promos as $promo)
    <div class="col-lg-4 col-sm-6">
        <div class="bz-card h-100 overflow-hidden">
            @if($promo->image)
                <img src="{{ bp_upload_url($promo->image) }}" class="w-100" style="aspect-ratio:16/9;object-fit:cover;" alt="{{ $promo->title }}">
            @endif
            <div class="p-4">
                @if($promo->badge)
                    <span class="badge mb-2" style="background:var(--bz-accent,#f59e0b);color:#fff;">{{ $promo->badge }}</span>
                @endif
                <h5 class="h6 mb-1">{{ $promo->title }}</h5>
                @if($promo->description)
                    <p class="mb-3" style="color:var(--bz-muted,#64748b);font-size:.9rem;">{{ \Illuminate\Support\Str::limit($promo->description, 110) }}</p>
                @endif
                @if($promo->link)
                    <a href="{{ $promo->link }}" class="fw-semibold" style="color:var(--bz-primary,#2563eb);">
                        {{ app()->getLocale() === 'mm' ? 'ကြည့်ရှုရန်' : 'View offer' }} <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
