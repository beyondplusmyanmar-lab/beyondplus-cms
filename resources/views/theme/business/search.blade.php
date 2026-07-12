{{-- Search results --}}
@extends('theme.business.layouts.app')

@section('title', 'Search')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h1 class="h3 mb-4">
                @isset($query)
                    {{ $mm ? '“'.$query.'” အတွက် ရှာဖွေမှုရလဒ်များ' : 'Search results for “'.$query.'”' }}
                @else
                    {{ $mm ? 'ရှာဖွေမှု' : 'Search' }}
                @endisset
            </h1>

            <form class="mb-4" role="search" action="{{ url('/search') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" value="{{ $query ?? '' }}"
                           placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Search…' }}" aria-label="Search">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> {{ $mm ? 'ရှာရန်' : 'Search' }}</button>
                </div>
            </form>

            @isset($posts)
                @forelse($posts as $post)
                    @php
                        if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
                    @endphp
                    <article class="bz-card mb-3 p-4">
                        <h2 class="h5"><a href="{{ url('/'.$post->post_link) }}" style="color:var(--bz-text);">{{ $post->title }}</a></h2>
                        <p class="mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}</p>
                    </article>
                @empty
                    <p class="bz-muted">{{ $mm ? 'ရှာဖွေမှု မတွေ့ရှိပါ။' : 'No results found.' }}</p>
                @endforelse

                @if($posts->hasPages())
                    <div class="mt-4">{{ $posts->links() }}</div>
                @endif
            @else
                <p class="bz-muted">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term to find content.' }}</p>
            @endisset
        </div>
        <aside class="col-lg-4">
            @include('theme.business.sidebar')
        </aside>
    </div>
</div>
@stop
