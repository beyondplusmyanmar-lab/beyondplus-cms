
@extends('bp-admin.layouts.admin.index')

@section('title', 'Menu Management')

@section('content')
<div class="row">
    <div class="col-md-8 tile">
                <div class="box box-danger" style="min-height:500px">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-sm-5">
                                <h4>Show</h4>
                            </div>
                            <div class="col-sm-7 pull-right">
                            <a href="{{ url('bp-admin/menu/create') }}" class="btn btn-success  pull-right">
                                <i class="fa fa-file"></i>
                                Custom Menu
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body menu-body" ondrop="drop(event)" ondragover="allowDrop(event)" id="0">
                            @foreach($menu as $m)
                            <div draggable="true" ondragstart="drag(event)" id="{{$m->menu_id}}" class="menu-block"   >
                                {{$m->menu_name}} | 
                                    @isset($m->translate)
                                        <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> | <a href="{{ url('bp-admin/menu/'.$m->translate->menu_id).'/edit' }}" >{{ langauge($m->translate->lang) }}</a>
                                    @else
                                        <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> 
                                    @endisset | 
                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="btn  btn-sm btnEdit clickable btn-info">Edit</a>
                                    <a href="{{ url('bp-admin/menu/delete',$m->menu_id) }}" class="btn  btn-sm btnEdit clickable btn-danger">Delete</a>
                                    @if($m->children)
                                        @foreach($m->children as $m)
                                            @if($m->lang == 1)
                                            <div draggable="true" ondragstart="drag(event)" id="{{$m->menu_id}}" class="menu-block"   >
                                                {{$m->menu_name}} | 
                                                    @isset($m->translate)
                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> | <a href="{{ url('bp-admin/menu/'.$m->translate->menu_id).'/edit' }}" >{{ langauge($m->translate->lang) }}</a>
                                                    @else
                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> 
                                                    @endisset | 
                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="btn  btn-sm btnEdit clickable btn-info">Edit</a>
                                                    <a href="{{ url('bp-admin/menu/delete',$m->menu_id) }}" class="btn  btn-sm btnEdit clickable btn-danger">Delete</a>
                                                    
                                                    <!-- second step -->

                                                    @if($m->children)
                                                        @foreach($m->children as $m)
                                                            @if($m->lang == 1)
                                                            <div draggable="true" ondragstart="drag(event)" id="{{$m->menu_id}}" class="menu-block"   >
                                                                {{$m->menu_name}} | 
                                                                    @isset($m->translate)
                                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> | <a href="{{ url('bp-admin/menu/'.$m->translate->menu_id).'/edit' }}" >{{ langauge($m->translate->lang) }}</a>
                                                                    @else
                                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> 
                                                                    @endisset | 
                                                                    <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="btn  btn-sm btnEdit clickable btn-info">Edit</a>
                                                                    <a href="{{ url('bp-admin/menu/delete',$m->menu_id) }}" class="btn  btn-sm btnEdit clickable btn-danger">Delete</a>

                                                                    <!-- third step -->
                                                                    @foreach($m->children as $m)
                                                                        @if($m->lang == 1)
                                                                        <div draggable="true" ondragstart="drag(event)" id="{{$m->menu_id}}" class="menu-block"   >
                                                                            {{$m->menu_name}} | 
                                                                                @isset($m->translate)
                                                                                <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> | <a href="{{ url('bp-admin/menu/'.$m->translate->menu_id).'/edit' }}" >{{ langauge($m->translate->lang) }}</a>
                                                                                @else
                                                                                <a href="{{ url('bp-admin/menu/'.$m->menu_id).'/edit' }}" >{{langauge($m->lang)}}</a> 
                                                                                @endisset | 
                                                                                <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="btn  btn-sm btnEdit clickable btn-info">Edit</a>
                                                                                <a href="{{ url('bp-admin/menu/delete',$m->menu_id) }}" class="btn  btn-sm btnEdit clickable btn-danger">Delete</a>
                                                                               
                                                                        </div>
                                                                        @endif
                                                                    @endforeach
                                                                   
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                   
                                            </div>

                                            @endif

                                        @endforeach
                                    @endif
                            </div>
                            @endforeach
                </div>
                <!-- /.box-body -->
            </div>
       </div>
    <div class="col-md-4 tile"> 
                    <div class="row  mb-1">
                        <div class="col-md-9">
                            {{ Form::open([
                            'url' => 'bp-admin/menu/pagestore',
                            'method' => 'post',
                            'files' => 'true',
                            ]) }}
                            {{ Form::label('Pages') }} 
                        </div>
                        <div class="col-md-3">
                                <button type="submit" class="pull-right btn btn-default btn-xs ">Insert</button>
                        </div>

                    </div>
                    <div class="row " id="scrollbar1">
                            <div class="scrollbar col-md-12">
                                @foreach($pages as $page)
                                <div class="row">
                                    <div class="col-md-2">
                                        {{ Form::checkbox('pages[]' , $page->id ) }}
                                    </div>
                                    <div class="col-md-10">
                                        <label for="{{$page ->title}}">
                                            {{$page->title}}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                 {{ Form::close() }}
                            </div>
                    </div>

                    <div class="row mt-4 mb-1">
                        <div class="col-md-9">
                            {{ Form::open([
                                'url' => 'bp-admin/menu/poststore',
                                'method' => 'post',
                                'files' => 'true',
                                ]) }}
                            {{ Form::label('Posts') }}<br />
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="pull-right btn btn-default btn-xs ">Insert</button>
                        </div>
                    </div>

                        <div class="row " id="scrollbar2">
                            <div class="scrollbar col-md-12">
                                @foreach($posts as $post)
                                <div class="row">
                                    <div class="col-md-2">
                                        {{ Form::checkbox('posts[]' , $post->id ) }}
                                    </div>
                                    <div class="col-md-10">
                                        <label for="{{$post ->title}}">
                                            {{$post->title}}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                {{ Form::close() }}
                            </div>
                        </div>
            <!-- /.box -->
        </div>
    </div>
    @stop

    @push('scripts')
    <script>
    function positionChange(parent_id,menu_id,weight) {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // var statusArray = ['REJECT','PENDING','SUCCESS'];
          if(this.responseText == 1){
            if(data == 2) {
                alert('success');
              // document.getElementById("status-"+id).innerHTML = '<label class="label label-success">'+statusArray[data]+'</label>';  
            } else if(data == 0) {
                alert('fail');
              // document.getElementById("status-"+id).innerHTML = '<label class="label label-danger">'+statusArray[data]+'</label>';  
            } else {
              
            }
            
          }
        }
      };
      xhttp.open("POST", "{{url('/')}}/bp-admin/iapi/menu/data", true);
      xhttp.setRequestHeader('X-CSRF-TOKEN' , '{{ csrf_token() }}' );
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("parent_id="+parent_id+"&menu_id="+menu_id+"&weight="+weight);
    }

        $(document).ready(function () {
            var height1 = $('#scrollbar1').height();
            var height2 = $('#scrollbar2').height();
            
            if (height1 > 158) {
                $('#scrollbar1').addClass('overflow-y');
            } else {
                $('#scrollbar1').removeClass('overflow-y');
            }

            if (height2 > 158) {
                $('#scrollbar2').addClass('overflow-y');
            } else {
                $('#scrollbar2').removeClass('overflow-y');
            }

        });

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev) {
            ev.preventDefault();
            var data = ev.dataTransfer.getData("text");
            ev.target.appendChild(document.getElementById(data));
            
            let menu_id = ev.target.appendChild(document.getElementById(data)).id;
            let parent_id = ev.target.appendChild(document.getElementById(data)).parentNode['id'];

            var child = document.getElementById('0');
            var parent = child.parentNode;
            // The equivalent of parent.children.indexOf(child)
            var index = Array.prototype.indexOf.call(parent.children, child);

            var data = child.children;
             permittedValues = [];
             for (i = 0; i < data.length; i++){
                permittedValues[i] = data[i]["id"];
             }

            // console.log(ev.target.appendChild(document.getElementById(data)));
            // console.log(menu_id);
            // console.log(parent_id);
            // console.log(index+1);
            positionChange(parent_id,menu_id,permittedValues);

        }

        
    </script>
    @endpush