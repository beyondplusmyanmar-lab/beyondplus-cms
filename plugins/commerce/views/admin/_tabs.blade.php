@php
    $path = request()->path();
    $isPromo  = \Illuminate\Support\Str::startsWith($path, 'bp-admin/commerce/promotions');
    $isBranch = \Illuminate\Support\Str::startsWith($path, 'bp-admin/commerce/branches');
    $isProduct = ! $isPromo && ! $isBranch;
@endphp
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ $isProduct ? 'active' : '' }}" href="{{ url('bp-admin/commerce') }}"><i class="fa fa-shopping-cart"></i> Products</a></li>
    <li class="nav-item"><a class="nav-link {{ $isPromo ? 'active' : '' }}" href="{{ url('bp-admin/commerce/promotions') }}"><i class="fa fa-tags"></i> Promotions</a></li>
    <li class="nav-item"><a class="nav-link {{ $isBranch ? 'active' : '' }}" href="{{ url('bp-admin/commerce/branches') }}"><i class="fa fa-map-marker"></i> Locations</a></li>
</ul>
