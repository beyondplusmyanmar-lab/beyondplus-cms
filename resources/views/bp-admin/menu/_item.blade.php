{{-- Recursive menu-tree item. Keeps the drag-to-reorder attributes: child
     .menu-block elements sit directly inside the parent .menu-block. --}}
<div draggable="true" ondragstart="drag(event)" id="{{ $m->menu_id }}" class="menu-block">
    <div class="menu-item-row">
        <span class="menu-grip" title="{{ $mm ?? false ? 'အစီအစဉ် ပြောင်းရန် ဆွဲပါ' : 'Drag to reorder' }}"><i class="fa fa-bars"></i></span>
        <span class="menu-title">{{ $m->menu_name }}</span>
        @if($m->menu_link && $m->menu_link !== '#')
            <code class="menu-link">/{{ ltrim($m->menu_link, '/') }}</code>
        @endif
        <span class="badge badge-{{ ($m->menu_type ?? 'custom') === 'default' ? 'info' : 'secondary' }} menu-type">
            {{ ($m->menu_type ?? 'custom') === 'default' ? (($mm ?? false) ? 'ချိတ်ဆက်' : 'link') : (($mm ?? false) ? 'စိတ်ကြိုက်' : 'custom') }}
        </span>
        <span class="menu-item-actions ml-auto">
            <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="lang-link" title="{{ ($mm ?? false) ? 'ပြင်ရန်' : 'Edit' }} ({{ langauge($m->lang) }})">{{ langauge($m->lang) }}</a>
            @isset($m->translate)
                <a href="{{ url('bp-admin/menu/'.$m->translate->menu_id.'/edit') }}" class="lang-link" title="{{ ($mm ?? false) ? 'ပြင်ရန်' : 'Edit' }} ({{ langauge($m->translate->lang) }})">{{ langauge($m->translate->lang) }}</a>
            @endisset
            <a href="{{ url('bp-admin/menu/'.$m->menu_id.'/edit') }}" class="btn btn-sm btn-info" title="{{ ($mm ?? false) ? 'ပြင်ရန်' : 'Edit' }}"><i class="fa fa-pencil"></i></a>
            <a href="{{ url('bp-admin/menu/delete', $m->menu_id) }}" class="btn btn-sm btn-danger" title="{{ ($mm ?? false) ? 'ဖျက်ရန်' : 'Delete' }}"
               onclick="return confirm('{{ ($mm ?? false) ? 'ဤ menu item ကို ဖျက်မှာလား။' : 'Delete this menu item?' }}')"><i class="fa fa-trash"></i></a>
        </span>
    </div>

    @if(isset($m->children) && count($m->children))
        @foreach($m->children as $child)
            @if($child->lang == 1)
                @include('bp-admin.menu._item', ['m' => $child])
            @endif
        @endforeach
    @endif
</div>
