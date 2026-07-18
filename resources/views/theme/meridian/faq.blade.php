@extends('theme.meridian.layouts.app')

@section('title', 'FAQ')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-4">
                <div class="md-kicker mb-1">{{ $mm ? 'အမေးအဖြေ' : 'Reference' }}</div>
                <h1 class="md-serif" style="font-weight:600;letter-spacing:-.01em;">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently asked questions' }}</h1>
                <p class="md-dek">{{ $mm ? 'အမေးများသော မေးခွန်းများအတွက် အဖြေများ။' : 'Answers to the questions we hear most often.' }}</p>
            </div>

            <div class="accordion accordion-flush" id="faqAccordion">
                @forelse($faqs as $faq)
                    <div class="accordion-item" style="background:transparent;border-bottom:1px solid var(--md-rule);">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed md-serif fs-5 bg-transparent shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}" style="color:var(--md-ink);font-weight:600;">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 md-dek">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center md-dek py-5">{{ $mm ? 'မေးခွန်းများ ထည့်သွင်းထားခြင်း မရှိသေးပါ။' : 'No questions have been published yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
