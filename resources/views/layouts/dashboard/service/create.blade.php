@extends('layouts.app')

@section('content')
    {{-- breadcrumb --}}
    @include('layouts.components.breadcrumb', [
        'title' => trans('app.create_new_service_title'),
        'first_list_item' => trans('app.service'),
        'last_list_item' => trans('app.add_service'),
    ])
    {{-- end breadcrumb --}}

    <!-- Row -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <!--div-->
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
                    <form action="{{ route('services.store') }}" method="post" id="service-form">
                        @csrf
                        <!-- Service Input Section -->
                        <div class="row row-sm mb-4">
                            <div class="col-lg">
                                <div class="form-group">
                                    <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                                    <input class="form-control" value="{{ old('name') }}" name="name"
                                        placeholder="@lang('app.name')" type="text">
                                    @error('name')
                                        <div class="text-danger"> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="form-group">
                                    <div class="main-content-label mg-b-5">@lang('app.price')</div>
                                    <input class="form-control" value="{{ old('price') }}" name="price"
                                        placeholder="@lang('app.price')" type="number" step="0.01">
                                    @error('price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Service Section -->
                        <div class="row row-sm mb-4">
                            <div class="col-lg">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is-sub-service"
                                            name="is_sub_service" value="1">
                                        <label class="form-check-label" for="is-sub-service">
                                            @lang('app.is_sub_service')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parent Service Dropdown -->
                        <div class="row row-sm mb-4" id="parent-service-section" style="display: none;">
                            <div class="col-lg">
                                <div class="form-group">
                                    <div class="main-content-label mg-b-5">@lang('app.parent_service')</div>
                                    <select class="form-control" name="parent_service_id">
                                        <option value="">@lang('app.select_service')</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer mt-4">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save pe-2"></i>@lang('app.submit')
                                    </button>
                                    <a role="button" href="{{ URL::previous() }}" class="btn btn-primary">
                                        <i class="fa fa-backward pe-2"></i>@lang('app.back')
                                    </a>
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

@section('script_footer')
    <script>
        // Show or hide the parent service dropdown based on the checkbox
        document.getElementById('is-sub-service').addEventListener('change', function() {
            const parentServiceSection = document.getElementById('parent-service-section');
            const parentServiceInput = document.querySelector('select[name="parent_service_id"]');

            if (this.checked) {
                parentServiceSection.style.display = 'block';
                parentServiceInput.setAttribute('required', 'required');
            } else {
                parentServiceSection.style.display = 'none';
                parentServiceInput.removeAttribute('required');
            }
        });

    </script>
@endsection
