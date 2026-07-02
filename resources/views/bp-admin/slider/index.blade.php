@extends('bp-admin.layouts.admin.index')

@section('title', 'Slider')

@section('content')
<style>
    .slider-card { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; transition: box-shadow .15s ease, transform .15s ease; }
    .slider-card:hover { box-shadow: 0 .6rem 1.4rem rgba(0,0,0,.10); transform: translateY(-3px); }
    .slider-thumb { height: 170px; background: #f3f4f6; }
    .slider-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .slider-card .card-footer { display: flex; gap: .5rem; }
    .slider-empty { border: 1px dashed #d1d5db; border-radius: 8px; margin: 1.5rem .5rem; padding: 1.5rem; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="mb-0">Homepage sliders</h4>
                        <small class="text-muted">These appear in the carousel at the top of the homepage.</small>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ url('bp-admin/slider/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-plus"></i> New slide
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    @forelse ($slider as $c)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card slider-card h-100">
                                <div class="slider-thumb">
                                    <img src="{{ url('uploads/'.$c->slider_link) }}" alt="{{ $c->slider_name }}">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title mb-1">{{ $c->slider_name }}</h5>
                                        <span class="badge badge-secondary" title="Display order">#{{ $c->slider_weight }}</span>
                                    </div>
                                    @if($c->slider_description)
                                        <p class="card-text text-muted small mb-0">{{ \Illuminate\Support\Str::limit($c->slider_description, 90) }}</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ url('bp-admin/slider/'.$c->slider_id.'/edit') }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <a href="{{ url('bp-admin/slider/delete', [$c->slider_id]) }}" class="btn btn-sm btn-danger btn-delete"
                                       onclick="return confirm('Delete this slide?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="slider-empty text-center text-muted py-5">
                                <i class="fa fa-image fa-2x mb-2 d-block"></i>
                                No slides yet. Click <strong>New slide</strong> to add one.
                            </div>
                        </div>
                    @endforelse
                </div>
                @if(method_exists($slider, 'links'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination"> {{ $slider->links() }} </div>
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
