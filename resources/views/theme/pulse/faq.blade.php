@extends('theme.pulse.layouts.app')

@section('title', 'FAQ')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    #plFaq .accordion-item { background:#fff; border:1px solid var(--pl-line); border-radius:20px !important; margin-bottom:.75rem; overflow:hidden; }
    #plFaq .accordion-button { background:#fff; color:var(--pl-ink); font-family:"Poppins",sans-serif; font-weight:600; box-shadow:none; }
    #plFaq .accordion-button:not(.collapsed) { color:var(--pl-indigo); background:var(--pl-soft); }
    #plFaq .accordion-body { color:var(--pl-muted); }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-4">
                <span class="pl-eyebrow mb-2">{{ $mm ? 'အမေးအဖြေ' : 'Help' }}</span>
                <h1 class="pl-display mt-3 mb-2"><span class="pl-grad-text">{{ $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently asked questions' }}</span></h1>
                <p class="pl-muted">{{ $mm ? 'အမေးများသော မေးခွန်းများအတွက် အဖြေများ။' : 'Answers to the questions we hear most often.' }}</p>
            </div>

            <div class="accordion" id="plFaq">
                @forelse($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">{{ $faq->question }}</button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#plFaq">
                            <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center pl-muted py-5">{{ $mm ? 'မေးခွန်းများ ထည့်သွင်းထားခြင်း မရှိသေးပါ။' : 'No questions have been published yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
