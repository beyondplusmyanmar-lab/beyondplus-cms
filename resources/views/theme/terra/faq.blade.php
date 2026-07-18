@extends('theme.terra.layouts.app')

@section('title', 'FAQ')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    #trFaq .accordion-item { background:transparent; border:0; border-top:1px solid var(--tr-line); }
    #trFaq .accordion-item:last-child { border-bottom:1px solid var(--tr-line); }
    #trFaq .accordion-button { background:transparent; color:var(--tr-ink); font-family:"Sora",sans-serif; font-weight:600; font-size:1.15rem; box-shadow:none; padding:1.3rem 0; }
    #trFaq .accordion-button:not(.collapsed) { color:var(--tr-sage-dk); }
    #trFaq .accordion-body { color:var(--tr-muted); padding:0 0 1.3rem; }
</style>
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="tr-label mb-3">{{ $mm ? 'အမေးအဖြေ' : 'faq' }}</div>
            <h1 class="tr-display mb-5" style="font-size:clamp(2rem,5vw,3rem);">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently asked questions' }}</h1>

            <div class="accordion accordion-flush" id="trFaq">
                @forelse($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">{{ $faq->question }}</button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#trFaq">
                            <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="tr-muted py-5">{{ $mm ? 'မေးခွန်းများ ထည့်သွင်းထားခြင်း မရှိသေးပါ။' : 'No questions have been published yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
