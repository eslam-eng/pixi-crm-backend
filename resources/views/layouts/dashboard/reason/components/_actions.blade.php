<td>
    <div class="d-flex align-items-center">
        @can('edit reasons')
            <a class="btn btn-primary me-2" href="{{ route('reasons.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        {{-- @can('view reasons')
            <a class="btn btn-primary me-2" href="{{ route('reasons.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan --}}
        @can('delete reasons')
        <button class="btn btn-danger" role="button"
        onclick="destroy('{{route('reasons.destroy', $model->id)}}')"> <i class="icon-trash"></i></button>
    @endcan
    </div>
</td>
