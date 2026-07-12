{{-- One repeater row. Shared by existing rows (numeric $index) and the JS
     clone template ($index = "__INDEX__"). $name, $fields, $index, $row. --}}
@php $rowName = $name.'['.$index.']'; @endphp
<div class="bz-rep-row" style="border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 4px;margin-bottom:10px;background:#fff;">
    <div class="d-flex justify-content-end" style="margin-bottom:-4px;">
        <button type="button" class="btn btn-link btn-sm text-danger bz-rep-del p-0" style="font-size:.8rem;text-decoration:none;"><i class="fa fa-times-circle"></i> Remove</button>
    </div>
    <div class="row">
        @foreach($fields as $sf)
            @php
                $sv    = $row[$sf['name']] ?? ($sf['default'] ?? '');
                $stype = $sf['type'] ?? 'text';
                $col   = $sf['col'] ?? 6;
                $iname = $rowName.'['.$sf['name'].']';
            @endphp
            <div class="col-md-{{ $col }} form-group mb-2">
                <label class="control-label small text-muted mb-1">{{ $sf['label'] ?? $sf['name'] }}</label>
                @if($stype === 'textarea')
                    <textarea name="{{ $iname }}" class="form-control form-control-sm" rows="2" placeholder="{{ $sf['placeholder'] ?? '' }}">{{ $sv }}</textarea>
                @elseif($stype === 'select')
                    <select name="{{ $iname }}" class="form-control form-control-sm">
                        @foreach(($sf['options'] ?? []) as $ov => $ol)
                            <option value="{{ $ov }}" {{ (string) $sv === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="{{ $stype === 'number' ? 'number' : 'text' }}" name="{{ $iname }}" class="form-control form-control-sm" value="{{ $sv }}" placeholder="{{ $sf['placeholder'] ?? '' }}">
                @endif
            </div>
        @endforeach
    </div>
</div>
