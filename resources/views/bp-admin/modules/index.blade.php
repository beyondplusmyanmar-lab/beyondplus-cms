@extends('bp-admin.layouts.admin.index')

@section('title', 'Modules')

@section('content')
<div class="row">
    <div class="col-md-12 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-9">

                    </div>
                    <div class="col-sm-3 pull-right">

                        <a href="{{ url('bp-admin/permission/reset') }}" class="btn btn-success  pull-right">
                            <i class="fa fa-user-plus"></i>
                            Reset<span id="ok"></span>
                        </a>
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
                            <th>Show/Hide</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; $temprole = []; @endphp
                        @foreach ($module as $m)

                            <tr>
                                <td>{{$i++}}</a></td>
                                <td>{{$m->module_name}}</a></td>
                                <td>{{Form::checkbox('section',$m->module_id,$m->section, ['class' => 'section' , 'id' => 'section-'.$m->module_id])}}</td>
                                <td>Edit</td>
                            </tr>

                        
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


        


      $('.section').change(function() {
            // alert( "Handler for  called." );


            var module_id = $(this).val();
            var option = Number(this.checked);
            var type = this.className;
            // loadDoc(module_id, option, this.className);


            $.post("/bp-admin/general/moduleupdate", {module_id: module_id,
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

    //     function loadDoc(module_id,option,type) {
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
    //       xhttp.send("module_id="+module_id+"&option="+option+"&type="+type);
    //       // xhttp.send();
    //   }


    //   $('.show,.create,.edit,.delete').on('click', function() {
    //     var module_id = $(this).val();
    //     var option = Number(this.checked);
    //     loadDoc(module_id, option, this.className);
    // });

  });


</script>
@endpush