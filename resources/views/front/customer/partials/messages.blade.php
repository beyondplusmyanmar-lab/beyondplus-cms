@if(Session::has('msg'))
    <div class="alert alert-{{ Session::get('msg')['status'] === 'Success' ? 'success' : 'danger' }} alert-dismissible fade show">
        {{ Session::get('msg')['message'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(Session::has('flash_message'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ Session::get('flash_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(Session::has('flash_danger'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ Session::get('flash_danger') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
