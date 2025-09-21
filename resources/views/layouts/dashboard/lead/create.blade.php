@extends('layouts.app')

@section('styles')
    <!--Internal Sumoselect css-->
    <link rel="stylesheet" href="{{asset('assets/plugins/sumoselect/sumoselect.css')}}">

@endsection

@section('content')

{{-- Breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.create_new_lead_title'),
    'first_list_item' => trans('app.lead'),
    'last_list_item' => trans('app.add_lead'),
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

                <form action="{{ route('leads.store') }}" method="post">
                    @csrf


                    <div class="form-group">
                        <label for="contact">@lang('app.contact')</label>
                        <select name="contact_id" class="form-control">
                            <option value="">@lang('app.select_contact')</option>
                            @foreach ($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contact_id', $lead->contact_id ?? '') == $contact->id ? 'selected' : '' }}>
                                    {{ $contact->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assign_to">@lang('app.assign_to')</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">@lang('app.select_sales_representative')</option>
                            @foreach ($salesUsers as $user)
                                <option value="{{ $user->id }}" {{ old('assign_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assign_to')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="status">@lang('app.status')</label>
                        <select name="status" class="form-control" id="status">
                            <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>open</option>
                            <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>lost</option>
                            <option value="won" {{ old('status') === 'won' ? 'selected' : '' }}>won</option>
                            <option value="abandoned" {{ old('status') === 'abandoned' ? 'selected' : '' }}>abandoned</option>
                        </select>
                    </div>
                    <div class="form-group" id="value-group" style="display: none;">
                        <label for="value">@lang('app.value')</label>
                        <input type="number" name="value" class="form-control" value="{{ old('value') }}" step="0.01">
                    </div>
                    <div class="form-group" id="reason-group" style="display: none;">
                        <label for="reason">@lang('app.reason')</label>
                        <select name="reason_id" class="form-control">
                            <option value="">@lang('app.select_reason')</option>
                            @foreach ($reasons as $reason)
                                <option value="{{ $reason->id }}" {{ old('reason_id') == $reason->id ? 'selected' : '' }}>
                                    {{ $reason->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stage">@lang('app.stage')</label>
                        <select name="stage_id" class="form-control" id="stage">
                            <option value="">@lang('app.select_stage')</option>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->id }}" {{ old('stage_id') == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="form-group">
                        <label for="pipeline">@lang('app.pipeline')</label>
                        <select name="pipeline_id" class="form-control" id="pipeline">
                            <option value="">@lang('app.select_pipeline')</option>
                            @foreach ($pipelines as $pipeline)
                                <option value="{{ $pipeline->id }}" {{ old('pipeline_id') == $pipeline->id ? 'selected' : '' }}>
                                    {{ $pipeline->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}



                    <div class="row row-sm mb-4">
                        <!-- Industries -->
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.industry') *</div>
                                <select  id="industry-select" name="industry[]" class="selectsum1" multiple="multiple">

                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}" {{ (collect(old('industry'))->contains($industry->id)) ? 'selected' : '' }}>
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
                                        <option value="{{ $service->id }}" {{ collect(old('services'))->contains($service->id) ? 'selected' : '' }}>
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
                            <div id="categories-{{ $service->id }}" class="form-group category-select" style="display: none;">
                                <label>@lang('app.categories') for {{ $service->name }}</label>
                                <select name="services[{{ $service->id }}][category_id]" class="form-control">
                                    <option value="">@lang('app.select_category')</option>
                                    @foreach ($service->categories as $category)
                                        <option value="{{ $category->id }}" {{ old("services.{$service->id}.category_id") == $category->id ? 'selected' : '' }}>
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
                                            <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control">
                                        @elseif ($field->type === 'number')
                                            <input type="number" name="custom_fields[{{ $field->id }}]" class="form-control">
                                        @elseif ($field->type === 'date')
                                            <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control" >
                                        @elseif ($field->type === 'dropdown')
                                            <select name="custom_fields[{{ $field->id }}]" class="form-control">
                                                @foreach ($field->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
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
                                <button type="submit" class="btn btn-primary" ><i class="fa fa-save pe-2"></i>@lang('app.submit')</button>
                                {{-- <a role="button" href="{{ URL::previous() }}" class="btn btn-secondary"><i class="fa fa-backward pe-2"></i>@lang('app.back')</a> --}}
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
    <script src="{{asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>

    <!--Internal Fancy uploader js-->
    <script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
    <script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js')}}"></script>

    <!--Internal  Form-elements js-->
    <script src="{{asset('assets/js/advanced-form-elements.js')}}"></script>
    <script src="{{asset('assets/js/select2.js')}}"></script>

    <!--Internal Sumoselect js-->
    <script src="{{asset('assets/plugins/sumoselect/jquery.sumoselect.js')}}"></script>

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
    $(document).ready(function () {
        $('#status').on('change', function () {
            const status = $(this).val();
            $('#value-group').toggle(status === 'won');
            $('#reason-group').toggle(status === 'lost');
        }).trigger('change');
    });

    </script>
@endpush
