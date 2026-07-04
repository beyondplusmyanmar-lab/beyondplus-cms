@extends('theme.default.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="bp-section-title text-center mb-2">Frequently Asked Questions</h1>
            <p class="text-muted text-center mb-4">Answers to the questions we hear most often.</p>

            <div class="accordion" id="faqAccordion">
                @forelse($faqs as $faq)
                    <div class="accordion-item mb-2 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted py-5">No questions have been published yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop
