@extends('theme.storefront.layouts.app')
@section('title', 'Search')
@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-4">
    <div class="sf-panel">
        <h1 class="h4 mb-3">@isset($query){{ $mm ? '“'.$query.'” ရလဒ်များ' : 'Results for “'.$query.'”' }}@else{{ $mm ? 'ရှာဖွေမှု' : 'Search' }}@endisset</h1>
        <form class="mb-4" role="search" action="{{ url('/search') }}" method="GET">
            <div class="input-group">
                <input class="form-control" type="search" name="q" value="{{ $query ?? '' }}" placeholder="{{ $mm ? 'ရှာရန်…' : 'Search…' }}">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
        @isset($posts)
            @forelse($posts as $post)
                @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                <article class="mb-3 pb-3 border-bottom">
                    <h2 class="h6"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--sf-text);">{{ $post->title }}</a></h2>
                    <p class="mb-0 small">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 180) }}</p>
                </article>
            @empty
                <p class="sf-muted">{{ $mm ? 'ရလဒ် မတွေ့ပါ။' : 'No results found.' }}</p>
            @endforelse
            @if($posts->hasPages())<div class="mt-3">{{ $posts->links() }}</div>@endif
        @else
            <p class="sf-muted">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term.' }}</p>
        @endisset
    </div>
</div>
@stop
