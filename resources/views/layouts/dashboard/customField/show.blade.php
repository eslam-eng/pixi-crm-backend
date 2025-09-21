@php
use \Illuminate\Support\Arr;
@endphp
@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.show_custom-field_title'),
    'first_list_item' => trans('app.custom-field'),
    'last_list_item' => trans('app.show_custom-field')
])
{{-- end breadcrumb --}}

<!-- Row -->
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
        <!--div-->
        <div class="card">
            <div class="card-body">
                <div class="row row-sm mb-4">
                    <div class="col-lg">
                        <div class="main-content-label mg-b-5">@lang('app.name')</div>
                        <label class="form-control">{{ $customField->name }}</label>
                    </div>
                    <div class="col-lg">
                        <div class="main-content-label mg-b-5">@lang('app.type')</div>
                        <label class="form-control">{{ $customField->type }}</label>
                    </div>
                </div>

                {{-- Show options if type is dropdown --}}
                @if ($customField->type === 'dropdown')
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="main-content-label mg-b-5">@lang('app.options')</div>
                            <div >
                                <ul>
                                    @foreach (Arr::wrap($customField->options) as $option)
                                        <li>{{ $option }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- End Row -->

@endsection
