@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="nc-glass p-4">
    <div class="nc-eyebrow mb-3">{{ ucfirst(__('general.categories')) }}</div>
    <ul class="list-unstyled mb-0">
        @foreach(bp_tax() as $category)
            @php if ($mm && isset($category->translate)) { $category = $category->translate; } @endphp
            <li class="mb-2">
                <a href="{{ url('/cat/'.$category->tax_link) }}" class="d-flex align-items-center gap-2 text-light">
                    <i class="bi bi-hash text-primary"></i> {{ $category->tax_name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
