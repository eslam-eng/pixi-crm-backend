<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div>
                    <a aria-controls="collapseExample" class="btn ripple btn-light collapsed" data-bs-toggle="collapse"
                        href="#collapseExample" role="button" aria-expanded="false"><i
                            class="fa fa-filter pe-2"></i>@lang('app.filter')
                    </a>
                </div>
                <div>
                    <div class="collapse" id="collapseExample">
                        <div class="mt-4">
                            <form class="datatables_parameters">
                                <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
                                    <!--div-->
                                    <div class="row row-sm">
                                        <div class="col-md-3 mb-2">
                                            {{-- @livewire('company') --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit"
                                                class="show_all_values search_datatable btn btn-primary"><i
                                                    class="fa fa-search pe-2"></i>@lang('app.search')</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
