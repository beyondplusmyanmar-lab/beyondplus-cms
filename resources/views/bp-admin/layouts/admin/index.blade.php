<!DOCTYPE html>
<html lang="en">
  <head>
    
   {{--  <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:site" content="@pratikborsadiya">
    <meta property="twitter:creator" content="@pratikborsadiya">
    <!-- Open Graph Meta-->

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Vali Admin">
    <meta property="og:title" content="Vali - Free Bootstrap 4 admin theme">
    <meta property="og:url" content="http://pratikborsadiya.in/blog/vali-admin">
    <meta property="og:image" content="http://pratikborsadiya.in/blog/vali-admin/hero-social.png">
    <meta property="og:description" content="Vali is a responsive and free admin theme built with Bootstrap 4, SASS and PUG.js. It's fully customizable and modular."> --}}
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @if (trim($__env->yieldContent('title'))) @yield('title') - Beyond Plus Dashboard @endif Beyond Plus Dashboard </title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset("bower_components/vali-admin/docs/css/main.css") }}">
    <!-- Bootstrap 5 (loaded after the Vali/BS4 base so its components win; the theme layer below wins last) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Fonts + Beyond Plus admin theme (overrides the base) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Myanmar:wght@400;500;600&display=swap">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bp-admin-theme.css') }}?v=5">
    <script src="{{ asset ("/bower_components/bptheme/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
  </head>
  <body class="app sidebar-mini">
    <!-- Navbar-->
    @include('bp-admin.layouts.admin.header')
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        @include('bp-admin.layouts.admin.sidebar')
    </aside>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-dashboard"></i> @yield('title')</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="#">@yield('title')</a></li>
        </ul>
      </div>
      <div class="container-fluid">
            @yield('content')
      </div>
            {{-- <div class="tile-body">Create a beautiful dashboard</div> --}}

    </main>
    <!-- Essential javascripts for application to work-->
    <script src="{{ asset("bower_components/vali-admin/docs/js/jquery-3.2.1.min.js") }}"></script>
    <!-- Bootstrap 5 bundle (Popper included) replaces the BS4 popper + bootstrap.min.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bridge legacy jQuery Bootstrap-plugin calls (tooltip/popover/modal/…) to Bootstrap 5,
         so kept jQuery scripts (incl. Vali main.js) keep working under BS5. -->
    <script>
    (function ($) {
        if (!$ || !window.bootstrap) return;
        ['Tooltip','Popover','Modal','Dropdown','Collapse','Tab','Toast','Offcanvas'].forEach(function (name) {
            var key = name.charAt(0).toLowerCase() + name.slice(1);
            if ($.fn[key]) return;
            $.fn[key] = function (opt) {
                return this.each(function () {
                    var inst = window.bootstrap[name].getOrCreateInstance(this, typeof opt === 'object' ? opt : {});
                    if (typeof opt === 'string' && typeof inst[opt] === 'function') inst[opt]();
                });
            };
        });
    })(window.jQuery);
    </script>
    <script src="{{ asset("bower_components/vali-admin/docs/js/main.js") }}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{ asset("bower_components/vali-admin/docs/js/plugins/pace.min.js") }}"></script>

    <script src="{{ asset("bower_components/vali-admin/docs/js/plugins/bootstrap-datepicker.min.js") }}"></script>

    @stack('scripts')
    <!-- Page specific javascripts-->
  </body>
</html>