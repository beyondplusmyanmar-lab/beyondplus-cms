@extends('bp-admin.layouts.admin.index')

@section('title', ($meta['name'] ?? $slug).' settings')

@section('content')
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-sliders"></i> {{ $meta['name'] ?? $slug }} — settings</h4>
                <small class="text-muted">{{ $meta['description'] ?? '' }}</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <form action="{{ url('bp-admin/plugins/settings') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="slug" value="{{ $slug }}">

                    @foreach($schema as $field)
                        @php $val = $values[$field['name']] ?? ''; $type = $field['type'] ?? 'text'; @endphp
                        <div class="form-group">
                            <label class="control-label">{{ $field['label'] ?? $field['name'] }}</label>

                            @if($type === 'textarea')
                                <textarea class="form-control" name="{{ $field['name'] }}" rows="3" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $val }}</textarea>
                            @elseif($type === 'select')
                                <select class="form-control" name="{{ $field['name'] }}">
                                    @foreach(($field['options'] ?? []) as $ov => $ol)
                                        <option value="{{ $ov }}" {{ (string)$val === (string)$ov ? 'selected' : '' }}>{{ $ol }}</option>
                                    @endforeach
                                </select>
                            @elseif($type === 'checkbox')
                                <div class="form-check">
                                    <input type="hidden" name="{{ $field['name'] }}" value="no">
                                    <input class="form-check-input" type="checkbox" name="{{ $field['name'] }}" value="yes" {{ $val === 'yes' ? 'checked' : '' }}>
                                </div>
                            @else
                                <input type="{{ $type === 'password' ? 'password' : 'text' }}" class="form-control" name="{{ $field['name'] }}" value="{{ $val }}" placeholder="{{ $field['placeholder'] ?? '' }}" autocomplete="off">
                            @endif

                            @if(!empty($field['help']))
                                <small class="form-text text-muted">{{ $field['help'] }}</small>
                            @endif
                        </div>
                    @endforeach

                    <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save settings</button>
                </form>

                @if(!empty($meta['test']))
                    <hr>
                    <form action="{{ url('bp-admin/plugins/test') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="slug" value="{{ $slug }}">
                        <label class="control-label">{{ $meta['test']['label'] ?? 'Send a test message to' }}</label>
                        <div class="input-group" style="max-width:420px;">
                            <input type="text" class="form-control" name="test_to" placeholder="{{ $meta['test']['placeholder'] ?? '' }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-paper-plane"></i> Send test</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Uses the saved settings above (save first).</small>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
