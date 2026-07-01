
<!-- START HEADER -->
<header class="header_wrap  header_with_topbar">
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                </div>
                <div class="col-md-8 text-center">
                  <div class="text-center">
                        
                        <h2><img class="logo_dark1" src="{{ url('assets/bptheme1/logo.jpg') }}" alt="logo" 
                          height="50px" /> Department of Research and Innovation</h2>
                    </div>
                </div>
                <div class="col-md-2">
                    @if (Auth::guard("customer_web")->check())

                        @php 
                            $first_name = Auth::guard("customer_web")->user()->first_name;
                        @endphp
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="lng_dropdown text-right">
                                    <select name="language" class="custome_select" id="profile_select">
                                        <option value='{{ $first_name }}'  data-title="{{ $first_name }}" data-link="{{ url('customer/profile') }}"><a herf="{{ url('customer/profile') }}">{{ $first_name }}</a></option>
                                         <option value='dashboard' data-title="Dashboard" data-link="{{ url('customer/profile') }}"><a herf="{{ url('customer/profile') }}">Profile</a></option>
                                        <option value='logout'  data-title="Logout" data-link="{{ url('/customer/logout') }}"><a herf="{{ url('/customer/logout') }}">Logout</a></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    @else
                         <a href="{{ url('/customer/sign-in') }}" class="nav-link"><i class="linearicons-user"></i> Login</a>
                    @endif
                  <div class="text-center text-md-right">
                        <ul class="header_list">
                            <li><a href="compare.html"><i class="ti-control-shuffle"></i><span>Compare</span></a></li>
                            <li><a href="wishlist.html"><i class="ti-heart"></i><span>Wishlist</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom_header dark_skin main_menu_uppercase">
      <div class="container">
            <nav class="navbar navbar-expand-lg"> 
                <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    Beyond Plus CMS
                    <img class="logo_light" src="assets/images/logo_light.png" alt="logo" />
                    <img class="logo_dark" src="assets/images/logo_dark.png" alt="logo" />
                </a> -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-expanded="false"> 
                    <span class="ion-android-menu"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav">

                        <li><a class="nav-link nav_item" href="{{ url('/') }}">Home</a></li>

                        @foreach (bp_menu() as $menu)

            
                         @if(sizeof($menu->children)>0) 
                            @php $children = $menu->children @endphp
                            @if(App::getLocale() == 'mm')
                              @if(isset($menu->translate))
                                @if($menu->translate->lang == 2)
                                  @php $menu = $menu->translate; @endphp
                                @endif
                              @endif
                            @endif
                       
                        <li class="dropdown">
                            <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">{{ $menu->menu_name }}</a>
                            <div class="dropdown-menu dropdown-reverse">
                                <ul>
                                    @foreach ($children as $sub)
                                      @if(App::getLocale() == 'mm')
                                        @if(isset($sub->translate))
                                          @if($sub->translate->lang == 2)
                                            @php $sub = $sub->translate; @endphp
                                          @endif
                                        @endif
                                      @endif
                                      <li>
                                          @if($sub->menu_type == 'default')
                                            <a href = "{{url('/'.$sub->menu_link) }}" class="dropdown-item">
                                          @else
                                            <a href = "{{$sub->menu_link}}" class="dropdown-item">
                                          @endif
                                          {{ $sub->menu_name }}</a>
                                          <!-- <a class="dropdown-item menu-link dropdown-toggler" href="#">Grids</a> -->

                                          @if(sizeof($sub->children)>0) 
                                            @php $children = $sub->children @endphp
                                            @if(App::getLocale() == 'mm')
                                              @if(isset($sub->translate))
                                                @if($sub->translate->lang == 2)
                                                  @php $sub = $sub->translate; @endphp
                                                @endif
                                              @endif
                                            @endif
                                            <div class="dropdown-menu">
                                                <ul> 
                                                  <li>

                                                    <!-- <a class="dropdown-item nav-link nav_item" href="blog-three-columns.html">3 columns</a> -->

                                                    @if($sub->menu_type == 'default')
                                                      <a href = "{{url('/'.$sub->menu_link) }}" class="dropdown-item">
                                                    @else
                                                      <a href = "{{$sub->menu_link}}" class="dropdown-item">
                                                    @endif
                                                    {{ $sub->menu_name }}</a>

                                                  </li>
                                                  
                                                </ul>
                                            </div>
                                          @endif
                                      </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                        @else
                          <li  class="dropdown">
                                @if($menu->menu_type == 'default')
                                    <a href = "{{url('/'.$menu->menu_link) }}"  class="nav-link" href="#" data-toggle="dropdown">
                                @else
                                    <a href = "{{$menu->menu_link}}">
                                @endif
                                  {{ $menu->menu_name }}</a>
                          </li>  
                        @endif
                      @endforeach
                         
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>
<!-- END HEADER -->