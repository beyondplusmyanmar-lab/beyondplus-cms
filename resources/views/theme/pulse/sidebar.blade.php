@php
    $mm = app()->getLocale() === 'mm';
    $pill = fn ($name) => 'c'.(abs(crc32((string) $name)) % 5);
@endphp
<div class="pl-card p-4">
    <span class="pl-eyebrow mb-3">{{ ucfirst(__('general.categories')) }}</span>
    <div class="d-flex flex-wrap gap-2 mt-3">
        @foreach(bp_tax() as $category)
            @php if ($mm && isset($category->translate)) { $category = $category->translate; } @endphp
            <a href="{{ url('/cat/'.$category->tax_link) }}" class="pl-pill {{ $pill($category->tax_name) }}">{{ $category->tax_name }}</a>
        @endforeach
    </div>
</div>
