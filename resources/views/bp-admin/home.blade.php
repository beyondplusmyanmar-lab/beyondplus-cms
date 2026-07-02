@extends('bp-admin.layouts.admin.index')
@section('title', 'Dashboard')
@section('content')

  <div class="row">
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4>Total Post</h4>
              <p><b>{{$totalPost}}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-file-text-o fa-3x"></i>
            <div class="info">
              <h4>Pages</h4>
              <p><b>{{ $totalPage }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
            <div class="info">
              <h4>User Registrations</h4>
              <p><b>{{$allUser}}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon"><i class="icon fa fa-picture-o fa-3x"></i>
            <div class="info">
              <h4>Media</h4>
              <p><b>{{ $totalMedia }}</b></p>
            </div>
          </div>
        </div>
      </div>
<div class='row tile '>

    <!-- Box -->
    <!-- PRODUCT LIST -->
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Recent Post</h3>
            <table class="table">
              <tbody>
                @foreach ($post as $p)
                <tr>
                  <td>
                    <a href="{{ url('bp-admin/post/'.$p->id.'/edit') }}" class="product-title">{{$p->title}}
                      <span class="label label-warning pull-right">{{ $p->updated_at->diffForHumans() }} </span></a>
                      <span class="product-description">
                        {{ str_replace("&nbsp;","",substr(strip_tags($p->body), 0, 1000)) }}...
                      </span>
                      </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
             <div class="box-footer text-center">
                <a href="{{ url("bp-admin/post")}}" class="uppercase">View All Posts</a>
              </div>
            </div>
        </div>

        <div class="col-md-6">
          <div class="tile">
            <div class="row">
              <div class="col-md-6">
                <h3 class="tile-title">Latest Members</h3>
              </div>
              <div class="col-md-6 ">
                <span class="label label-danger pull-right"> {{ $allUser }} New Members </span>
              </div>
            </div>
            <div class="row">
              
                  @foreach($latestUsers as $latestUser )
                  <div class="col-md-3">
                      <img src="{{ url('/img/avatar2.png')}}" alt="User Image" style="height:50px">
                      <br><a class="users-list-name" href="{{ url('bp-admin/user/'.$latestUser->id.'/edit') }}">  {{ $latestUser->first_name }} </a>
                      <br>
                      <span class="users-list-date">{{ $latestUser->created_at->diffForHumans() }}</span>
                  </div>
                  @endforeach
              
            </div>
             <div class="box-footer text-center">
                 <a href="{{ url("bp-admin/user")}}" class="uppercase">View All Users</a>
              </div>
            </div>
        </div>


          @endsection

          @push('scripts')
          <script>
            $(document).ready(function () {
            });
          </script>
          @endpush