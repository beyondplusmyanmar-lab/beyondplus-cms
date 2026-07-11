{{-- Search results --}}
@extends('theme.bptheme1.layouts.app')

@section('title', 'Search')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="md-kicker mb-1">{{ $mm ? 'ရှာဖွေမှု' : 'Search' }}</div>
            <h1 class="md-serif mb-4" style="font-weight:600;letter-spacing:-.01em;">
                @isset($query){{ $mm ? '“'.$query.'” အတွက် ရလဒ်များ' : 'Results for “'.$query.'”' }}@else{{ $mm ? 'ရှာဖွေရန်' : 'Find a story' }}@endisset
            </h1>

            <form class="mb-4" role="search" action="{{ url('/search') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" value="{{ $query ?? '' }}" placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Search…' }}" aria-label="Search">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> {{ $mm ? 'ရှာရန်' : 'Search' }}</button>
                </div>
            </form>

            @isset($posts)
                @forelse($posts as $post)
                    @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                    <article class="md-story mb-4">
                        <h2 class="md-story-title h5"><a href="{{ url('/'.$post->post_link) }}">{{ $post->title }}</a></h2>
                        <p class="md-dek mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}</p>
                    </article>
                @empty
                    <p class="md-dek">{{ $mm ? 'ရှာဖွေမှု မတွေ့ရှိပါ။' : 'No results found.' }}</p>
                @endforelse
                @if($posts->hasPages())<div class="mt-4">{{ $posts->links() }}</div>@endif
            @else
                <p class="md-dek">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term to find content.' }}</p>
            @endisset
        </div>
        <aside class="col-lg-4">@include('theme.bptheme1.sidebar')</aside>
    </div>
</div>
@stop
