@extends('layouts.app')

@section('content')
{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
    'title' => trans('app.piplines_title'),
    'first_list_item' => trans('app.piplines'),
    'last_list_item' => trans('app.create_pipline'),
])
{{-- end breadcrumb --}}
<!-- Row -->
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
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
                <form action="{{ route('piplines.store') }}" method="POST">
                    @csrf
                    <div class="row row-sm mb-4">
                        <div class="col-lg">
                            <div class="form-group">
                                <div class="main-content-label mg-b-5">@lang('app.pipline_name') *</div>
                                <input class="form-control" value="{{ old('name') }}" name="name" placeholder="@lang('app.name')" type="text">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stages">@lang('app.Stages')</label>
                        <div id="stages">
                            <div class="stage d-flex align-items-center mb-2">
                                <button type="button" class="btn btn-light me-2 move-up">&#x2191;</button>
                                <button type="button" class="btn btn-light me-2 move-down">&#x2193;</button>
                                <input type="text" name="stages[0][name]" class="form-control" placeholder="@lang('app.Stage Name')" required>
                            </div>
                        </div>
                        <button type="button" id="addStage" class="btn btn-primary mt-3">@lang('app.Add Stage')</button>
                    </div>

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

@push('scripts')
<script>
    document.getElementById('addStage').addEventListener('click', function () {
        const stagesDiv = document.getElementById('stages');
        const stageCount = stagesDiv.querySelectorAll('.stage').length;
        const newStage = document.createElement('div');
        newStage.classList.add('stage', 'd-flex', 'align-items-center', 'mb-2');
        newStage.innerHTML = `
            <button type="button" class="btn btn-light me-2 move-up">&#x2191;</button>
            <button type="button" class="btn btn-light me-2 move-down">&#x2193;</button>
            <input type="text" name="stages[${stageCount}][name]" class="form-control" placeholder="Stage Name" required>
        `;
        stagesDiv.appendChild(newStage);
        updateStageNames();
    });

    function addMoveListeners() {
        const stagesDiv = document.getElementById('stages');

        stagesDiv.addEventListener('click', function (e) {
            if (e.target.classList.contains('move-up') || e.target.classList.contains('move-down')) {
                const stage = e.target.closest('.stage');
                if (!stage) return;

                const isMoveUp = e.target.classList.contains('move-up');
                const sibling = isMoveUp ? stage.previousElementSibling : stage.nextElementSibling;

                if (sibling && sibling.classList.contains('stage')) {
                    if (isMoveUp) {
                        stagesDiv.insertBefore(stage, sibling);
                    } else {
                        stagesDiv.insertBefore(sibling, stage);
                    }
                    updateStageNames();
                }
            }
        });
    }

    function updateStageNames() {
        const stages = document.querySelectorAll('.stage');
        stages.forEach((stage, index) => {
            const input = stage.querySelector('input');
            input.setAttribute('name', `stages[${index}][name]`);
        });
    }

    addMoveListeners();
</script>

@endpush
