@extends('layouts.app')

@section('content')
    {{-- breadcrumb --}}
    @include('layouts.components.breadcrumb', [
        'title' => trans('app.show_client'),
        'first_list_item' => trans('app.client'),
        'last_list_item' => trans('app.show_client')
    ])
    {{-- end breadcrumb --}}

    <!-- Row -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <!-- Client Details Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">@lang('app.client_details')</h4>
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.name')</div>
                            <label class="form-control">{{ $client->name }}</label>
                        </div>
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.phone')</div>
                            <label class="form-control">{{ $client->phone }}</label>
                        </div>
                    </div>
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.email')</div>
                            <label class="form-control">{{ $client->email }}</label>
                        </div>
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.address')</div>
                            <label class="form-control">{{ $client->address }}</label>
                        </div>
                    </div>

                    <!-- Resource -->
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.resource')</div>
                            <label class="form-control">{{ $client->resource->name ?? '-' }}</label>
                        </div>
                    </div>

                    <!-- Industries -->
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.industries')</div>
                            <ul class="list-group">
                                @foreach ($client->industries as $industry)
                                    <li class="list-group-item">{{ $industry->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.services')</div>
                            <ul class="list-group">
                                @foreach ($client->services as $service)
                                    <li class="list-group-item">
                                        {{ $service->name }}
                                        <span class="badge badge-primary">
                                            {{ $service->pivot->category_id ? 'Category: ' . $service->pivot->category_id : 'No Category' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Custom Fields -->
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.custom_fields')</div>
                            <ul class="list-group">
                                @foreach ($client->customFields as $field)
                                    <li class="list-group-item">
                                        {{ $field->name }}: <strong>{{ $field->pivot->value }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
