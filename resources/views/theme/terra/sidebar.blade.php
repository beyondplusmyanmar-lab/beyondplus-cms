@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="tr-aside">
    <div class="tr-label mb-3">{{ strtolower(__('general.categories')) }}</div>
    <ul class="list-unstyled mb-4">
        @foreach(bp_tax() as $category)
            @php if ($mm && isset($category->translate)) { $category = $category->translate; } @endphp
            <li class="mb-2"><a href="{{ url('/cat/'.$category->tax_link) }}" class="tr-ul">{{ $category->tax_name }}</a></li>
        @endforeach
    </ul>
    <hr>
    <p class="tr-muted small mb-2">{{ $mm ? 'မေးခွန်း ရှိပါသလား။' : 'Have a question?' }}</p>
    <a href="{{ url('/contact') }}" class="btn btn-tr-ghost btn-sm">{{ $mm ? 'ဆက်သွယ်ရန်' : 'Get in touch' }}</a>
</div>
