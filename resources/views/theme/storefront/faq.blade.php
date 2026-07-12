@extends('theme.storefront.layouts.app')
@section('title', app()->getLocale() === 'mm' ? 'အမေးအဖြေ' : 'FAQ')
@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="sf-panel">
                <h1 class="h4 text-center mb-4">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently Asked Questions' }}</h1>
                <div class="accordion" id="sfFaq">
                    @forelse($faqs as $faq)
                        <div class="accordion-item mb-2 border-0 shadow-sm">
                            <h2 class="accordion-header"><button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#sfFaq{{ $faq->id }}">{{ $faq->question }}</button></h2>
                            <div id="sfFaq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#sfFaq"><div class="accordion-body sf-muted">{!! nl2br(e($faq->answer)) !!}</div></div>
                        </div>
                    @empty
                        <p class="text-center sf-muted py-4">{{ $mm ? 'မေးခွန်းများ မရှိသေးပါ။' : 'No questions published yet.' }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@stop
