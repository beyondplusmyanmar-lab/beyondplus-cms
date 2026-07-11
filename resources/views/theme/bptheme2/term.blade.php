{{-- Category / term listing --}}
@extends('theme.bptheme2.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="nc-eyebrow mb-1">{{ $mm ? 'ကဏ္ဍ' : 'Category' }}</div>
            <h1 class="h2 text-light mb-4">{{ $mm ? 'ဆောင်းပါးများ' : 'Stories in this category' }}</h1>

            @forelse($posts as $post)
                @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                <article class="nc-card p-4 mb-3">
                    <h2 class="h4"><a href="{{ url('/'.$post->post_link) }}" class="text-light">{{ $post->title }}</a></h2>
                    <p class="nc-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}</p>
                </article>
            @empty
                <p class="nc-muted">{{ $mm ? 'ဤကဏ္ဍတွင် ဆောင်းပါးများ မရှိသေးပါ။' : 'No stories in this category yet.' }}</p>
            @endforelse

            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
        <aside class="col-lg-4">@include('theme.bptheme2.sidebar')</aside>
    </div>
</div>
@stop
