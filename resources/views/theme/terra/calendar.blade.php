@extends('theme.terra.layouts.app')

@section('title', 'Events')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .tr-cal { width:100%; border-collapse:collapse; table-layout:fixed; }
    .tr-cal th { padding:.6rem .4rem; text-align:left; font-family:"Sora",sans-serif; font-size:.7rem; text-transform:lowercase; letter-spacing:.04em; color:var(--tr-muted); border-bottom:2px solid var(--tr-ink); }
    .tr-cal td { border-bottom:1px solid var(--tr-line); vertical-align:top; height:118px; padding:.5rem .35rem; overflow:hidden; }
    .tr-cal .daynum { font-family:"Sora",sans-serif; font-size:.82rem; color:var(--tr-ink); font-weight:600; }
    .tr-cal td.out .daynum { color:#c6c6bb; }
    .tr-cal td.today .daynum { color:var(--tr-sage-dk); text-decoration:underline; text-underline-offset:3px; }
    .tr-cal .ev { display:block; font-size:.72rem; line-height:1.3; padding:2px 0 2px 8px; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--tr-ink); border-left:2px solid var(--tr-sage); }
    .tr-cal .ev:hover { color:var(--tr-sage-dk); }
    .tr-cal .ev .t { color:var(--tr-muted); font-weight:600; margin-right:4px; }
    @media (max-width:640px){ .tr-cal td { height:80px; } .tr-cal .ev { font-size:.62rem; } }
</style>
<div class="container" style="padding-top:3.5rem;padding-bottom:3.5rem;">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 gap-2">
        <div>
            <div class="tr-label mb-2">{{ $mm ? 'ပြက္ခဒိန်' : 'calendar' }}</div>
            <h1 class="tr-display mb-0" style="font-size:clamp(1.8rem,4.5vw,2.8rem);">{{ $cursor->translatedFormat('F Y') }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-tr-ghost btn-sm"><i class="bi bi-chevron-left"></i></a>
            <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-tr-ghost btn-sm"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ url('/events') }}" class="btn btn-tr btn-sm">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
        </div>
    </div>

    @php
        $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $end   = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today = \Carbon\Carbon::today();
        $day   = $start->copy();
    @endphp
    <table class="tr-cal">
        <thead><tr>@foreach(['sun','mon','tue','wed','thu','fri','sat'] as $d)<th>{{ $d }}</th>@endforeach</tr></thead>
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
