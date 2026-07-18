@extends('theme.meridian.layouts.app')

@section('title', 'Events')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .md-cal { width:100%; border-collapse:collapse; table-layout:fixed; }
    .md-cal th { padding:.5rem; text-align:center; font-family:"Inter",sans-serif; font-size:.7rem; text-transform:uppercase; letter-spacing:.14em; color:var(--md-muted); border-bottom:2px solid var(--md-ink); }
    .md-cal td { border:1px solid var(--md-rule); vertical-align:top; height:112px; padding:.35rem; overflow:hidden; }
    .md-cal .daynum { font-family:"Fraunces",serif; font-size:.95rem; color:var(--md-ink); font-weight:600; }
    .md-cal td.out { background:#f4efe6; } .md-cal td.out .daynum { color:#c7bdad; }
    .md-cal td.today .daynum { display:inline-flex; align-items:center; justify-content:center; min-width:1.5rem; height:1.5rem; background:var(--md-burgundy); color:#fff; border-radius:50%; }
    .md-cal .ev { display:block; background:var(--md-burgundy); color:#fff; font-family:"Inter",sans-serif; font-size:.7rem; line-height:1.3; padding:2px 6px; border-radius:2px; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .md-cal .ev:hover { background:var(--md-burgundy-dk); color:#fff; }
    .md-cal .ev .t { opacity:.85; font-weight:600; margin-right:3px; }
    @media (max-width:640px){ .md-cal td { height:76px; } .md-cal .ev { font-size:.6rem; } }
</style>
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 gap-2">
        <div>
            <div class="md-kicker mb-1">{{ $cursor->translatedFormat('Y') }}</div>
            <h1 class="md-serif mb-0" style="font-weight:600;">{{ $mm ? 'ပွဲများ' : 'Events' }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-md-ghost btn-sm"><i class="bi bi-chevron-left"></i></a>
            <span class="md-serif px-2" style="min-width:9rem;text-align:center;font-weight:600;">{{ $cursor->translatedFormat('F Y') }}</span>
            <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-md-ghost btn-sm"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ url('/events') }}" class="btn btn-primary btn-sm">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
        </div>
    </div>

    @php
        $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $end   = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today = \Carbon\Carbon::today();
        $day   = $start->copy();
    @endphp
    <table class="md-cal">
        <thead><tr>@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<th>{{ $d }}</th>@endforeach</tr></thead>
        <tbody>
            @while($day <= $end)
                <tr>
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $dayEvents = $events->get($day->toDateString(), collect());
                            $classes = ($day->month === $cursor->month ? '' : 'out').($day->isSameDay($today) ? ' today' : '');
                        @endphp
                        <td class="{{ trim($classes) }}">
                            <span class="daynum">{{ $day->day }}</span>
                            @foreach($dayEvents as $ev)
                                @php $t = ($mm && isset($ev->translate) && $ev->translate->lang == 2) ? $ev->translate : $ev; @endphp
                                <a class="ev" href="{{ url('/'.$ev->post_link) }}" title="{{ $t->title }} — {{ \Carbon\Carbon::parse($ev->event_at)->format('D, d M Y g:i A') }}">
                                    <span class="t">{{ \Carbon\Carbon::parse($ev->event_at)->format('g:i A') }}</span>{{ $t->title }}
                                </a>
                            @endforeach
                        </td>
                        @php $day->addDay(); @endphp
                    @endfor
                </tr>
            @endwhile
        </tbody>
    </table>
</div>
@stop
