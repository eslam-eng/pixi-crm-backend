@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.create_new_country_title'),
'first_list_item' => trans('app.countries'),
'last_list_item' => trans('app.add_country'),
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

                <form action="{{route('locations.store')}}" method="post">
                    @csrf
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
    $('.main-toggle').on('click', function() {
        $(this).toggleClass('on');
    })

    $(document).ready(function () {
        $("#status-toggle").change(function () {
            let status = $(this).is(":checked") ? "active" : "inactive";
            $(".main-toggle").attr("data-status", status);
            console.log("Status:", status);
        });
    });
</script>

@endsection