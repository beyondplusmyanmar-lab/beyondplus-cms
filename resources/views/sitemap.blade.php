{!! '<?xml version="1.0" encoding="UTF-8"?>'."\n" !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
@foreach($posts as $post)
@php
    $link = trim((string) ($post->post_link ?? ''));
    $active = ($post->post_active ?? 'yes') === 'yes';
@endphp
@if($active && $link !== '')
    <url>
        <loc>{{ url('/'.$link) }}</loc>
@if(!empty($post->updated_at))
        <lastmod>{{ \Illuminate\Support\Carbon::parse($post->updated_at)->toAtomString() }}</lastmod>
@endif
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
@endif
@endforeach
</urlset>
