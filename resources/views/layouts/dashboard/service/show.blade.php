 @php
 use \Illuminate\Support\Arr;
 @endphp
 @extends('layouts.app')

 @section('content')
     {{-- breadcrumb --}}
     @include('layouts.components.breadcrumb', [
         'title' => trans('app.show_service'),
         'first_list_item' => trans('app.service'),
         'last_list_item' => trans('app.show_service')
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
                             <label class="form-control">{{ $service->name }}</label>
                         </div>
                         <div class="col-lg">
                             <div class="main-content-label mg-b-5">@lang('app.price')</div>
                             <label class="form-control">{{ $service->price }}</label>
                         </div>
                     </div>

                     <!-- Display Categories if they exist -->
                     @if($service->categories->isNotEmpty())
                         <div class="row row-sm mb-4">
                             <div class="col-lg">
                                 <div class="main-content-label mg-b-5">@lang('app.categories')</div>
                                 <table class="table table-bordered">
                                     <thead>
                                         <tr>
                                             <th>@lang('app.category_name')</th>
                                             <th>@lang('app.price')</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         @foreach($service->categories as $category)
                                             <tr>
                                                 <td>{{ $category->name }}</td>
                                                 <td>{{ $category->price }}</td>
                                             </tr>
                                         @endforeach
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     @else
                         <div class="row row-sm mb-4">
                             <div class="col-lg">
                                 <div class="main-content-label mg-b-5">@lang('app.categories')</div>
                                 <label class="form-control">@lang('app.no_categories_found')</label>
                             </div>
                         </div>
                     @endif
                 </div>
             </div>
         </div>
     </div>
     <!-- End Row -->
 @endsection
