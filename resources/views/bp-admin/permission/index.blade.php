@extends('bp-admin.layouts.admin.index')

@section('title', 'Permissions')

@section('content')
<style>
    .perm-table td { vertical-align: middle; }
    .perm-role-row td {
        background: #0f766e; color: #fff; font-weight: 600;
        letter-spacing: .3px; text-transform: capitalize;
    }
    .perm-name { font-weight: 500; color: #111827; }
    .perm-child .perm-name { padding-left: 2rem; color: #4b5563; font-weight: 400; }
    .perm-child .perm-name::before { content: '\21B3'; margin-right: .5rem; color: #9ca3af; }
    .perm-check { text-align: center; width: 110px; }
</style>
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <h4 class="mb-0">Access permissions</h4>
                <small class="text-muted">Tick a module to grant that role access to it. Changes save automatically.</small>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-hover perm-table">
                    <thead>
                        <tr>
                            <th style="width:70px">#</th>
                            <th>Module</th>
                            <th class="perm-check">Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; $temprole = []; @endphp
                        @foreach ($module as $m)

                            @if(!in_array($m->usertype, $temprole))
                                <tr class="perm-role-row">
                                    @php array_push($temprole, $m->usertype) @endphp
                                    <td colspan="3">{{ array_search($m->usertype, \App\Models\Bp_usertype::pluck('id', 'role')->toArray()) }}</td>
                                </tr>
                            @endif

                            @if(count($m->module->child) > 0)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td class="perm-name">{{ $m->module->module_name }}</td>
                                    <td class="perm-check">{{ Form::checkbox('canshow', $m->access_id, $m->canshow, ['class' => 'canshow', 'id' => 'canshow-'.$m->access_id]) }}</td>
                                </tr>

                                @foreach($m->module->child as $m1)
                                    @foreach($m1->access as $m1access)
                                        @if($m->usertype == $m1access->usertype)
                                            <tr class="perm-child">
                                                <td>{{ $i++ }}</td>
                                                <td class="perm-name">{{ $m1->module_name }}</td>
                                                <td class="perm-check">{{ Form::checkbox('canshow', $m1access->access_id, $m1access->canshow, ['class' => 'canshow', 'id' => 'canshow-'.$m1access->access_id]) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td class="perm-name">{{ $m->module->module_name }}</td>
                                    <td class="perm-check">{{ Form::checkbox('canshow', $m->access_id, $m->canshow, ['class' => 'canshow', 'id' => 'canshow-'.$m->access_id]) }}</td>
                                </tr>
                            @endif

                        @endforeach
                    </tbody>
                </table>
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.canshow,.cancreate,.canedit,.candelete').change(function() {
            var access_id = $(this).val();
            var option = Number(this.checked);
            var type = this.className;

            $.post("/bp-admin/permissionupdate", {
                access_id: access_id,
                option: option,
                type: type
            }, function(result){
                if(!result) {
                    alert("Please contact to administrator");
                }
            });
        });

    });
</script>
@endpush
