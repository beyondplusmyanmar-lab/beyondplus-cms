
@extends('bp-admin.layouts.admin.index')

@section('title', 'Menu Management')

@section('content')
<style>
    .menu-body { padding: .5rem; min-height: 460px; }
    .menu-item-row {
        display: flex; align-items: center; gap: .5rem;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 6px;
        padding: .5rem .75rem; margin-bottom: .5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,.04); transition: border-color .12s ease;
    }
    .menu-item-row:hover { border-color: #14b8a6; }
    .menu-block .menu-block { margin-left: 1.25rem; padding-left: 1rem; border-left: 2px solid #e5e7eb; }
    .menu-grip { color: #9ca3af; cursor: grab; }
    .menu-title { font-weight: 600; color: #111827; }
    .menu-link { background: #f3f4f6; color: #6b7280; padding: 1px 6px; border-radius: 4px; font-size: .78rem; }
    .menu-type { font-size: .66rem; text-transform: uppercase; letter-spacing: .3px; }
    .menu-item-actions { display: flex; align-items: center; gap: .4rem; }
    .menu-item-actions .lang-link { font-size: .78rem; }
    .menu-item-actions .btn { padding: .15rem .45rem; line-height: 1.2; }
    .menu-empty { border: 1px dashed #d1d5db; border-radius: 8px; }
    .insert-list .form-check { padding: .3rem .25rem; border-bottom: 1px solid #f1f3f5; }
    .insert-list .form-check:hover { background: #f8fafc; }
</style>
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger" style="min-height:500px">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="mb-0">Menu structure</h4>
                        <small class="text-muted">Drag items to reorder. Nested items appear as dropdowns on the site.</small>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ url('bp-admin/menu/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-plus"></i> Custom link
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body menu-body" ondrop="drop(event)" ondragover="allowDrop(event)" id="0">
                @forelse($menu as $m)
                    @include('bp-admin.menu._item', ['m' => $m])
                @empty
                    <div class="menu-empty text-center text-muted py-5">
                        <i class="fa fa-bars fa-2x mb-2 d-block"></i>
                        No menu items yet. Add a custom link, or insert pages/posts from the right.
                    </div>
                @endforelse
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="col-md-4 tile">
        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        {{ Form::open(['url' => 'bp-admin/menu/pagestore', 'method' => 'post', 'files' => 'true']) }}
                        <h5 class="mb-0"><i class="fa fa-file-o"></i> Pages</h5>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="pull-right btn btn-primary btn-sm">Insert</button>
                    </div>
                </div>
            </div>
            <div class="box-body insert-list" id="scrollbar1">
                <div class="scrollbar col-md-12">
                    @forelse($pages as $page)
                        <div class="form-check">
                            {{ Form::checkbox('pages[]', $page->id, false, ['class' => 'form-check-input', 'id' => 'page-'.$page->id]) }}
                            <label class="form-check-label" for="page-{{ $page->id }}">{{ $page->title }}</label>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No pages yet.</p>
                    @endforelse
                </div>
                {{ Form::close() }}
            </div>
        </div>

        <div class="box box-danger">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        {{ Form::open(['url' => 'bp-admin/menu/poststore', 'method' => 'post', 'files' => 'true']) }}
                        <h5 class="mb-0"><i class="fa fa-newspaper-o"></i> Posts</h5>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="pull-right btn btn-primary btn-sm">Insert</button>
                    </div>
                </div>
            </div>
            <div class="box-body insert-list" id="scrollbar2">
                <div class="scrollbar col-md-12">
                    @forelse($posts as $post)
                        <div class="form-check">
                            {{ Form::checkbox('posts[]', $post->id, false, ['class' => 'form-check-input', 'id' => 'post-'.$post->id]) }}
                            <label class="form-check-label" for="post-{{ $post->id }}">{{ $post->title }}</label>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No posts yet.</p>
                    @endforelse
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
function positionChange(parent_id,menu_id,weight) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if(this.responseText == 1){
        if(data == 2) {
            alert('success');
        } else if(data == 0) {
            alert('fail');
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

        if (height1 > 158) { $('#scrollbar1').addClass('overflow-y'); } else { $('#scrollbar1').removeClass('overflow-y'); }
        if (height2 > 158) { $('#scrollbar2').addClass('overflow-y'); } else { $('#scrollbar2').removeClass('overflow-y'); }
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
        var index = Array.prototype.indexOf.call(parent.children, child);

        var data = child.children;
         permittedValues = [];
         for (i = 0; i < data.length; i++){
            permittedValues[i] = data[i]["id"];
         }

        positionChange(parent_id,menu_id,permittedValues);
    }
</script>
@endpush
