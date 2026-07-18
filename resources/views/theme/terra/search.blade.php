{{-- Search results --}}
@extends('theme.terra.layouts.app')

@section('title', 'Search')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="tr-label mb-3">{{ $mm ? 'ရှာဖွေမှု' : 'search' }}</div>
            <h1 class="tr-display mb-4" style="font-size:clamp(1.8rem,4.5vw,2.8rem);">
                @isset($query){{ $mm ? '“'.$query.'”' : '“'.$query.'”' }}@else{{ $mm ? 'ရှာဖွေရန်' : 'Find a post' }}@endisset
            </h1>

            <form class="mb-5" role="search" action="{{ url('/search') }}" method="GET">
                <div class="input-group input-group-lg">
                    <input class="form-control" type="search" name="q" value="{{ $query ?? '' }}" placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Type to search…' }}" aria-label="Search">
                    <button class="btn btn-tr" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            @isset($posts)
                @forelse($posts as $post)
                    @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                    <div class="tr-row">
                        <a href="{{ url('/'.$post->post_link) }}" class="tr-row-link">
                            <h2 class="tr-row-title mb-1">{{ $post->title }}</h2>
                            <p class="tr-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 160) }}</p>
                        </a>
                    </div>
                @empty
                    <p class="tr-muted">{{ $mm ? 'ရှာဖွေမှု မတွေ့ရှိပါ။' : 'No results found.' }}</p>
                @endforelse
                @if($posts->hasPages())<div class="mt-4">{{ $posts->links() }}</div>@endif
            @else
                <p class="tr-muted">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term to find content.' }}</p>
            @endisset
        </div>
    </div>
</div>
@stop
