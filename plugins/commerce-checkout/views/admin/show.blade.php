@extends('bp-admin.layouts.admin.index')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header d-flex justify-content-between align-items-center" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-shopping-bag"></i> Order <code>{{ $order->order_number }}</code></h4>
                <a href="{{ url('bp-admin/orders') }}" class="btn btn-sm btn-outline-secondary">Back to orders</a>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <table class="table mb-4">
                    <thead><tr><th>Item</th><th style="width:80px">Qty</th><th style="width:130px">Price</th><th class="text-end" style="width:140px">Total</th></tr></thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr>
                                <td>{{ $it->name }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ number_format($it->price) }} {{ $currency }}</td>
                                <td class="text-end">{{ number_format($it->line_total) }} {{ $currency }}</td>
                            </tr>
                        @endforeach
                        <tr><td colspan="3" class="text-end fw-bold">Subtotal</td><td class="text-end fw-bold">{{ number_format($order->subtotal) }} {{ $currency }}</td></tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-7">
                        <h6 class="text-muted">Customer</h6>
                        <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                        <p class="mb-1"><i class="fa fa-phone"></i> {{ $order->customer_phone }}</p>
                        @if($order->customer_email)<p class="mb-1"><i class="fa fa-envelope"></i> {{ $order->customer_email }}</p>@endif
                        <p class="mb-1"><i class="fa fa-map-marker"></i> {{ $order->address }}</p>
                        @if($order->note)<p class="mb-1 text-muted"><em>{{ $order->note }}</em></p>@endif
                    </div>
                    <div class="col-md-5">
                        <h6 class="text-muted">Status</h6>
                        <form method="post" action="{{ url('bp-admin/orders/'.$order->id.'/status') }}" class="d-flex gap-2">
                            {{ csrf_field() }}
                            <select name="status" class="form-control form-control-sm">
                                @foreach(['new','confirmed','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                        <p class="small text-muted mt-2">Placed {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
