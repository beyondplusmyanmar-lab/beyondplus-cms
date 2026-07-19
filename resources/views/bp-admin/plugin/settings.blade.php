@extends('bp-admin.layouts.admin.index')

@section('title', ($meta['name'] ?? $slug).' '.(app()->getLocale() === 'mm' ? 'ဆက်တင်' : 'settings'))

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-sliders"></i> {{ $meta['name'] ?? $slug }} — {{ $mm ? 'ဆက်တင်' : 'settings' }}</h4>
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

                            @if($type === 'repeater')
                                <div class="bz-repeater" data-name="{{ $field['name'] }}">
                                    <div class="bz-rep-rows">
                                        @foreach(($val ?: []) as $i => $row)
                                            @include('bp-admin.theme._repeater_row', ['name' => $field['name'], 'fields' => $field['fields'] ?? [], 'index' => $i, 'row' => (array) $row])
                                        @endforeach
                                    </div>
                                    <template class="bz-rep-tpl">
                                        @include('bp-admin.theme._repeater_row', ['name' => $field['name'], 'fields' => $field['fields'] ?? [], 'index' => '__INDEX__', 'row' => []])
                                    </template>
                                    <button type="button" class="btn btn-sm btn-outline-primary bz-rep-add"><i class="fa fa-plus"></i> {{ $field['add_label'] ?? 'Add item' }}</button>
                                </div>
                            @elseif($type === 'textarea')
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

                    <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-secondary">{{ $mm ? 'ပြန်ရန်' : 'Back' }}</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ $mm ? 'ဆက်တင် သိမ်းရန်' : 'Save settings' }}</button>
                </form>

                @if(!empty($meta['test']))
                    <hr>
                    <form action="{{ url('bp-admin/plugins/test') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="slug" value="{{ $slug }}">
                        <label class="control-label">{{ $meta['test']['label'] ?? ($mm ? 'စမ်းသပ် မက်ဆေ့ချ် ပို့ရန်' : 'Send a test message to') }}</label>
                        <div class="input-group" style="max-width:420px;">
                            <input type="text" class="form-control" name="test_to" placeholder="{{ $meta['test']['placeholder'] ?? '' }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-paper-plane"></i> {{ $mm ? 'စမ်းသပ် ပို့ရန်' : 'Send test' }}</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ $mm ? 'အထက်ရှိ သိမ်းထားသော ဆက်တင်များကို သုံးသည် (အရင် သိမ်းပါ)။' : 'Uses the saved settings above (save first).' }}</small>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
    $(function () {
        // Repeater rows: add / remove. New rows get a unique index so their
        // field names don't collide with existing ones.
        var seq = Date.now();
        $('.bz-repeater').on('click', '.bz-rep-add', function () {
            var box = $(this).closest('.bz-repeater');
            var html = box.find('.bz-rep-tpl').html().split('__INDEX__').join('n' + (seq++));
            box.find('.bz-rep-rows').append(html);
        });
        $('.bz-repeater').on('click', '.bz-rep-del', function () {
            $(this).closest('.bz-rep-row').remove();
        });
    });
</script>
@endpush
