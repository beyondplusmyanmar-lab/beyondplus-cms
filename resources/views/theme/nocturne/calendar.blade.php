@extends('theme.nocturne.layouts.app')

@section('title', 'Events')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .nc-cal { width:100%; border-collapse:separate; border-spacing:4px; table-layout:fixed; }
    .nc-cal th { padding:.5rem; text-align:center; font-size:.7rem; text-transform:uppercase; letter-spacing:.14em; color:var(--nc-muted); }
    .nc-cal td { background:var(--nc-panel); border:1px solid var(--nc-border); border-radius:10px; vertical-align:top; height:112px; padding:.4rem; overflow:hidden; }
    .nc-cal .daynum { font-family:"Space Grotesk",sans-serif; font-size:.85rem; color:#d7cfe9; font-weight:600; }
    .nc-cal td.out { opacity:.4; } .nc-cal td.today { border-color:var(--nc-violet); box-shadow:0 0 0 1px var(--nc-violet); }
    .nc-cal td.today .daynum { color:var(--nc-cyan); }
    .nc-cal .ev { display:block; font-size:.7rem; line-height:1.3; padding:2px 6px; border-radius:6px; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#e9d5ff; background:rgba(168,85,247,.22); border:1px solid rgba(168,85,247,.4); }
    .nc-cal .ev:hover { background:rgba(34,211,238,.22); border-color:rgba(34,211,238,.5); color:#cffafe; }
    .nc-cal .ev .t { opacity:.85; font-weight:600; margin-right:3px; }
    @media (max-width:640px){ .nc-cal td { height:76px; } .nc-cal .ev { font-size:.6rem; } }
</style>
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 gap-2">
        <div>
            <div class="nc-eyebrow mb-1">{{ $mm ? 'ပြက္ခဒိန်' : 'Calendar' }}</div>
            <h1 class="h3 text-light mb-0">{{ $mm ? 'ပွဲများ' : 'Events' }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/events?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-nc-ghost btn-sm"><i class="bi bi-chevron-left"></i></a>
            <span class="nc-display fw-semibold px-2" style="min-width:9rem;text-align:center;">{{ $cursor->translatedFormat('F Y') }}</span>
            <a href="{{ url('/events?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-nc-ghost btn-sm"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ url('/events') }}" class="btn btn-nc btn-sm">{{ $mm ? 'ယနေ့' : 'Today' }}</a>
        </div>
    </div>

    @php
        $start = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $end   = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $today = \Carbon\Carbon::today();
        $day   = $start->copy();
    @endphp
    <table class="nc-cal">
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
