@extends('theme.bptheme2.layouts.app')

@section('title', 'FAQ')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    #ncFaq .accordion-item { background:var(--nc-panel); border:1px solid var(--nc-border); border-radius:14px !important; margin-bottom:.65rem; overflow:hidden; }
    #ncFaq .accordion-button { background:transparent; color:#ece9f6; font-weight:600; box-shadow:none; }
    #ncFaq .accordion-button:not(.collapsed) { color:var(--nc-cyan); background:rgba(168,85,247,.08); }
    #ncFaq .accordion-button::after { filter:invert(1) grayscale(1) brightness(1.6); }
    #ncFaq .accordion-body { color:var(--nc-muted); }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-4">
                <div class="nc-eyebrow mb-1">{{ $mm ? 'အမေးအဖြေ' : 'Help' }}</div>
                <h1 class="nc-gradient-text display-6 fw-bold">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently asked questions' }}</h1>
                <p class="nc-muted">{{ $mm ? 'အမေးများသော မေးခွန်းများအတွက် အဖြေများ။' : 'Answers to the questions we hear most often.' }}</p>
            </div>

            <div class="accordion" id="ncFaq">
                @forelse($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">{{ $faq->question }}</button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#ncFaq">
                            <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center nc-muted py-5">{{ $mm ? 'မေးခွန်းများ ထည့်သွင်းထားခြင်း မရှိသေးပါ။' : 'No questions have been published yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
