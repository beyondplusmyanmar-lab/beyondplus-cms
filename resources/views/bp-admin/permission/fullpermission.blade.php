@extends('bp-admin.layouts.admin.index')

@section('title', 'Permisson')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-9">

                    </div>
                    <div class="col-sm-3 pull-right">

                      <!--   <a href="{{ url('bp-admin/permission/reset') }}" class="btn btn-success  pull-right">
                            <i class="fa fa-user-plus"></i>
                            Reset<span id="ok"></span>
                        </a> -->
                        <!-- <a href="{{ url('bp-admin/module/create') }}" class="btn btn-success  pull-right">
                            <i class="fa fa-user-plus"></i>
                            New<span id="ok"></span>
                        </a> -->
                    </div>
                </div>
            </div>

            <!-- /.box-header -->
            <div class="box-body">
                <table  class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Module Name</th>
                            <th>Show</th>
                            <th>Create</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; $temprole = []; @endphp
                        @foreach ($module as $m)


                           @if(!in_array( $m->usertype ,$temprole))
                             <tr>
                                <td  colspan="6" class="bg-primary text-white">
                                    @php array_push($temprole, $m->usertype) @endphp
                                    <b> {{ ucfirst(array_search($m->usertype, \App\Models\Bp_usertype::pluck('id','role')->toArray())) }} </b>
                                </td>
                              </tr>
                            @endif

                            @if(count($m->module->child) > 0)
                                 <tr>
                                    <td class="bg-light text-dark">{{$i++}}</a></td>
                                    <td class="bg-light text-dark">{{$m->module->module_name}}</a></td>
                                    <td class="bg-light text-dark">{{Form::checkbox('canshow',$m->access_id,$m->canshow, ['class' => 'canshow' , 'id' => 'canshow-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('cancreate', $m->access_id, $m->cancreate, ['class' => 'cancreate' , 'id' => 'cancreate-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('canedit', $m->access_id, $m->canedit, ['class' => 'canedit' ,'id' => 'canedit-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('candelete', $m->access_id, $m->candelete, ['class' => 'candelete' ,'id' => 'candelete-'.$m->access_id])}}</td>
                                </tr>

                                @foreach($m->module->child as $m1)

                                    <!-- child module access looping and filter with user type -->
                                    @foreach($m1->access as $m1access)
                                        @if($m->usertype == $m1access->usertype)
                                        <tr>
                                            <td>- {{$i++}}</a></td>
                                            <td>{{$m1->module_name}}</a></td>
                                            <td>{{Form::checkbox('canshow',$m1access->access_id,$m1access->canshow, ['class' => 'canshow' , 'id' => 'canshow-'.$m1access->access_id])}}</td>
                                            <td>{{Form::checkbox('cancreate', $m1access->access_id, $m1access->cancreate, ['class' => 'cancreate' , 'id' => 'cancreate-'.$m1->access_id])}}</td>
                                            <td>{{Form::checkbox('canedit', $m1access->access_id, $m1access->canedit, ['class' => 'canedit' ,'id' => 'canedit-'.$m->access_id])}}</td>
                                            <td>{{Form::checkbox('candelete', $m1access->access_id, $m1access->candelete, ['class' => 'candelete' ,'id' => 'candelete-'.$m1access->access_id])}}</td>
                                        </tr>
                                        @endif
                                    @endforeach

                                @endforeach
                            @else

                                <tr>
                                    <td class="bg-light text-dark">{{$i++}}</a></td>
                                    <td class="bg-light text-dark">{{$m->module->module_name}}</a></td>
                                    <td class="bg-light text-dark">{{Form::checkbox('canshow',$m->access_id,$m->canshow, ['class' => 'canshow' , 'id' => 'canshow-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('cancreate', $m->access_id, $m->cancreate, ['class' => 'cancreate' , 'id' => 'cancreate-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('canedit', $m->access_id, $m->canedit, ['class' => 'canedit' ,'id' => 'canedit-'.$m->access_id])}}</td>
                                    <td class="bg-light text-dark">{{Form::checkbox('candelete', $m->access_id, $m->candelete, ['class' => 'candelete' ,'id' => 'candelete-'.$m->access_id])}}</td>
                                </tr>

                            @endif

                        
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12">
                     {{--!! dataPaginator($users, true) !!--}}
                 </div>
             </div>
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
            // alert( "Handler for  called." );


            var access_id = $(this).val();
            var option = Number(this.checked);
            var type = this.className;
            // loadDoc(access_id, option, this.className);


            $.post("/bp-admin/permissionupdate", {access_id: access_id,
                option: option,
                type: type}, function(result){
                if(result) {
                  // alert("Successfully submited");
                    // if(result.success == "1") {
                    //     alert("Successfully submited");
                    // } else {
                    //     alert("Failed");
                    // }
                } else {
                    alert("Please contact to administrator");
                }
                

            });

        });

    //     function loadDoc(access_id,option,type) {
    //         var xhttp = new XMLHttpRequest();
    //         xhttp.onreadystatechange = function() {
    //           if (this.readyState == 4 && this.status == 200) {
    //               if(option == 1) {
    //                 $(this.responseText).unchecked;
    //               } else {
    //                 $(this.responseText).checked;
    //               } 
    //           } 
    //       };
    //       xhttp.open("POST", "/bp-admin/permisson", true);
    //       // xhttp.open("GET", '/bp-admin/permission/update', true);
    //       xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    //       xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //       xhttp.setRequestHeader('Cache-Control', 'no-cache');
    //       xhttp.setRequestHeader('X-CSRF-TOKEN', document.querySelector('[name=csrf-token]').getAttribute('content'));
    //       xhttp.send("access_id="+access_id+"&option="+option+"&type="+type);
    //       // xhttp.send();
    //   }


    //   $('.show,.create,.edit,.delete').on('click', function() {
    //     var access_id = $(this).val();
    //     var option = Number(this.checked);
    //     loadDoc(access_id, option, this.className);
    // });

  });


</script>
@endpush