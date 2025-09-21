<td>
    <div class="d-flex align-items-center">
        @can('edit piplines')
            <a class="btn btn-primary me-2" href="{{ route('piplines.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        @can('view piplines')
            <a class="btn btn-primary me-2" href="{{ route('piplines.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan
        @can('delete piplines')
        <button class="btn btn-danger" role="button"
        onclick="destroy('{{route('piplines.destroy', $model->id)}}')"> <i class="icon-trash"></i></button>
    @endcan
    </div>
</td>
