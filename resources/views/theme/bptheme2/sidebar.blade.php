<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title bp-section-title">{{ ucfirst(__('general.categories')) }}</h5>
        <hr>
        <ul class="list-unstyled mb-0">
            @foreach(bp_tax() as $category)
                @php
                    if (app()->getLocale() === 'mm' && isset($category->translate)) {
                        $category = $category->translate;
                    }
                @endphp
                <li class="mb-1">
                    <a href="{{ url('/cat/'.$category->tax_link) }}" class="text-decoration-none text-dark">
                        <i class="bi bi-tag"></i> {{ $category->tax_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
