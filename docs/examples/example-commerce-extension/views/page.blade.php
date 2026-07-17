@extends('bp-admin.layouts.admin.index')

@section('title', "Today's takings")

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-body">
                <h4 class="mb-3"><i class="fa fa-line-chart"></i> Today's takings</h4>
                @if ($error !== null)
                    <div class="alert alert-warning mb-0">{{ $error }}</div>
                @else
                    <p><strong>{{ $summary['count'] }}</strong> order(s) today (UTC).</p>
                    @forelse ($summary['totals'] as $cur => $amount)
                        <div style="font-size:22px; font-variant-numeric:tabular-nums;">{{ $amount }} {{ $cur }}</div>
                    @empty
                        <p class="text-muted mb-0">No priced orders yet.</p>
                    @endforelse
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
