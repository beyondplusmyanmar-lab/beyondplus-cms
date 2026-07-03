@extends('theme.default.layouts.app')

@section('title', $post->title)

@section('content')
@php
    if (app()->getLocale() === 'mm' && isset($post->translate) && $post->translate->lang == 2) {
        $post = $post->translate;
    }
@endphp
<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            @include('theme.default.sidebar')
        </aside>

        <article class="col-lg-9">
            <div class="d-flex justify-content-between align-items-start">
                <h1 class="h2 mb-3">{{ $post->title }}</h1>
                @if(Auth::guard('admins')->check() && $post->post_type == 'post')
                    <a href="{{ url('/bp-admin/post/'.$post->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
            </div>

            @if($post->featured_img)
                <img src="{{ bp_upload_url($post->featured_img) }}" class="img-fluid rounded mb-4" alt="{{ $post->title }}">
            @endif

            <div class="bp-content">
                {!! bbParse($post->body) !!}
            </div>

            {{-- Comments --}}
            @auth
                <hr class="my-4">
                <h5 class="mb-3">Comments</h5>

                <div class="d-flex gap-2 mb-4">
                    <img src="{{ Auth::user()->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}"
                         class="rounded-circle" width="48" height="48" alt="you">
                    <div class="flex-grow-1">
                        {{ csrf_field() }}
                        <input type="text" class="form-control" id="comment" name="comment" placeholder="Write a comment and press Enter…">
                    </div>
                </div>

                @if($post->comment)
                    @foreach($post->comment as $c)
                        @php $author = $c->users()->find($c->user_id); @endphp
                        @if($author)
                            <div class="d-flex gap-2 mb-3">
                                <img src="{{ $author->avatar ?: asset('/img/blank_profile_pic_60x60.jpg') }}"
                                     class="rounded-circle" width="40" height="40" alt="{{ $author->name }}">
                                <div>
                                    <strong>{{ $author->name }}</strong>
                                    <span class="text-muted small">&middot; {{ $c->created_at->diffForHumans() }}</span>
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
                    data: {
                        body: $('#comment').val(),
                        _token: $('input[name=_token]').val(),
                        post_id: '{{ $post->id }}'
                    },
                    success: function (data) {
                        if (data == 1) { location.reload(); }
                    }
                });
            }
        });
    });
</script>
@endpush
