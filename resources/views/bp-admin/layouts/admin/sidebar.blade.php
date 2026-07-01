<div class="app-sidebar__user">
        <div>
          <p class="app-sidebar__user-name">Admin Dashboard</p>
         <p class="app-sidebar__user-designation">{{ Auth::guard("admins")->user()->name }}</p> 
        </div>
      </div>
      <!-- <ul class="app-menu">
        @foreach(slidebar() as $s)
            @if(Session::get('applocale') == "mm") 
                @php $s->module_name = $s->module_name_mm; @endphp
            @endif

            @if(count($s->child)>0)
                <li class="treeview">
                    <a class="app-menu__item"  href="{{ url("bp-admin/".$s->module_link)}}" data-toggle="treeview"><i class="app-menu__icon {{$s->module_icon}}"></i> <span class="app-menu__label">{{$s->module_name}}</span> <i class="fa fa-angle-right pull-right"></i></a>
                    <ul class="treeview-menu">

                        @if($s->module_link == 'post')
                            <li><a href="{{ url("bp-admin/".$s->module_link)}}" class="treeview-item">{{ $s->module_name }}</a></li>
                        @endif
                        @foreach($s->child as $c)
                            @if(Session::get('applocale') == "mm") 
                            @php $c->module_name = $c->module_name_mm; @endphp
                            @endif
                            <li><a href="{{ url("bp-admin/".$c->module_link)}}" class="treeview-item">{{ $c->module_name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @else
                <li><a class="app-menu__item" href="{{ url("bp-admin/".$s->module_link)}}"><i class=" app-menu__icon {{$s->module_icon}}"></i>  <span class="app-menu__label">{{$s->module_name}}</span></a></li>
            @endif
            
        @endforeach

        
      </ul> -->

      <ul class="app-menu">
        @foreach(slidebar() as $s)
            @if(Session::get('applocale') == "mm") 
                @php $s->module_name = $s->module_name_mm; @endphp
            @endif

            @if(Auth::guard("admins")->check())

                @if($role = Auth::guard("admins")->user()->role)


                    @foreach($s->access as $access)

                        @if($access->canshow == 1)
                          @if($access->usertype == $role  )
                              @if(count($s->child)>0)
                                <!-- check show -->
                                
                                  <li class="treeview">
                                      <a class="app-menu__item"  href="{{ url("dashboard/".$s->module_link)}}" data-toggle="treeview"><i class="app-menu__icon {{$s->module_icon}}"></i> <span class="app-menu__label">{{$s->module_name}} </span> <i class="fa fa-angle-right pull-right"></i></a>
                                      <ul class="treeview-menu">

                                          @if (Auth::guard("admins")->user()->role > 3) 
                                            @if($s->module_link == 'post')
                                                <li><a href="{{ url("bp-admin/".$s->module_link)}}" class="treeview-item">{{ $s->module_name }} </a></li>
                                            @endif
                                          @endif

                                          @foreach($s->child as $m1)

                                            <!-- child module access looping and filter with user type -->
                                              @foreach($m1->access as $m1access)
                                                  @if($role  == $m1access->usertype)

                                                    @if($m1access->canshow == 1)

                                                      @if(Session::get('applocale') == "mm") 
                                                        @php $m1->module_name = $m1->module_name_mm; @endphp
                                                      @endif

                                                        <li><a href="{{ url("bp-admin/".$m1->module_link)}}" class="treeview-item">{{ $m1->module_name }}</a></li>
                                                    @endif
                                                  @endif
                                              @endforeach

                                          @endforeach

                                            
                                      </ul>
                                  </li>
                                  
                              @else
                                <li><a class="app-menu__item" href="{{ url("bp-admin/".$s->module_link)}}"><i class=" app-menu__icon {{$s->module_icon}}"></i>  <span class="app-menu__label">{{$s->module_name}}</span></a></li>
                                  
                              @endif
                            @endif
                          @endif
                    @endforeach

                @endif

                    
                    
           
            @endif

        @endforeach

        <!-- <li><a class="app-menu__item" href="{{ url("dashboard/tracking-scan/create")}}"><i class=" app-menu__icon"></i>  <span class="app-menu__label">Scan In</span></a></li>

        <li><a class="app-menu__item" href="{{ url("dashboard/tracking-scan/create")}}"><i class=" app-menu__icon"></i>  <span class="app-menu__label">Full Pack Request</span></a></li>
        
        <li><a class="app-menu__item" href="{{ url("dashboard/tracking-scan/create")}}"><i class=" app-menu__icon"></i>  <span class="app-menu__label">Custom Pack Request</span></a></li> -->

        <!-- <li><a class="app-menu__item" href="{{ url("dashboard/tracking-scan")}}"><i class=" app-menu__icon"></i>  <span class="app-menu__label">Tracking Scan</span></a></li> -->
      </ul>