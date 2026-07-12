{{-- Rendered into the Business theme's `business_store_locations` slot. Emits
     column items; the theme supplies the section + `.row g-4` wrapper. --}}
@php $mm = app()->getLocale() === 'mm'; @endphp
@foreach($branches as $b)
    <div class="col-lg-4 col-sm-6">
        <div class="bz-card h-100 p-4">
            <h5 class="h6 mb-3"><i class="bi bi-geo-alt" style="color:var(--bz-primary,#2563eb);"></i> {{ $b->name }}</h5>
            <ul class="list-unstyled mb-3 d-grid gap-2" style="color:var(--bz-muted,#64748b);font-size:.9rem;">
                @if($b->address)<li><i class="bi bi-pin-map me-1"></i> {{ $b->address }}</li>@endif
                @if($b->phone)<li><i class="bi bi-telephone me-1"></i> <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $b->phone) }}" style="color:inherit;">{{ $b->phone }}</a></li>@endif
                @if($b->hours)<li><i class="bi bi-clock me-1"></i> {{ $b->hours }}</li>@endif
            </ul>
            @if($b->map_embed)
                <div class="rounded overflow-hidden" style="border:1px solid var(--bz-border,#e5e7eb);">
                    <iframe src="{{ $b->map_embed }}" width="100%" height="150" style="border:0;" loading="lazy" title="{{ $b->name }}" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @endif
        </div>
    </div>
@endforeach
