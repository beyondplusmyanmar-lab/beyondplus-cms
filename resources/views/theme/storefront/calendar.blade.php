@extends('theme.storefront.layouts.app')
@section('title', app()->getLocale() === 'mm' ? 'ပွဲများ' : 'Events')
@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .sf-cal { width:100%; border-collapse:collapse; table-layout:fixed; }
    .sf-cal th { padding:.5rem; text-align:center; font-size:.72rem; text-transform:uppercase; color:var(--sf-muted); border-bottom:1px solid var(--sf-border); }
    .sf-cal td { border:1px solid var(--sf-border); vertical-align:top; height:100px; padding:.3rem; overflow:hidden; }
    .sf-cal .daynum { font-size:.8rem; color:var(--sf-muted); font-weight:600; }
    .sf-cal td.out { background:var(--sf-bg); } .sf-cal td.today .daynum { background:var(--sf-primary); color:#fff; border-radius:50%; padding:0 .35rem; }
    .sf-cal .ev { display:block; background:var(--sf-primary); color:#fff; font-size:.7rem; padding:2px 5px; border-radius:4px; margin-top:3px; text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
</style>
<div class="container py-4">
    <div class="sf-panel">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap:.5rem;">
            <h1 class="h4 mb-0">{{ $mm ? 'ပွဲများ' : 'Events' }}</h1>
            <div class="d-flex align-items-center" style="gap:.4rem;">
                <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                <span class="fw-semibold px-2" style="min-width:9rem;text-align:center;">{{ $cursor->format('F Y') }}</span>
                <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
                <a href="{{ url('/events') }}" class="btn btn-sm btn-primary">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
            </div>
        </div>
        @php
            $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $end = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
            $today = \Carbon\Carbon::today(); $day = $start->copy();
        @endphp
        <table class="sf-cal">
            <thead><tr>@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<th>{{ $d }}</th>@endforeach</tr></thead>
            <tbody>
                @while($day <= $end)
                    <tr>
                        @for($i = 0; $i < 7; $i++)
                            @php $dayEvents = $events->get($day->toDateString(), collect()); $classes = ($day->month === $cursor->month ? '' : 'out').($day->isSameDay($today) ? ' today' : ''); @endphp
                            <td class="{{ trim($classes) }}">
                                <span class="daynum">{{ $day->day }}</span>
                                @foreach($dayEvents as $ev)
                                    @php $tv = ($mm && isset($ev->translate) && $ev->translate->lang == 2) ? $ev->translate : $ev; @endphp
                                    <a class="ev" href="{{ url('/'.$ev->post_link) }}" title="{{ $tv->title }}">{{ \Carbon\Carbon::parse($ev->event_at)->format('g:i A') }} {{ $tv->title }}</a>
                                @endforeach
                            </td>
                            @php $day->addDay(); @endphp
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>
    </div>
</div>
@stop
