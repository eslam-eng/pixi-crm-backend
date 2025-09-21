<td>
    <div class="d-flex align-items-center">
        @can('edit customFields')
            <a class="btn btn-primary me-2" href="{{ route('custom-fields.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        {{-- @can('view customFields')
            <a class="btn btn-primary me-2" href="{{ route('custom-fields.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan --}}
        @can('delete customFields')
        <button class="btn btn-danger" role="button"
        onclick="destroy('{{route('custom-fields.destroy', $model->id)}}')"> <i class="icon-trash"></i></button>
    @endcan
    </div>
</td>
