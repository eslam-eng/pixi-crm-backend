@extends('layouts.app')

@section('content')
{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.sources_title'),
'first_list_item' => trans('app.sources'),
'last_list_item' => trans('app.all_sources'),
])
{{-- end breadcrumb --}}

<!--start filters section -->
{{-- @include('layouts.dashboard.resource.components._filters') --}}
<!--end filterd section -->
@livewire('sources.create-source-modal')
@livewire('sources.search-source-modal')

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive export-table">
                    <div class="w-25">
                        <div class="input-group rounded-pill border overflow-hidden">
                            <span class="input-group-text bg-white border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <div id="search-here"></div>
                            <button class="btn btn-light border-0" data-bs-target="#searchModal" data-bs-toggle="modal">
                                <i class="fas fa-filter text-muted"></i>
                            </button>
                        </div>
                    </div>
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
<!-- JavaScript for Modal -->
<script>


    // Livewire listener to refresh the table
    Livewire.on('refreshDatatable', function(filters) {
        var table = $('#resources-table')
        table.on('preXhr.dt', function (e, settings, data) {
            data.filters = filters[0];
        });
        table.DataTable().ajax.reload(null, false);


        var modalElement = document.getElementById('searchModal');
        var modal = bootstrap.Modal.getInstance(modalElement); // Get the existing modal instance

        if (modal) {
            modal.hide(); // Hide the modal
        } else {
            console.error("Bootstrap modal instance not found! Trying to create a new instance...");
            modal = new bootstrap.Modal(modalElement);
            modal.hide();
        }

    });

</script>
@endpush
