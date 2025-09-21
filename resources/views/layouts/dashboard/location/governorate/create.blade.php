@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.create_new_governorate_title'),
'first_list_item' => trans('app.governorates'),
'last_list_item' => trans('app.add_governorate'),
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

                <form action="{{route('locations.sublocation.store')}}" method="post">
                    @csrf
                    <input type="hidden" name="parent" value="country" value="parent_id">
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
                        </div>
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.country') *</div>
                                <select id="searchable-select" class="form-control" style="width: 100%;"
                                    name="parent_id">
                                    @forelse (app()->make(App\Services\LocationService::class)->getAll(filters:
                                    ['depth' =>0]); as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                    @empty
                                    <option value="">No Option</option>
                                    @endforelse
                                </select>
                                @error('parent_id')
                                <div class="text-danger"> {{$message}}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg d-flex justify-content-center">
                            <div class="form-group d-flex align-items-center">
                                <label class="custom-switch">
                                    <span class="custom-switch-description  tx-17 me-2">Status</span>
                                    <input type="hidden" name="status" value="inactive">
                                    <input type="checkbox" name="status" class="custom-switch-input" value="active">
                                    <span
                                        class="custom-switch-indicator custom-switch-indicator-lg custom-square"></span>
                                </label>
                                @error('status')
                                <div class="text-danger"> {{$message}}</div>
                                @enderror
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
<script>
    $(document).ready(function() {
            $('#searchable-select').select2({
                placeholder: 'Select an option',
                allowClear: true
            });
        });
</script>
@endsection