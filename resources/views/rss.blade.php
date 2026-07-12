{!! '<?xml version="1.0" encoding="UTF-8"?>'."\n" !!}
@php
    $siteName = optional(site_information('blogname'))->option_value ?: config('app.name');
    $siteDesc = optional(site_information('blogdescription'))->option_value ?: $siteName;
@endphp
<rss version="2.0">
    <channel>
        <title>{{ $siteName }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ $siteDesc }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
@foreach($posts as $post)
@php
    $link = trim((string) ($post->post_link ?? ''));
    $active = ($post->post_active ?? 'yes') === 'yes';
@endphp
@if($active && $link !== '')
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ url('/'.$link) }}</link>
            <guid>{{ url('/'.$link) }}</guid>
@if(!empty($post->created_at))
            <pubDate>{{ \Illuminate\Support\Carbon::parse($post->created_at)->toRssString() }}</pubDate>
@endif
            <description>{{ \Illuminate\Support\Str::limit(strip_tags(str_replace('&nbsp;', ' ', (string) $post->body)), 300) }}</description>
        </item>
@endif
@endforeach
    </channel>
</rss>
