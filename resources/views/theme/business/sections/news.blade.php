{{-- Latest News / Blog — pulls the newest published CMS posts. Hidden if none. --}}
@php
    $mm = app()->getLocale() === 'mm';
    $posts = bp_post((int) bp_option('biz_news_count', '3'));
@endphp

@if($posts->count())
<section id="news" class="bz-section bz-section--alt">
    <div class="container">
        <div class="bz-section-head">
            <span class="bz-eyebrow">{{ $mm ? 'သတင်း' : 'News' }}</span>
            <h2 class="mt-2">{{ bp_option('biz_news_title', $mm ? 'နောက်ဆုံး သတင်းများ' : 'Latest News' ) }}</h2>
        </div>
        <div class="row g-4">
            @foreach($posts as $post)
                @php
                    $postCategory = optional($post->categories->firstWhere('tax_link', '!=', 'uncategorized') ?? $post->categories->first());
                    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                @endphp
                <div class="col-lg-4 col-sm-6">
                    <article class="bz-card h-100 overflow-hidden">
                        @if($post->featured_img)
                            <a href="{{ url('/'.$post->post_link) }}">
                                <img src="{{ bp_upload_url($post->featured_img) }}" class="w-100" style="aspect-ratio:16/10;object-fit:cover;" alt="{{ $post->title }}">
                            </a>
                        @endif
                        <div class="p-4">
                            @if($postCategory->tax_name)
                                <a href="{{ url('/cat/'.$postCategory->tax_link) }}" class="badge text-white text-decoration-none mb-2" style="background:var(--bz-primary);">{{ $postCategory->tax_name }}</a>
                            @endif
                            <h5 class="h6"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--bz-text);">{{ $post->title }}</a></h5>
                            <p class="bz-muted small mb-3">{{ \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', strip_tags($post->body)), 100) }}</p>
                            <div class="bz-muted small"><i class="bi bi-calendar3"></i> {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ url('/blog') }}" class="btn btn-outline-primary">{{ $mm ? 'သတင်းအားလုံး' : 'View all news' }} <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif
