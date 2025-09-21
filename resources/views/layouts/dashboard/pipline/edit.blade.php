@extends('layouts.app')

@section('content')
@include('layouts.components.breadcrumb', [
    'title' => trans('app.piplines_title'),
    'first_list_item' => trans('app.piplines'),
    'last_list_item' => trans('app.edit_pipline'),
])

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
        <form action="{{ route('piplines.update', $pipline->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">@lang('app.pipline_name')</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $pipline->name) }}" required>
            </div>

            <div class="form-group">
                <label for="stages">@lang('app.Stages')</label>
                <div id="stages">
                    @foreach ($pipline->stages as $index => $stage)
                        <div class="stage d-flex align-items-center mb-2">
                            <button type="button" class="btn btn-light me-2 move-up">&#x2191;</button>
                            <button type="button" class="btn btn-light me-2 move-down">&#x2193;</button>
                            <input type="text" name="stages[{{ $index }}][name]" class="form-control me-2" value="{{ $stage->name }}" placeholder="@lang('app.Stage Name')" required>
                            <button type="button" class="btn btn-danger delete-stage" data-index="{{ $index }}">
                                <i class="icon-trash"></i>
                            </button>

                            {{-- <input type="number" name="stages[{{ $index }}][seq_number]" class="form-control me-2" value="{{ $stage->seq_number }}" placeholder="@lang('app.Sequence Number')" required> --}}
                        </div>
                    @endforeach
                </div>
                <button type="button" id="addStage" class="btn btn-primary mt-3">@lang('app.Add Stage')</button>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save pe-2"></i>@lang('app.update')
                </button>
                <a role="button" href="{{ route('piplines.index') }}" class="btn btn-secondary">
                    <i class="fa fa-backward pe-2"></i>@lang('app.back')
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('addStage').addEventListener('click', function() {
        const stagesDiv = document.getElementById('stages');
        const stageCount = stagesDiv.querySelectorAll('.stage').length;
        const newStage = document.createElement('div');
        newStage.classList.add('stage', 'd-flex', 'align-items-center', 'mb-2');
        newStage.innerHTML = `
            <button type="button" class="btn btn-light me-2 move-up">&#x2191;</button>
            <button type="button" class="btn btn-light me-2 move-down">&#x2193;</button>
            <input type="text" name="stages[${stageCount}][name]" class="form-control me-2" placeholder="@lang('app.Stage Name')" required>
            <button type="button" class="btn btn-danger delete-stage" data-index="${stageCount}">
                <i class="icon-trash"></i>
            </button>
        `;
        stagesDiv.appendChild(newStage);
        addMoveListeners();
        addDeleteListeners();
    });

    function addMoveListeners() {
        const stagesDiv = document.getElementById('stages');
        stagesDiv.addEventListener('click', function(e) {
            const stage = e.target.closest('.stage');
            if (!stage) return;

            if (e.target.classList.contains('move-up')) {
                const prev = stage.previousElementSibling;
                if (prev) {
                    stagesDiv.insertBefore(stage, prev);
                    updateStageNames();
                }
            }

            if (e.target.classList.contains('move-down')) {
                const next = stage.nextElementSibling;
                if (next) {
                    stagesDiv.insertBefore(next, stage);
                    updateStageNames();
                }
            }
        });
    }

    function addDeleteListeners() {
        const deleteButtons = document.querySelectorAll('.delete-stage');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const stage = button.closest('.stage');
                if (stage) {
                    stage.remove();
                    updateStageNames();
                }
            });
        });
    }

    function updateStageNames() {
        const stages = document.querySelectorAll('.stage');
        stages.forEach((stage, index) => {
            const nameInput = stage.querySelector('input[name^="stages"][name$="[name]"]');
            nameInput.setAttribute('name', `stages[${index}][name]`);
        });
    }

    addMoveListeners();
    addDeleteListeners();

</script>
@endpush
@endsection
