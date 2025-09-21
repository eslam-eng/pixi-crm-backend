<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div>
                    <a aria-controls="collapseExample" class="btn ripple btn-light collapsed" data-bs-toggle="collapse"
                        href="#collapseExample" role="button" aria-expanded="false">
                        <i class="fa fa-filter pe-2"></i>@lang('app.filter')
                    </a>
                </div>
                <div>
                    <div class="collapse" id="collapseExample">
                        <div class="mt-4">
                            <form class="datatables_parameters" method="GET" action="{{ route('clients.index') }}">
                                <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
                                    <div class="row row-sm">
                                        <div class="col-md-3 mb-2">
                                            <label for="name">@lang('app.name')</label>
                                            <input type="text" name="filters[name]" id="name" class="form-control" value="{{ request('filters.name') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit" class="show_all_values search_datatable btn btn-primary">
                                                <i class="fa fa-search pe-2"></i>@lang('app.search')
                                            </button>
                                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                                <i class="fa fa-refresh pe-2"></i>@lang('app.reset')
                                            </a>
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
