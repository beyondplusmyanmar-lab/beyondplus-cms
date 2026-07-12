@extends('bp-admin.layouts.admin.index')

@section('title', app()->getLocale() === 'mm' ? 'ပလပ်အင် လုံခြုံရေး စစ်ဆေးမှု' : 'Plugin security scan')

@section('content')
@php $mm = app()->getLocale() === 'mm'; @endphp
<div class="row">
    <div class="col-md-10 tile">
        <div class="box box-danger">
            <div class="box-header" style="padding-bottom:.75rem;">
                <h4 class="mb-0"><i class="fa fa-shield"></i> {{ $mm ? 'လုံခြုံရေး စစ်ဆေးမှု —' : 'Security scan —' }} {{ $meta['name'] ?? $slug }}</h4>
                <small class="text-muted">{{ $mm ? 'ပလပ်အင်၏ PHP ဖိုင်များကို static heuristic ဖြင့် စစ်ဆေးခြင်း။ sandbox မဟုတ်ဘဲ ကာကွယ်မှုသာ ဖြစ်၍ — ယုံကြည်ရသော ပလပ်အင်များကိုသာ install လုပ်ပါ။' : "Static heuristic scan of the plugin's PHP files. A safety net, not a sandbox — only install plugins you trust." }}</small>
            </div>
            <div class="box-body pt-3" style="border-top:1px solid #eef0f3;">

                @if(empty($scan['critical']) && empty($scan['warning']))
                    <div class="alert alert-success mb-0"><i class="fa fa-check"></i> {{ $mm ? 'အန္တရာယ်ရှိသော ပုံစံ မတွေ့ပါ။ ဤပလပ်အင် သန့်ရှင်းပုံ ရသည်။' : 'No risky patterns found. This plugin looks clean.' }}</div>
                @endif

                @if(!empty($scan['critical']))
                    <h6 class="text-danger"><i class="fa fa-ban"></i> {{ $mm ? 'ပြင်းထန် — activation ကို ပိတ်ပင်ထားသည်' : 'Critical — activation is blocked' }}</h6>
                    <table class="table table-sm mb-4">
                        <thead><tr><th style="width:40%">{{ $mm ? 'ဖိုင်' : 'File' }}</th><th>{{ $mm ? 'အကြောင်းရင်း' : 'Reason' }}</th></tr></thead>
                        <tbody>
                            @foreach($scan['critical'] as $f)
                                <tr class="table-danger"><td><code>{{ $f['file'] }}</code></td><td>{{ $f['reason'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if(!empty($scan['warning']))
                    <h6 class="text-warning"><i class="fa fa-exclamation-triangle"></i> {{ $mm ? 'သတိပေးချက် — ခွင့်ပြုသည်၊ ပြန်လည်စစ်ဆေးရန် အကြံပြု' : 'Warnings — allowed, review recommended' }}</h6>
                    <table class="table table-sm mb-4">
                        <thead><tr><th style="width:40%">{{ $mm ? 'ဖိုင်' : 'File' }}</th><th>{{ $mm ? 'အကြောင်းရင်း' : 'Reason' }}</th></tr></thead>
                        <tbody>
                            @foreach($scan['warning'] as $f)
                                <tr><td><code>{{ $f['file'] }}</code></td><td>{{ $f['reason'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <a href="{{ url('bp-admin/plugins') }}" class="btn btn-sm btn-outline-secondary">{{ $mm ? 'ပလပ်အင်များသို့ ပြန်ရန်' : 'Back to Plugins' }}</a>
                @if(empty($scan['critical']))
                    <form action="{{ url('bp-admin/plugins/activate') }}" method="post" class="d-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="slug" value="{{ $slug }}">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> {{ $mm ? 'ဖွင့်ရန်' : 'Activate' }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
