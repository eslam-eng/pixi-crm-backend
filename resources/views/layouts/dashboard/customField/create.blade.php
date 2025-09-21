@extends('layouts.app')

@section('content')

{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.create_new_customField_title'),
    'first_list_item' => trans('app.customField'),
    'last_list_item' => trans('app.add_customField'),
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

                <form action="{{ route('custom-fields.store') }}" method="post">
                    @csrf
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.name') *</div>
                                <input
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    name="name"
                                    placeholder="@lang('app.name')"
                                    type="text"
                                    required
                                    autofocus
                                    maxlength="255"
                                >
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.type') *</div>
                                <select
                                    class="form-control"
                                    name="type"
                                    required
                                >
                                    <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                                    <option value="date" {{ old('type') == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="dropdown" {{ old('type') == 'dropdown' ? 'selected' : '' }}>Dropdown</option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm mb-4" id="options-container" style="display: none;">
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.options')</div>
                                <div id="options-list">
                                    <input
                                        class="form-control mb-2"
                                        name="options[]"
                                        placeholder="@lang('app.option_placeholder')"
                                        type="text"
                                    >
                                </div>
                                <button type="button" id="add-option" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-plus"></i> @lang('app.add_option')
                                </button>
                                @error('options')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input to clear options when type is not dropdown -->
                    <input type="hidden" name="options" value="" id="hidden-options">

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
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.querySelector('select[name="type"]');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const addOptionButton = document.getElementById('add-option');
        const hiddenOptionsInput = document.getElementById('hidden-options');

        // Function to toggle options container visibility
        function toggleOptions() {
            if (typeSelect.value === 'dropdown') {
                optionsContainer.style.display = 'block';
                hiddenOptionsInput.disabled = true; // Enable options input
            } else {
                optionsContainer.style.display = 'none';
                hiddenOptionsInput.disabled = false; // Clear options input
            }
        }

        // Function to add a new option input field
        function addOption() {
            const newInput = document.createElement('input');
            newInput.className = 'form-control mb-2';
            newInput.name = 'options[]';
            newInput.placeholder = "@lang('app.option_placeholder')";
            newInput.type = 'text';
            optionsList.appendChild(newInput);
        }

        // Event listener for type select change
        typeSelect.addEventListener('change', toggleOptions);

        // Event listener for add option button
        addOptionButton.addEventListener('click', addOption);

        // Initialize on page load
        toggleOptions();
    });
</script>
@endsection
