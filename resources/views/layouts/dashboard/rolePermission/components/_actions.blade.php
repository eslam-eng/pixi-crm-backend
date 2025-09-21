<td>
    <div class="d-flex align-items-center">
        @can('edit role-permissions')
            <a class="btn btn-primary me-2" href="{{ route('role-permissions.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        {{-- @can('show role-permissions')
        <a class="btn btn-primary me-2" href="{{ route('role-permissions.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan --}}
        @can('delete role-permissions')
        <form action="{{ route('role-permissions.destroy', $model->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"> <i class="icon-trash"></i></button>
        </form>
        @endcan
    </div>
</td>
