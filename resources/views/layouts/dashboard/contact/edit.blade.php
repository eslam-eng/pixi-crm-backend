@extends('layouts.app')

@section('styles')
    <!--Internal Sumoselect css-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sumoselect/sumoselect.css') }}">
@endsection

@section('content')

{{-- Breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.edit_contact_title'),
    'first_list_item' => trans('app.contact'),
    'last_list_item' => trans('app.edit_contact'),
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

                <form action="{{ route('contacts.update', $contact->id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="row row-sm mb-4">
                        <!-- Name Field -->
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('name', $contact->name) }}"
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
                                    value="{{ old('phone', $contact->phone) }}"
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
                                    value="{{ old('email', $contact->email) }}"
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
                                    value="{{ old('address', $contact->address) }}"
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
                                <option value="{{ $city->id }}" {{ old('city_id', $contact->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->title }}
                                </option>
                            @endforeach
                        </select>
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

@endpush
