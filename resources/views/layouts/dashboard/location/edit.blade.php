@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.edit_location_title'),
'first_list_item' => trans('app.locations'),
'last_list_item' => trans('app.edit_location'),
])
{{-- end breadcrumb --}}

<!-- Row -->
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
        <!--div-->
        <div class="card">
            <div class="card-body">
                <form action="{{route('locations.update', $location->id)}}" method="post">
                    @csrf
                    @method('put')
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                            <input class="form-control" value="{{$location->title}}" name="name"
                                placeholder="@lang('app.name')" type="text">
                            @error('name')
                            <div class="text-danger"> {{$message}}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer mt-4">
                        <div class="form-group mb-0 mt-3 justify-content-end">
                            <div>
                                <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-save pe-2"></i>@lang('app.submit')</button>

                                <a role="button" href="{{ URL::previous() }}" class="btn btn-primary"><i
                                        class="fa fa-backward pe-2"></i>@lang('app.back')</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">Areas</h4>
                </div>

                <div class="form-group mb-0 mt-3 justify-content-end">
                    <div>
                        <a class="btn btn-primary" href="{{ route('locations.areas.create', $location->id) }}"><i
                                class="fe fe-plus me-2"></i>@lang('app.new')</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>title</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($areas as $area)
                            <tr>
                                <th scope="row">{{ $area->id }}</th>
                                <td>{{ $area->title }}</td>
                                <td>
                                    <x-action model="locations" id="{{$area->id}}" />
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-muted text-center">
                                    <span class="text-muted text-center">@lang('app.no_data_found')</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->
@endsection