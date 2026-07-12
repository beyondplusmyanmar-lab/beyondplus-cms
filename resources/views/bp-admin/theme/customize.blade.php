@extends('bp-admin.layouts.admin.index')

@section('title', ($meta['name'] ?? $slug).' — Customize')

@section('content')
<style>
    .bz-group-title { font-size: .8rem; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin: 1.5rem 0 .75rem; padding-bottom: .35rem; border-bottom: 1px solid #eef0f3; }
    .bz-group-title:first-of-type { margin-top: 0; }
    .bz-color-wrap { display: flex; align-items: center; gap: .5rem; }
    .bz-color-wrap input[type=color] { width: 46px; height: 38px; padding: 2px; border: 1px solid #d1d5db; border-radius: 6px; }
    .bz-img-preview { max-height: 60px; border-radius: 6px; margin-top: 6px; display: block; }
</style>
<div class="row">
    <div class="col-md-9 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-sliders"></i> {{ $meta['name'] ?? $slug }} — Customize</h4>
                <small class="text-muted">Edit your site content here — no code needed. Empty sections hide automatically on the site.</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">
                @component('bp-admin.inc.alert')@endcomponent

                <form action="{{ url('bp-admin/themes/customize') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="theme" value="{{ $slug }}">

                    @php $lastGroup = null; @endphp
                    @foreach($schema as $field)
                        @php $name = $field['name'] ?? null; @endphp
                        @continue(! $name)
                        @php $type = $field['type'] ?? 'text'; $val = $values[$name] ?? ''; @endphp

                        @if(($field['group'] ?? null) !== $lastGroup)
                            <div class="bz-group-title">{{ $field['group'] ?? 'General' }}</div>
                            @php $lastGroup = $field['group'] ?? null; @endphp
                        @endif

                        <div class="form-group">
                            @if($type !== 'checkbox')
                                <label class="control-label">{{ $field['label'] ?? $name }}</label>
                            @endif

                            @if($type === 'repeater')
                                <div class="bz-repeater" data-name="{{ $name }}">
                                    <div class="bz-rep-rows">
                                        @foreach(($val ?: []) as $i => $row)
                                            @include('bp-admin.theme._repeater_row', ['name' => $name, 'fields' => $field['fields'] ?? [], 'index' => $i, 'row' => (array) $row])
                                        @endforeach
                                    </div>
                                    <template class="bz-rep-tpl">
                                        @include('bp-admin.theme._repeater_row', ['name' => $name, 'fields' => $field['fields'] ?? [], 'index' => '__INDEX__', 'row' => []])
                                    </template>
                                    <button type="button" class="btn btn-sm btn-outline-primary bz-rep-add"><i class="fa fa-plus"></i> {{ $field['add_label'] ?? 'Add item' }}</button>
                                </div>
                            @elseif($type === 'textarea')
                                <textarea class="form-control" name="{{ $name }}" rows="3" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $val }}</textarea>
                            @elseif($type === 'select')
                                <select class="form-control" name="{{ $name }}">
                                    @foreach(($field['options'] ?? []) as $ov => $ol)
                                        <option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>
                                    @endforeach
                                </select>
                            @elseif($type === 'checkbox')
                                <div class="form-check">
                                    <input type="hidden" name="{{ $name }}" value="no">
                                    <input class="form-check-input" type="checkbox" id="f_{{ $name }}" name="{{ $name }}" value="yes" {{ $val === 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="f_{{ $name }}">{{ $field['label'] ?? $name }}</label>
                                </div>
                            @elseif($type === 'color')
                                <div class="bz-color-wrap">
                                    {{-- The color input carries the name (always submits a valid hex);
                                         the text box mirrors it and updates it when a full hex is typed. --}}
                                    <input type="color" name="{{ $name }}" value="{{ $val ?: '#000000' }}" oninput="this.nextElementSibling.value=this.value">
                                    <input type="text" class="form-control" style="max-width:140px;" value="{{ $val }}" placeholder="#2563eb"
                                           oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)){this.previousElementSibling.value=this.value;}">
                                </div>
                            @elseif($type === 'image')
                                <input type="text" class="form-control" name="{{ $name }}" value="{{ $val }}" placeholder="{{ $field['placeholder'] ?? 'uploads path or full URL' }}">
                                @if($val)<img src="{{ \Illuminate\Support\Str::startsWith($val, ['http','/']) ? $val : bp_upload_url($val) }}" class="bz-img-preview" alt="preview">@endif
                            @else
                                <input type="text" class="form-control" name="{{ $name }}" value="{{ $val }}" placeholder="{{ $field['placeholder'] ?? '' }}">
                            @endif

                            @if(!empty($field['help']))
                                <small class="form-text text-muted">{{ $field['help'] }}</small>
                            @endif
                        </div>
                    @endforeach

                    <hr>
                    <a href="{{ url('bp-admin/themes') }}" class="btn btn-sm btn-outline-secondary">Back to themes</a>
                    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info"><i class="fa fa-external-link"></i> View site</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save content</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
    $(function () {
        // Repeater: add / remove rows. New rows get a unique index so their
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
