{{-- Search results template --}}
@extends('theme.default.layouts.app')

@section('title', 'Search')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            @include('theme.default.sidebar')
        </aside>

        <div class="col-lg-9">
            <h1 class="h3 mb-4">
                @isset($query)
                    {{ $mm ? '“'.$query.'” အတွက် ရှာဖွေမှုရလဒ်များ' : 'Search results for “'.$query.'”' }}
                @else
                    {{ $mm ? 'ရှာဖွေမှု' : 'Search' }}
                @endisset
            </h1>

            {{-- Search box (also in the header) --}}
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
                        if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
                            $post = $post->translate;
                        }
                    @endphp
                    <article class="mb-4 pb-4 border-bottom">
                        <h2 class="h5">
                            <a href="{{ url('/'.$post->post_link) }}" class="text-dark">{{ $post->title }}</a>
                        </h2>
                        <p class="text-body mb-0">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                        </p>
                    </article>
                @empty
                    <p class="text-muted">{{ $mm ? 'ရှာဖွေမှု မတွေ့ရှိပါ။' : 'No results found.' }}</p>
                @endforelse

                @if($posts->hasPages())
                    <div class="mt-4">{{ $posts->links() }}</div>
                @endif
            @else
                <p class="text-muted">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term to find content.' }}</p>
            @endisset
        </div>
    </div>
</div>
@stop
