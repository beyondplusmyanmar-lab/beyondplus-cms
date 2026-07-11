@extends('theme.bptheme4.layouts.app')

@section('title', $post->title)

@section('content')
@php
    $mm = app()->getLocale() === 'mm';
    $pill = fn ($name) => 'c'.(abs(crc32((string) $name)) % 5);
    $postCategories = $post->categories;
    if ($mm && isset($post->translate) && $post->translate->lang == 2) { $post = $post->translate; }
@endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <article class="col-lg-8">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($postCategories as $cat)<a href="{{ url('/cat/'.$cat->tax_link) }}" class="pl-pill {{ $pill($cat->tax_name) }}">{{ $cat->tax_name }}</a>@endforeach
                </div>
                @if(Auth::guard('admins')->check() && $post->post_type == 'post')
                    <a href="{{ url('/bp-admin/post/'.$post->id.'/edit') }}" class="btn btn-pl-soft btn-sm"><i class="bi bi-pencil"></i> {{ $mm ? 'ပြင်ရန်' : 'Edit' }}</a>
                @endif
            </div>
            <h1 class="pl-display mb-3" style="font-size:clamp(1.9rem,4.5vw,2.9rem);line-height:1.12;">{{ $post->title }}</h1>
            <div class="small pl-muted mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-primary"></i> {{ optional($post->creator)->name ?? 'Admin' }} <span>·</span> {{ $post->created_at->translatedFormat('j F Y') }}
            </div>

            @if($post->featured_img)
                <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid mb-4 w-100" style="border-radius:24px;" alt="{{ $post->title }}">
            @endif

            <div class="bp-content">{!! bbParse($post->body) !!}</div>

            @auth
                <hr class="my-4">
                <h5 class="pl-display mb-3">{{ $mm ? 'မှတ်ချက်များ' : 'Comments' }}</h5>
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
                                <div><strong>{{ $author->name }}</strong> <span class="small pl-muted">· {{ $c->created_at->diffForHumans() }}</span><div>{{ $c->body }}</div></div>
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
