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
                            <form id="filterForm" class="datatables_parameters">
                                <div class="row row-sm">
                                    <div class="col-md-3 mb-2">
                                        <label for="name">@lang('app.name')</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="@lang('app.name')">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="min_price">@lang('app.min_price')</label>
                                        <input type="number" name="min_price" id="min_price" class="form-control" placeholder="@lang('app.min_price')">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="max_price">@lang('app.max_price')</label>
                                        <input type="number" name="max_price" id="max_price" class="form-control" placeholder="@lang('app.max_price')">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="created_at">@lang('app.created_at')</label>
                                        <input type="date" name="created_at" id="created_at" class="form-control" placeholder="@lang('app.created_at')">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search pe-2"></i>@lang('app.search')
                                            </button>
                                            <button type="button" id="resetFilters" class="btn btn-secondary">
                                                <i class="fa fa-refresh pe-2"></i>@lang('app.reset')
                                            </button>
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
