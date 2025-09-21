@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.create_new_area_title'),
'first_list_item' => trans('app.areas'),
'last_list_item' => trans('app.add_area'),
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

                <form action="{{route('locations.areas.store')}}" method="post">
                    @csrf
                    <!-- Hidden input for city_id -->
                    <input type="hidden" name="city_id" value="{{ $city->id }}">
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.title') *</div>
                                <input class="form-control" value="{{old('title')}}" name="title"
                                    placeholder="@lang('app.title')" type="text">
                                @error('title')
                                <div class="text-danger"> {{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">For {{ $city->title }} </div>
                            </div>
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
    </div>
</div>

<!-- End Row -->

@endsection
@section('script_footer')

@endsection
