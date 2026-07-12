@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'မီဒီယာ' : 'Media')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .media-card { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; transition: box-shadow .15s ease, transform .15s ease; }
    .media-card:hover { box-shadow: 0 .6rem 1.4rem rgba(0,0,0,.10); transform: translateY(-3px); }
    .media-thumb { height: 170px; background: #f3f4f6; }
    .media-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .media-card .card-footer { display: flex; gap: .5rem; }
    .media-empty { border: 1px dashed #d1d5db; border-radius: 8px; margin: 1.5rem .5rem; padding: 1.5rem; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="mb-0">{{ $mm ? 'မီဒီယာ စာကြည့်တိုက်' : 'Media library' }}</h4>
                        <small class="text-muted">{{ $mm ? 'ပို့စ်၊ စာမျက်နှာ နှင့် slider များတွင် ပြန်သုံးနိုင်သော upload လုပ်ထားသည့် ပုံများ။' : 'Uploaded images you can reuse across posts, pages and sliders.' }}</small>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ url('bp-admin/media/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-upload"></i> {{ $mm ? 'Upload တင်ရန်' : 'Upload' }}
                        </a>
                    </div>
                </div>

                @if(Auth::guard("admins")->user()->role > 2)
                    <form action="{{ url('/bp-admin/media') }}" method="get">
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Search by name"
                                       autocomplete="off" value="{{ Request::get('name') }}" placeholder="{{ $mm ? 'အမည်ဖြင့် ရှာရန်' : 'Search by name' }}">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-info"><span class="fa fa-search"></span> {{ $mm ? 'ရှာရန်' : 'Search' }}</button>
                                <a href="{{ url('/bp-admin/media') }}" class="btn btn-primary" title="{{ $mm ? 'ပြန်စရန်' : 'Reset' }}"><span class="fa fa-refresh"></span></a>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    @forelse ($media as $c)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card media-card h-100">
                                <div class="media-thumb">
                                    <img src="{{ bp_upload_url($c->media_link) }}" alt="{{ $c->media_name }}">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-2">{{ $c->media_name }}</h5>
                                    <div class="input-group input-group-sm">
                                        <input type="text" value="{{ bp_upload_url($c->media_link) }}" class="form-control" readonly
                                               onclick="this.select()" title="{{ $mm ? 'ရွေးရန် နှိပ်ပြီး ကူးပါ' : 'Click to select, then copy' }}">
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ url('bp-admin/media/'.$c->media_id.'/edit') }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-pencil"></i> {{ $mm ? 'ပြင်ရန်' : 'Edit' }}
                                    </a>
                                    <a href="{{ url('bp-admin/media/delete', [$c->media_id]) }}" class="btn btn-sm btn-danger btn-delete"
                                       onclick="return confirm('{{ $mm ? 'ဤ ပုံကို ဖျက်မှာလား။' : 'Delete this image?' }}')">
                                        <i class="fa fa-trash"></i> {{ $mm ? 'ဖျက်ရန်' : 'Delete' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="media-empty text-center text-muted py-5">
                                <i class="fa fa-image fa-2x mb-2 d-block"></i>
                                {!! $mm ? 'မီဒီယာ မရှိသေးပါ။ ပုံ ထည့်ရန် <strong>Upload</strong> ကို နှိပ်ပါ။' : 'No media yet. Click <strong>Upload</strong> to add an image.' !!}
                            </div>
                        </div>
                    @endforelse
                </div>
                @if(method_exists($media, 'links'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $media->links() }} </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
@stop

@push('scripts')
<script>
    $(document).ready(function () {
    });
</script>
@endpush
