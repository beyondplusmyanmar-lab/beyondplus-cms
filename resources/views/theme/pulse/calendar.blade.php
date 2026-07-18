@extends('theme.pulse.layouts.app')

@section('title', 'Events')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .pl-cal { width:100%; border-collapse:separate; border-spacing:6px; table-layout:fixed; }
    .pl-cal th { padding:.5rem; text-align:center; font-family:"Poppins",sans-serif; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--pl-muted); }
    .pl-cal td { background:#fff; border:1px solid var(--pl-line); border-radius:16px; vertical-align:top; height:114px; padding:.45rem; overflow:hidden; }
    .pl-cal .daynum { font-family:"Poppins",sans-serif; font-size:.82rem; color:var(--pl-ink); font-weight:600; }
    .pl-cal td.out { background:var(--pl-soft); } .pl-cal td.out .daynum { color:#c3bcda; }
    .pl-cal td.today { border-color:var(--pl-indigo); box-shadow:0 0 0 2px rgba(109,94,252,.25); }
    .pl-cal td.today .daynum { color:#fff; background:var(--pl-grad); border-radius:999px; padding:.05rem .5rem; }
    .pl-cal .ev { display:block; font-family:"Inter",sans-serif; font-size:.7rem; line-height:1.3; padding:2px 7px; border-radius:999px; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#fff; background:var(--pl-grad); background-size:180% 100%; }
    .pl-cal .ev:hover { color:#fff; background-position:100% 0; }
    .pl-cal .ev .t { opacity:.9; font-weight:600; margin-right:3px; }
    @media (max-width:640px){ .pl-cal td { height:78px; } .pl-cal .ev { font-size:.6rem; } }
</style>
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 gap-2">
        <div>
            <span class="pl-eyebrow mb-2">{{ $mm ? 'ပြက္ခဒိန်' : 'Calendar' }}</span>
            <h1 class="pl-display mt-2 mb-0 h3">{{ $mm ? 'ပွဲများ' : 'Events' }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-pl-soft btn-sm"><i class="bi bi-chevron-left"></i></a>
            <span class="pl-display px-2" style="min-width:9rem;text-align:center;">{{ $cursor->translatedFormat('F Y') }}</span>
            <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-pl-soft btn-sm"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ url('/events') }}" class="btn btn-pl btn-sm">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
        </div>
    </div>

    @php
        $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $end   = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today = \Carbon\Carbon::today();
        $day   = $start->copy();
    @endphp
    <table class="pl-cal">
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
