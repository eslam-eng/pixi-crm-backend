<td>
    <div class="d-flex align-items-center">
        @can('edit sources')
            <a class="btn btn-primary me-2" href="{{ route('resources.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        @can('view sources')
        <a class="btn btn-primary me-2" href="{{ route('resources.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan
        @can('delete sources')
        <button class="btn btn-danger" role="button"
        onclick="destroy('{{route('resources.destroy', $model->id)}}')"> <i class="icon-trash"></i></button>
        @endcan
    </div>
</td>
