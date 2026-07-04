@extends('theme.default.layouts.app')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .bp-cal { width:100%; border-collapse:collapse; table-layout:fixed; }
    .bp-cal th { padding:.5rem; text-align:center; font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8; border-bottom:1px solid #e5e7eb; }
    .bp-cal td { border:1px solid #eef0f3; vertical-align:top; height:112px; padding:.35rem; overflow:hidden; }
    .bp-cal .daynum { font-size:.8rem; color:#475569; font-weight:600; }
    .bp-cal td.out { background:#fafbfc; } .bp-cal td.out .daynum { color:#cbd5e1; }
    .bp-cal td.today { background:#f0fdfa; }
    .bp-cal td.today .daynum { display:inline-flex; align-items:center; justify-content:center; min-width:1.4rem; height:1.4rem; background:var(--bp-accent); color:#fff; border-radius:50%; }
    .bp-cal .ev { display:block; background:var(--bp-accent); color:#fff; font-size:.72rem; line-height:1.3; padding:2px 6px; border-radius:4px; margin-top:3px; text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .bp-cal .ev:hover { background:var(--bp-accent-dark); color:#fff; }
    .bp-cal .ev .t { opacity:.85; font-weight:600; margin-right:3px; }
    @media (max-width:640px){ .bp-cal td { height:76px; } .bp-cal .ev { font-size:.6rem; } }
</style>
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4" style="gap:.5rem;">
        <h1 class="bp-section-title mb-0">{{ $mm ? 'ပွဲများ' : 'Events' }}</h1>
        <div class="d-flex align-items-center" style="gap:.4rem;">
            <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
            <span class="fw-semibold px-2" style="min-width:9rem;text-align:center;">{{ $cursor->format('F Y') }}</span>
            <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ url('/events') }}" class="btn btn-sm btn-primary">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
        </div>
    </div>

    @php
        $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $end   = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today = \Carbon\Carbon::today();
        $day   = $start->copy();
    @endphp
    <table class="bp-cal">
        <thead>
            <tr>@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<th>{{ $d }}</th>@endforeach</tr>
        </thead>
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
