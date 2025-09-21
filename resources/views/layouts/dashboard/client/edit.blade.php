@extends('layouts.app')

@section('styles')
    <!--Internal Sumoselect css-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sumoselect/sumoselect.css') }}">
@endsection

@section('content')

{{-- Breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.edit_client_title'),
    'first_list_item' => trans('app.client'),
    'last_list_item' => trans('app.edit_client'),
])
{{-- End Breadcrumb --}}

<!-- Row -->
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
        <!-- Div -->
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('clients.update', $client->id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="row row-sm mb-4">
                        <!-- Name Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('name', $client->name) }}"
                                    name="name"
                                    placeholder="@lang('app.name')"
                                    type="text"
                                >
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4">
                        <!-- Phone Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.phone') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('phone', $client->phone) }}"
                                    name="phone"
                                    placeholder="@lang('app.phone')"
                                    type="text"
                                >
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4">
                        <!-- Email Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.email') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('email', $client->email) }}"
                                    name="email"
                                    placeholder="@lang('app.email')"
                                    type="email"
                                >
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4">
                        <!-- Address Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.address') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('address', $client->address) }}"
                                    name="address"
                                    placeholder="@lang('app.address')"
                                    type="text"
                                >
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="city">@lang('app.city')</label>
                        <select name="city_id" class="form-control">
                            <option value="">@lang('app.select_city')</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', $client->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row row-sm mb-4">
                        <!-- Resource Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.source') *</div>
                                <select class="form-control" name="resource_id">
                                    <option value="">@lang('app.select_source')</option>
                                    @foreach ($sources as $resource)
                                        <option value="{{ $resource->id }}" {{ old('resource_id', $client->resource_id) == $resource->id ? 'selected' : '' }}>
                                            {{ $resource->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('resource_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4">
                        <!-- Industries -->
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.industry') *</div>
                                <select id="industry-select" name="industry[]" class="selectsum1" multiple="multiple">
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}"
                                            {{ in_array($industry->id, old('industry', $client->industries->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $industry->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('industry')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="row row-sm mb-4">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.services') *</div>
                                <select id="services-select" name="services[]" class="selectsum1" multiple="multiple">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ in_array($service->id, old('services', $client->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    @foreach ($services as $service)
                        @if ($service->categories->isNotEmpty())
                            <div id="categories-{{ $service->id }}" class="form-group category-select"
                                style="display: {{ in_array($service->id, old('services', $client->services->pluck('id')->toArray())) ? 'block' : 'none' }};">
                                <label>@lang('app.categories') for {{ $service->name }}</label>
                                <select name="services[{{ $service->id }}][category_id]" class="form-control">
                                    <option value="">@lang('app.select_category')</option>
                                    @foreach ($service->categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old("services.{$service->id}.category_id", $client->services->find($service->id)->pivot->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endforeach


                    <div class="row row-sm mb-4">
                    <!-- Custom Fields -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="main-content-label mg-b-5">@lang('app.custom_fields')</div>
                            @foreach ($customFields as $field)
                                <div>
                                    <label class="main-content-label mg-b-5">{{ $field->name }} *</label>
                                    @if ($field->type === 'text')
                                        <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control"
                                            value="{{ old('custom_fields.' . $field->id, $client->customFields->where('id', $field->id)->first()->pivot->value ?? '') }}">
                                    @elseif ($field->type === 'number')
                                        <input type="number" name="custom_fields[{{ $field->id }}]" class="form-control"
                                            value="{{ old('custom_fields.' . $field->id, $client->customFields->where('id', $field->id)->first()->pivot->value ?? '') }}">
                                    @elseif ($field->type === 'date')
                                        <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control"
                                            value="{{ old('custom_fields.' . $field->id, $client->customFields->where('id', $field->id)->first()->pivot->value ?? '') }}">
                                    @elseif ($field->type === 'dropdown')
                                        <select name="custom_fields[{{ $field->id }}]" class="form-control">
                                            @foreach ($field->options as $option)
                                                <option value="{{ $option }}"
                                                    {{ old('custom_fields.' . $field->id, $client->customFields->where('id', $field->id)->first()->pivot->value ?? '') == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                    <div class="card-footer mt-4">
                        <div class="form-group mb-0 mt-3 justify-content-end">
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save pe-2"></i>@lang('app.submit')</button>
                                <a role="button" href="{{ URL::previous() }}" class="btn btn-secondary"><i class="fa fa-backward pe-2"></i>@lang('app.back')</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- End Row -->

@endsection

@push('scripts')
    <!-- Additional scripts can be added here -->

    <!--Internal Fileuploads js-->
    <script src="{{ asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

    <!--Internal Fancy uploader js-->
    <script src="{{ asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>

    <!--Internal  Form-elements js-->
    <script src="{{ asset('assets/js/advanced-form-elements.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <!--Internal Sumoselect js-->
    <script src="{{ asset('assets/plugins/sumoselect/jquery.sumoselect.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize SumoSelect for services
            $('#services-select').SumoSelect({
                placeholder: 'Select Services',
                csv: true,
                search: true,
                searchText: 'Search...',
                noMatch: 'No matches found',
                selectAll: true,
                okCancel: true
            });

            // Show/hide categories based on selected services
            $('#services-select').on('change', function() {
                var selectedServices = $(this).val();
                $('.category-select').hide(); // Hide all category dropdowns
                if (selectedServices) {
                    selectedServices.forEach(function(serviceId) {
                        $('#categories-' + serviceId).show(); // Show the category dropdown for the selected service
                    });
                }
            });

            // Initialize the categories display based on pre-selected services
            var preSelectedServices = $('#services-select').val();
            if (preSelectedServices) {
                preSelectedServices.forEach(function(serviceId) {
                    $('#categories-' + serviceId).show();
                });
            }
        });
    </script>
@endpush
