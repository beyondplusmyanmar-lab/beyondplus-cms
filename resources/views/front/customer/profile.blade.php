@extends('theme.'.(optional(site_information('theme'))->option_value ?: 'default').'.layouts.app')

@section('title', 'My profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                             style="width:64px;height:64px;font-size:1.5rem;">
                            {{ strtoupper(substr($customer->first_name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="h4 mb-0">{{ trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')) ?: 'My profile' }}</h1>
                            <span class="text-muted">Member since {{ optional($customer->created_at)->format('M Y') }}</span>
                        </div>
                    </div>

                    @include('front.customer.partials.messages')

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">Phone</dt>
                        <dd class="col-sm-8">{{ $customer->phone ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Email</dt>
                        <dd class="col-sm-8">{{ $customer->email ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Reward points</dt>
                        <dd class="col-sm-8">{{ number_format($customer->total_reward_points ?? 0) }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Status</dt>
                        <dd class="col-sm-8">
                            @if($customer->status)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif
                        </dd>
                    </dl>

                    <hr class="my-4">
                    <a href="{{ url('/customer/logout') }}" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Log out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
