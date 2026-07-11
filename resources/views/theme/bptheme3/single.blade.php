@extends('theme.bptheme3.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $postCategories = $post->categories;
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<article class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <header class="mb-4">
                @if($postCategories->count())
                    <a href="{{ url('/cat/'.$postCategories->first()->tax_link) }}" class="tr-cat">{{ $postCategories->first()->tax_name }}</a>
                @endif
                <h1 class="tr-display my-2" style="font-size:clamp(2rem,5vw,3.2rem);line-height:1.1;">{{ $post->title }}</h1>
                <div class="tr-date d-flex align-items-center gap-2">
                    <span>{{ optional($post->creator)->name ?? 'Admin' }}</span><span>·</span><span>{{ $post->created_at->translatedFormat('j F Y') }}</span>
                    @if(Auth::guard('admins')->check() && $post->post_type == 'post')
                        <a href="{{ url('/bp-admin/post/'.$post->id.'/edit') }}" class="tr-ul ms-1"><i class="bi bi-pencil"></i> {{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>
                    @endif
                </div>
            </header>

            @if($post->featured_img)
                <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded-1 mb-4 w-100" alt="{{ $post->title }}">
            @endif

            <div class="bp-content">{!! bbParse($post->body) !!}</div>

            @if($postCategories->count())
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="tr-date">{{ $mm ? 'ခေါင်းစဉ်များ:' : 'Filed under:' }}</span>
                    @foreach($postCategories as $cat)<a href="{{ url('/cat/'.$cat->tax_link) }}" class="tr-cat tr-ul">{{ $cat->tax_name }}</a>@endforeach
                </div>
            @endif

            @auth
                <hr class="my-4">
                <h5 class="tr-display mb-3">{{ $mm ? 'မှတ်ချက်များ' : 'Comments' }}</h5>
                <div class="d-flex gap-2 mb-4">
                    <img src="{{ Auth::user()->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}" class="rounded-circle" width="42" height="42" alt="you">
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
                                <img src="{{ $author->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}" class="rounded-circle" width="36" height="36" alt="{{ $author->name }}">
                                <div><strong>{{ $author->name }}</strong> <span class="tr-date">· {{ $c->created_at->diffForHumans() }}</span><div>{{ $c->body }}</div></div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endauth
        </div>
    </div>
</article>
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
