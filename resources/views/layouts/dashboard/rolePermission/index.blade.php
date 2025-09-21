@extends('layouts.app')

@section('content')
{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app_roles_title'),
'first_list_item' => trans('app_roles'),
'last_list_item' => trans('app.all_roles'),
])
{{-- end breadcrumb --}}

<!--start filters section -->
@include('layouts.dashboard.rolePermission.components._filters')
<!--end filterd section -->

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-header">
                <div class="form-group mb-0 mt-3 justify-content-end">
                    <div>
                        <a class="btn btn-primary" href="{{ route('role-permissions.create') }}"><i
                                class="fe fe-plus me-2"></i>@lang('app.new')</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive export-table" style="overflow: auto">
                    {!! $dataTable->table(['class' => 'table-data table table-bordered text-nowrap border-bottom ']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->

<!-- End Row -->
@endsection

@push('scripts')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
