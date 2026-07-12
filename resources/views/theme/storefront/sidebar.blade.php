<div class="sf-panel">
    <h5 class="sf-panel-title mb-3">{{ ucfirst(__('general.categories')) }}</h5>
    <ul class="list-unstyled mb-0 d-grid gap-2">
        @foreach(bp_tax() as $category)
            @php if (app()->getLocale() === 'mm' && isset($category->translate)) { $category = $category->translate; } @endphp
            <li><a href="{{ url('/cat/'.$category->tax_link) }}" style="color:var(--sf-text);"><i class="bi bi-tag text-primary"></i> {{ $category->tax_name }}</a></li>
        @endforeach
    </ul>
</div>
