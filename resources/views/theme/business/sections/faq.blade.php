{{-- FAQ — accordion of the top published questions. Hidden when FAQ is
     disabled or no questions exist. Uses the same Faq model as the /faq page. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $faqs = collect();
    if (bp_option('faq_enabled', 'yes') === 'yes') {
        $faqs = \App\Models\Faq::where('is_active', 1)
            ->orderBy('sort_order')->orderBy('id')
            ->limit((int) bp_option('biz_faq_count', '6'))->get();
    }
@endphp

@if($faqs->count())
<section id="faq" class="bz-section">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'အမေးအဖြေ' : 'FAQ' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_faq_title', $mm ? 'မေးလေ့ရှိသော မေးခွန်းများ' : 'Frequently Asked Questions') }}</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion" id="bzFaq">
                    @foreach($faqs as $faq)
                        <div class="accordion-item mb-2 border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#bzFaq{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="bzFaq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#bzFaq">
                                <div class="accordion-body bz-muted">{!! nl2br(e($faq->answer)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ url('/faq') }}" class="btn btn-outline-primary btn-sm">{{ $mm ? 'မေးခွန်းအားလုံး' : 'See all questions' }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
