@extends('bp-admin.layouts.admin.index')

@section('title', 'Events Calendar')

@section('content')
<style>
    .bp-cal { width:100%; border-collapse:collapse; table-layout:fixed; }
    .bp-cal th { padding:.5rem; text-align:center; font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; color:#6b7280; border-bottom:1px solid #e5e7eb; }
    .bp-cal td { border:1px solid #eef0f3; vertical-align:top; height:112px; padding:.35rem; overflow:hidden; }
    .bp-cal .daynum { font-size:.8rem; color:#374151; font-weight:600; }
    .bp-cal td.out { background:#fafbfc; }
    .bp-cal td.out .daynum { color:#c7ccd4; }
    .bp-cal td.today { background:#eef2ff; }
    .bp-cal td.today .daynum { display:inline-flex; align-items:center; justify-content:center; min-width:1.4rem; height:1.4rem; background:#4f46e5; color:#fff; border-radius:50%; }
    .bp-cal .ev { display:block; background:#4f46e5; color:#fff; font-size:.72rem; line-height:1.3; padding:2px 6px; border-radius:4px; margin-top:3px; text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .bp-cal .ev:hover { background:#4338ca; color:#fff; }
    .bp-cal .ev .t { opacity:.85; font-weight:600; margin-right:3px; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">
                    <div>
                        <h4 class="mb-0">{{ $cursor->format('F Y') }}</h4>
                        <small class="text-muted">Events by date &amp; time. Click an event to edit.</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap:.4rem;">
                        <a href="{{ url('bp-admin/news/calendar?month='.$cursor->copy()->subMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary" title="Previous month"><i class="fa fa-chevron-left"></i></a>
                        <a href="{{ url('bp-admin/news/calendar') }}" class="btn btn-sm btn-outline-secondary">Today</a>
                        <a href="{{ url('bp-admin/news/calendar?month='.$cursor->copy()->addMonth()->format('Y-m')) }}" class="btn btn-sm btn-outline-secondary" title="Next month"><i class="fa fa-chevron-right"></i></a>
                        <a href="{{ url('bp-admin/news') }}" class="btn btn-sm btn-primary"><i class="fa fa-list"></i> List</a>
                        <a href="{{ url('bp-admin/news/create') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> New</a>
                    </div>
                </div>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @php
                    $start  = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $end    = $cursor->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    $today  = \Carbon\Carbon::today();
                    $day    = $start->copy();
                @endphp
                <table class="bp-cal">
                    <thead>
                        <tr>
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<th>{{ $d }}</th>@endforeach
                        </tr>
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
                                            <a class="ev" href="{{ url('bp-admin/news/'.$ev->id.'/edit') }}" title="{{ $ev->title }} — {{ \Carbon\Carbon::parse($ev->event_at)->format('D, d M Y g:i A') }}">
                                                <span class="t">{{ \Carbon\Carbon::parse($ev->event_at)->format('g:i A') }}</span>{{ $ev->title }}
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
        </div>
    </div>
</div>
@stop
