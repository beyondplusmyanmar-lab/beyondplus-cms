{{-- Injected into Commerce product cards via the commerce_product_actions hook. --}}
<form method="post" action="{{ url('/cart/add') }}" class="mt-2">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="product_id" value="{{ $p->id }}">
    <button type="submit" class="btn btn-sm btn-primary w-100">
        <i class="bi bi-cart-plus"></i> {{ app()->getLocale() === 'mm' ? 'ခြင်းထဲ ထည့်ရန်' : 'Add to cart' }}
    </button>
</form>
