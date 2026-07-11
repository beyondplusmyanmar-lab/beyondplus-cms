{{-- Search results --}}
@extends('theme.bptheme4.layouts.app')

@section('title', 'Search')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <span class="pl-eyebrow mb-2">{{ $mm ? 'ရှာဖွေမှု' : 'Search' }}</span>
            <h1 class="pl-display mt-2 mb-4" style="font-size:clamp(1.9rem,4.5vw,2.9rem);">
                @isset($query){{ $mm ? '“'.$query.'” ရလဒ်များ' : 'Results for “'.$query.'”' }}@else{{ $mm ? 'ရှာဖွေရန်' : 'Find a post' }}@endisset
            </h1>

            <form class="mb-5" role="search" action="{{ url('/search') }}" method="GET">
                <div class="input-group input-group-lg">
                    <input class="form-control" type="search" name="q" value="{{ $query ?? '' }}" placeholder="{{ $mm ? 'ရှာဖွေရန်…' : 'Type to search…' }}" aria-label="Search" style="border-radius:14px 0 0 14px;">
                    <button class="btn btn-pl" type="submit" style="border-radius:0 14px 14px 0;"><i class="bi bi-search"></i> {{ $mm ? 'ရှာရန်' : 'Search' }}</button>
                </div>
            </form>

            @isset($posts)
                <div class="row g-4">
                    @forelse($posts as $post)
                        @php if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; } @endphp
                        <div class="col-md-6">
                            <article class="pl-card p-4 h-100">
                                <h2 class="pl-display h5 mb-2"><a href="{{ url('/'.$post->post_link) }}" class="text-reset">{{ $post->title }}</a></h2>
                                <p class="small pl-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 150) }}</p>
                            </article>
                        </div>
                    @empty
                        <div class="col-12"><p class="pl-muted">{{ $mm ? 'ရှာဖွေမှု မတွေ့ရှိပါ။' : 'No results found.' }}</p></div>
                    @endforelse
                </div>
                @if($posts->hasPages())<div class="mt-4">{{ $posts->links() }}</div>@endif
            @else
                <p class="pl-muted">{{ $mm ? 'ရှာဖွေရန် စကားလုံး ရိုက်ထည့်ပါ။' : 'Enter a search term to find content.' }}</p>
            @endisset
        </div>
    </div>
</div>
@stop
