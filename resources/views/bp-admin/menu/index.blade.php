
@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'Menu စီမံခန့်ခွဲမှု' : 'Menu Management')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<style>
    .menu-body { padding: 1rem 1.25rem 1.25rem; min-height: 460px; }
    .box-header { padding-bottom: .75rem; }
    .insert-list { padding: .5rem 1rem .75rem; }
    .box-header h5 { font-weight: 600; }
    .menu-item-row {
        display: flex; align-items: center; gap: .5rem;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 6px;
        padding: .5rem .75rem; margin-bottom: .5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,.04); transition: border-color .12s ease;
    }
    .menu-item-row:hover { border-color: #6366f1; }
    .menu-block .menu-block { margin-left: 1.25rem; padding-left: 1rem; border-left: 2px solid #e5e7eb; }
    .menu-grip { color: #9ca3af; cursor: grab; }
    .menu-title { font-weight: 600; color: #111827; }
    .menu-link { background: #f3f4f6; color: #6b7280; padding: 1px 6px; border-radius: 4px; font-size: .78rem; }
    .menu-type { font-size: .66rem; text-transform: uppercase; letter-spacing: .3px; }
    .menu-item-actions { display: flex; align-items: center; gap: .4rem; }
    .menu-item-actions .lang-link { font-size: .78rem; }
    .menu-item-actions .btn { padding: .15rem .45rem; line-height: 1.2; }
    .menu-empty { border: 1px dashed #d1d5db; border-radius: 8px; }
    /* .insert-list checkbox rows are styled by the shared checklist rules in bp-admin-theme.css */
</style>
<div class="row">
    <div class="col-md-8 tile">
        <div class="box box-danger" style="min-height:500px">
            <div class="box-header">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="mb-0">{{ $mm ? 'Menu ဖွဲ့စည်းပုံ' : 'Menu structure' }}</h4>
                        <small class="text-muted">{{ $mm ? 'အစီအစဉ် ပြောင်းရန် ဆွဲယူပါ။ အထဲတွင် ထည့်ထားသော item များသည် ဆိုက်တွင် dropdown အဖြစ် ပေါ်သည်။' : 'Drag items to reorder. Nested items appear as dropdowns on the site.' }}</small>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ url('bp-admin/menu/create') }}" class="btn btn-success pull-right">
                            <i class="fa fa-plus"></i> {{ $mm ? 'စိတ်ကြိုက် link' : 'Custom link' }}
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
                        {{ $mm ? 'menu item မရှိသေးပါ။ စိတ်ကြိုက် link ထည့်ပါ၊ သို့မဟုတ် ညာဘက်မှ စာမျက်နှာ/ပို့စ်များ ထည့်ပါ။' : 'No menu items yet. Add a custom link, or insert pages/posts from the right.' }}
                    </div>
                @endforelse
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="col-md-4 tile">
        <div class="box box-danger">
            {{ Form::open(['url' => 'bp-admin/menu/pagestore', 'method' => 'post']) }}
            <div class="box-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-file-o"></i> {{ $mm ? 'စာမျက်နှာများ' : 'Pages' }}</h5>
                <button type="submit" class="btn btn-primary btn-sm">{{ $mm ? 'ထည့်ရန်' : 'Insert' }}</button>
            </div>
            <div class="box-body insert-list" id="scrollbar1">
                <div class="scrollbar">
                    @forelse($pages as $page)
                        <div class="form-check">
                            {{ Form::checkbox('pages[]', $page->id, false, ['class' => 'form-check-input', 'id' => 'page-'.$page->id]) }}
                            <label class="form-check-label" for="page-{{ $page->id }}">{{ $page->title }}</label>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">{{ $mm ? 'စာမျက်နှာ မရှိသေးပါ။' : 'No pages yet.' }}</p>
                    @endforelse
                </div>
            </div>
            {{ Form::close() }}
        </div>

        <div class="box box-danger">
            {{ Form::open(['url' => 'bp-admin/menu/poststore', 'method' => 'post']) }}
            <div class="box-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-newspaper-o"></i> {{ $mm ? 'ပို့စ်များ' : 'Posts' }}</h5>
                <button type="submit" class="btn btn-primary btn-sm">{{ $mm ? 'ထည့်ရန်' : 'Insert' }}</button>
            </div>
            <div class="box-body insert-list" id="scrollbar2">
                <div class="scrollbar">
                    @forelse($posts as $post)
                        <div class="form-check">
                            {{ Form::checkbox('posts[]', $post->id, false, ['class' => 'form-check-input', 'id' => 'post-'.$post->id]) }}
                            <label class="form-check-label" for="post-{{ $post->id }}">{{ $post->title }}</label>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">{{ $mm ? 'ပို့စ် မရှိသေးပါ။' : 'No posts yet.' }}</p>
                    @endforelse
                </div>
            </div>
            {{ Form::close() }}
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
