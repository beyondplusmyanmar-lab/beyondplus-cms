<div class="bz-card p-4">
    <h5 class="h6 mb-3">{{ ucfirst(__('general.categories')) }}</h5>
    <ul class="list-unstyled mb-0 d-grid gap-2">
        @foreach(bp_tax() as $category)
            @php
                if (app()->getLocale() === 'mm' && isset($category->translate)) { $category = $category->translate; }
            @endphp
            <li>
                <a href="{{ url('/cat/'.$category->tax_link) }}" class="d-inline-flex align-items-center gap-2" style="color:var(--bz-text);">
                    <i class="bi bi-tag text-primary"></i> {{ $category->tax_name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
