<!-- breadcrumb -->
<div class="d-flex align-items-center justify-content-around mx-4">
    <div class="breadcrumb-header flex-column">

        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">{{$first_list_item}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$last_list_item}}</li>
            </ol>
        </div>
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">{{$title}}</span>
        </div>
    </div>

    <div class="d-flex align-items-center">
        {{-- <a class="btn btn-primary" href="{{ route('industries.create') }}"><i
                class="fe fe-plus me-2"></i>@lang('app.new')</a> --}}
        <a class="btn ripple btn-info px-4 py-3 d-flex align-items-center gap-2 text-nowrap"
            data-bs-target="#modaldemo3" data-bs-toggle="modal" href=""><i class="fa fa-plus"></i>Add Agencey</a>

    </div>
</div>

<!-- /breadcrumb -->