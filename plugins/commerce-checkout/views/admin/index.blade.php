@extends('bp-admin.layouts.admin.index')

@section('title', 'Orders')

@section('content')
@php
    $badge = ['new' => 'info', 'confirmed' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary'];
@endphp
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-shopping-bag"></i> Orders</h4>
                <small class="text-muted">Checkout orders (inquiry / cash-on-delivery). No payment data is stored.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Order</th><th>Customer</th><th style="width:130px">Phone</th><th style="width:90px">Items</th><th style="width:140px">Total</th><th style="width:110px">Status</th><th style="width:150px">Placed</th><th class="text-end" style="width:70px"></th></tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td><code>{{ $o->order_number }}</code></td>
                                <td>{{ $o->customer_name }}</td>
                                <td class="small">{{ $o->customer_phone }}</td>
                                <td>{{ $o->item_count }}</td>
                                <td>{{ number_format($o->subtotal) }} {{ $currency }}</td>
                                <td><span class="badge badge-{{ $badge[$o->status] ?? 'secondary' }}">{{ ucfirst($o->status) }}</span></td>
                                <td class="small text-muted">{{ \Illuminate\Support\Carbon::parse($o->created_at)->format('d M Y H:i') }}</td>
                                <td class="text-end"><a href="{{ url('bp-admin/orders/'.$o->id) }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @if(method_exists($orders, 'hasPages') && $orders->hasPages())
                    <div class="mt-3">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
