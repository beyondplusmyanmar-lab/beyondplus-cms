@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="md-aside">
    <div class="md-aside-title">{{ ucfirst(__('general.categories')) }}</div>
    <ul class="list-unstyled mb-4">
        @foreach(bp_tax() as $category)
            @php if ($mm && isset($category->translate)) { $category = $category->translate; } @endphp
            <li class="mb-2 d-flex justify-content-between align-items-baseline">
                <a href="{{ url('/cat/'.$category->tax_link) }}" class="md-serif">{{ $category->tax_name }}</a>
                <span class="md-byline"><i class="bi bi-chevron-right"></i></span>
            </li>
        @endforeach
    </ul>

    <div class="md-aside-title">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Newsletter' }}</div>
    <p class="md-dek small">{{ $mm ? 'နောက်ဆုံးရ သတင်းများအတွက် ဆက်သွယ်ပါ။' : 'Prefer email? Reach us through the contact page.' }}</p>
    <a href="{{ url('/contact') }}" class="btn btn-md-ghost btn-sm">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</a>
</div>
