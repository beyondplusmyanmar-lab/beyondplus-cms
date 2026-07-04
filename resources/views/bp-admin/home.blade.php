@extends('bp-admin.layouts.admin.index')
@section('title', 'Dashboard')
@section('content')

<style>
    .bp-stats { margin-bottom: 6px; }
    .stat-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px;
        display:flex; align-items:center; gap:16px; height:100%;
        box-shadow:0 1px 2px rgba(15,23,42,.04); transition:box-shadow .15s ease, transform .15s ease;
    }
    .stat-card:hover { box-shadow:0 .6rem 1.4rem rgba(15,23,42,.08); transform:translateY(-2px); }
    .stat-icon {
        width:52px; height:52px; flex:0 0 52px; border-radius:12px;
        display:flex; align-items:center; justify-content:center; font-size:1.3rem;
        color:rgb(var(--c)); background:rgba(var(--c),.12);
    }
    .stat-value { font-size:1.7rem; font-weight:700; color:#0f172a; line-height:1; }
    .stat-label { font-size:.72rem; color:#64748b; margin-top:5px; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }

    .dash-tile { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:22px; box-shadow:0 1px 2px rgba(15,23,42,.04); height:100%; }
    .dash-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
    .dash-head h5 { margin:0; font-weight:600; color:#0f172a; }
    .dash-item { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f1f5f9; text-decoration:none; }
    .dash-item:last-of-type { border-bottom:0; }
    .dash-item-title { color:#0f172a; font-weight:500; }
    .dash-item-sub { color:#94a3b8; font-size:.8rem; margin-top:2px; }
    .dash-time { color:#94a3b8; font-size:.76rem; white-space:nowrap; }
    .dash-avatar { width:38px; height:38px; flex:0 0 38px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#4338ca); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:.9rem; }
    .dash-empty { color:#94a3b8; font-size:.9rem; padding:22px 0; text-align:center; }
    .dash-chip { font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px; background:rgba(79,70,229,.1); color:#4f46e5; }
</style>

<div class="row bp-stats">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="--c:79,70,229;"><i class="fa fa-file-text-o"></i></div>
            <div>
                <div class="stat-value">{{ $totalPost }}</div>
                <div class="stat-label">Total Posts</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="--c:2,132,199;"><i class="fa fa-copy"></i></div>
            <div>
                <div class="stat-value">{{ $totalPage }}</div>
                <div class="stat-label">Pages</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="--c:217,119,6;"><i class="fa fa-users"></i></div>
            <div>
                <div class="stat-value">{{ $allUser }}</div>
                <div class="stat-label">Members</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="--c:5,150,105;"><i class="fa fa-picture-o"></i></div>
            <div>
                <div class="stat-value">{{ $totalMedia }}</div>
                <div class="stat-label">Media</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="dash-tile">
            <div class="dash-head">
                <h5><i class="fa fa-newspaper-o text-muted"></i> Recent Posts</h5>
                <a href="{{ url('bp-admin/post') }}" class="small">View all</a>
            </div>
            @forelse ($post as $p)
                <a href="{{ url('bp-admin/post/'.$p->id.'/edit') }}" class="dash-item">
                    <div>
                        <div class="dash-item-title">{{ $p->title }}</div>
                        <div class="dash-item-sub">{{ \Illuminate\Support\Str::limit(trim(strip_tags(str_replace('&nbsp;', ' ', $p->body ?? ''))), 90) }}</div>
                    </div>
                    <span class="dash-time">{{ $p->updated_at->diffForHumans() }}</span>
                </a>
            @empty
                <div class="dash-empty">No posts yet. <a href="{{ url('bp-admin/post/create') }}">Create one</a>.</div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="dash-tile">
            <div class="dash-head">
                <h5><i class="fa fa-user-plus text-muted"></i> Latest Members</h5>
                <span class="dash-chip">{{ $allUser }} total</span>
            </div>
            @forelse ($latestUsers as $u)
                <a href="{{ url('bp-admin/user/'.$u->id.'/edit') }}" class="dash-item">
                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span class="dash-avatar">{{ strtoupper(substr($u->first_name ?? 'U', 0, 1)) }}</span>
                        <span class="dash-item-title">{{ $u->first_name }}</span>
                    </div>
                    <span class="dash-time">{{ $u->created_at->diffForHumans() }}</span>
                </a>
            @empty
                <div class="dash-empty">No members yet.</div>
            @endforelse
            <div class="text-center mt-3"><a href="{{ url('bp-admin/user') }}" class="small">View all users</a></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="dash-tile">
            <div class="dash-head">
                <h5><i class="fa fa-history text-muted"></i> Recent activity</h5>
            </div>
            @forelse ($activities as $a)
                <div class="dash-item">
                    <div class="d-flex align-items-center" style="gap:12px;">
                        <span class="dash-avatar">{{ strtoupper(substr(optional($a->causer)->name ?? 'S', 0, 1)) }}</span>
                        <div>
                            <div class="dash-item-title">
                                <strong>{{ optional($a->causer)->name ?? optional($a->causer)->email ?? 'System' }}</strong> {{ $a->description }}
                            </div>
                            <div class="dash-item-sub text-uppercase" style="font-size:.68rem; letter-spacing:.3px;">{{ $a->log_name }}</div>
                        </div>
                    </div>
                    <span class="dash-time">{{ $a->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="dash-empty">No activity yet — actions taken in the admin panel will appear here.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>$(document).ready(function () {});</script>
@endpush
