@extends('layouts.app')

@section('content')
    {{-- breadcrumb --}}
    @include('layouts.components.breadcrumb', [
        'title' => trans('app.edit_service_title'),
        'first_list_item' => trans('app.service'),
        'last_list_item' => trans('app.edit_service'),
    ])
    {{-- end breadcrumb --}}

    <!-- Row -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <!--div-->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('services.update', $service->id) }}" method="post" id="service-form">
                        @csrf
                        @method('put')
                        <!-- Service Name Input -->
                        <div class="row row-sm mb-4">
                            <div class="col-lg">
                                <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                                <input class="form-control" value="{{ $service->name }}" name="name"
                                    placeholder="@lang('app.name')" type="text">
                                @error('name')
                                    <div class="text-danger"> {{ $message }}</div>
                                @enderror
                            </div>


                        <!-- Service Price Input -->

                            <div class="col-lg">
                                <div class="main-content-label mg-b-5">@lang('app.price')</div>
                                <input class="form-control" value="{{ $service->price }}" name="price"
                                    placeholder="@lang('app.price')" type="number" step="0.01">
                                @error('price')
                                    <div class="text-danger"> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Categories Section -->
                        <div class="row row-sm mb-4">
                            <div class="col-lg">
                                <div id="categories-container" >
                                    @if($service->categories->isNotEmpty())
                                        @foreach($service->categories as $index => $category)
                                            <div class="row row-sm mb-4">
                                                <div class="col-lg">
                                                    <div class="main-content-label mg-b-5">@lang('app.category_name')</div>
                                                    <input class="form-control" name="categories[{{ $index }}][name]"
                                                        value="{{ $category->name }}" placeholder="@lang('app.category_name')" type="text">
                                                </div>
                                                <div class="col-lg">
                                                    <div class="main-content-label mg-b-5">@lang('app.price')</div>
                                                    <input class="form-control" name="categories[{{ $index }}][price]"
                                                        value="{{ $category->price }}" placeholder="@lang('app.price')" type="number" step="0.01">
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row row-sm mb-4">
                                            <div class="col-lg">
                                                <div class="main-content-label mg-b-5">@lang('app.category_name')</div>
                                                <input class="form-control" name="categories[0][name]" placeholder="@lang('app.category_name')" type="text">
                                            </div>
                                            <div class="col-lg">
                                                <div class="main-content-label mg-b-5">@lang('app.price')</div>
                                                <input class="form-control" name="categories[0][price]" placeholder="@lang('app.price')" type="number" step="0.01">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{-- <button type="button" id="add-category" class="btn btn-secondary mt-2">
                                    <i class="fa fa-plus pe-2"></i>@lang('app.add_category')
                                </button> --}}
                            </div>
                        </div>

                        <!-- Form Footer -->
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
@endsection

@section('script_footer')
    <script>
        // Add new category inputs
        // document.getElementById('add-category').addEventListener('click', function () {
        //     const container = document.getElementById('categories-container');
        //     const index = container.querySelectorAll('.row.row-sm.mb-4').length; // Count existing rows
        //     const newCategory = document.createElement('div');
        //     newCategory.classList.add('row', 'row-sm', 'mb-4');
        //     newCategory.innerHTML = `
        //         <div class="col-lg">
        //             <div class="main-content-label mg-b-5">@lang('app.category_name')</div>
        //             <input class="form-control" name="categories[${index}][name]" placeholder="@lang('app.category_name')" type="text">
        //         </div>
        //         <div class="col-lg">
        //             <div class="main-content-label mg-b-5">@lang('app.price')</div>
        //             <input class="form-control" name="categories[${index}][price]" placeholder="@lang('app.price')" type="number" step="0.01">
        //         </div>
        //     `;
        //     container.appendChild(newCategory);
        // });

        // // Remove empty category inputs before form submission
        // document.getElementById('service-form').addEventListener('submit', function (e) {
        //     const categoryInputs = document.querySelectorAll('.row.row-sm.mb-4');
        //     categoryInputs.forEach(input => {
        //         const name = input.querySelector('input[name*="[name]"]').value;
        //         const price = input.querySelector('input[name*="[price]"]').value;
        //         if (!name && !price) {
        //             input.remove(); // Remove empty category inputs
        //         }
        //     });
        // });
    </script>
@endsection
