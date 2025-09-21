@php
use \Illuminate\Support\Arr ;
@endphp
@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb',['title' => trans('app.show_pipline_title'),'first_list_item' =>
trans('app.pipline'),'last_list_item' => trans('app.show_pipline')])
{{-- end breadcrumb --}}

<!-- Row -->

<!-- Row -->
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
        <!--div-->
        <div class="card">
            <div class="card-body">
                <div class="row row-sm mb-4">

                        <div class="main-content-label mg-b-5">@lang('app.pipline_name')</div>
                        <label class="form-control">{{ $pipline->name }}</label>
                    </div>


                    <h4 class="mb-4">@lang('app.stage_details')</h4>

                    <div class="card-body">
                        <ul>
                            @foreach ($pipline->stages as $stage)
                                <li>{{ $stage->name }} (Sequence: {{ $stage->seq_number }})</li>
                            @endforeach
                        </ul>
                    </div>
                    </div>


            </div>
            </div>
        </div>

    </div>
</div>

<!-- End Row -->

<!-- End Row -->

@endsection
