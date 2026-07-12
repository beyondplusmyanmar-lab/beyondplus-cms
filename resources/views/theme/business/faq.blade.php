{{-- FAQ page (/faq) — full accordion of every published question. --}}
@extends('theme.business.layouts.app')

@section('title', app()->getLocale() === 'mm' ? 'အမေးအဖြေ' : 'FAQ')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-5">
                <span class="bz-eyebrow">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</span>
                <h1 class="mt-2 mb-2">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently Asked Questions' }}</h1>
                <p class="bz-muted mb-0">{{ $mm ? 'အမေးများသော မေးခွန်းများအတွက် အဖြေများ။' : 'Answers to the questions we hear most often.' }}</p>
            </div>

            <div class="accordion" id="bzFaqPage">
                @forelse($faqs as $faq)
                    <div class="accordion-item mb-2 border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#bzFaqPage{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="bzFaqPage{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#bzFaqPage">
                            <div class="accordion-body bz-muted">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center bz-muted py-5">{{ $mm ? 'မေးခွန်းများ ထည့်သွင်းထားခြင်း မရှိသေးပါ။' : 'No questions have been published yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
