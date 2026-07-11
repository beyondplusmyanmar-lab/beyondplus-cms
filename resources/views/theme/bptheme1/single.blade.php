@extends('theme.bptheme1.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $postCategories = $post->categories;
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <article class="col-lg-8 md-article mx-auto">
            <header class="text-center mb-4">
                @if($postCategories->count())
                    <a href="{{ url('/cat/'.$postCategories->first()->tax_link) }}" class="md-kicker">{{ $postCategories->first()->tax_name }}</a>
                @endif
                <h1 class="md-article-title display-6 my-3">{{ $post->title }}</h1>
                <div class="md-byline">
                    {{ $mm ? 'ရေးသားသူ' : 'By' }} {{ optional($post->creator)->name ?? 'Editorial' }}
                    <span class="mx-1">·</span> {{ $post->created_at->translatedFormat('j F Y') }}
                    @if(Auth::guard('admins')->check() && $post->post_type == 'post')
                        <a href="{{ url('/bp-admin/post/'.$post->id.'/edit') }}" class="ms-2"><i class="bi bi-pencil"></i> {{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>
                    @endif
                </div>
            </header>

            @if($post->featured_img)
                <figure class="mb-4">
                    <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded-1 w-100" alt="{{ $post->title }}">
                </figure>
            @endif

            <div class="bp-content mx-auto">
                {!! bbParse($post->body) !!}
            </div>

            @if($postCategories->count())
                <hr class="md-hairline my-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="md-byline me-1">{{ $mm ? 'ခေါင်းစဉ်များ' : 'Filed under' }}</span>
                    @foreach($postCategories as $cat)
                        <a href="{{ url('/cat/'.$cat->tax_link) }}" class="md-tag">{{ $cat->tax_name }}</a>
                    @endforeach
                </div>
            @endif

            {{-- Comments --}}
            @auth
                <hr class="md-hairline my-4">
                <h5 class="md-serif mb-3">{{ $mm ? 'မှတ်ချက်များ' : 'Comments' }}</h5>
                <div class="d-flex gap-2 mb-4">
                    <img src="{{ Auth::user()->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}" class="rounded-circle" width="44" height="44" alt="you">
                    <div class="flex-grow-1">
                        {{ csrf_field() }}
                        <input type="text" class="form-control" id="comment" name="comment" placeholder="{{ $mm ? 'မှတ်ချက်ရေးပြီး Enter နှိပ်ပါ…' : 'Write a comment and press Enter…' }}">
                    </div>
                </div>
                @if($post->comment)
                    @foreach($post->comment as $c)
                        @php $author = $c->users()->find($c->user_id); @endphp
                        @if($author)
                            <div class="d-flex gap-2 mb-3">
                                <img src="{{ $author->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}" class="rounded-circle" width="38" height="38" alt="{{ $author->name }}">
                                <div>
                                    <strong>{{ $author->name }}</strong>
                                    <span class="md-byline">· {{ $c->created_at->diffForHumans() }}</span>
                                    <div>{{ $c->body }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endauth
        </article>
    </div>
</div>
@stop

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(function () {
        $('#comment').keypress(function (e) {
            if (e.which === 13) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url('/comment') }}',
                    data: { body: $('#comment').val(), _token: $('input[name=_token]').val(), post_id: '{{ $post->id }}' },
                    success: function (data) { if (data == 1) { location.reload(); } }
                });
            }
        });
    });
</script>
@endpush
