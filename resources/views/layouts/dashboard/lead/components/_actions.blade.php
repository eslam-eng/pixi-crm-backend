<td>
    <div class="d-flex align-items-center">
        @can('edit leads')
            <a class="btn btn-primary me-2" href="{{ route('leads.edit', $model->id) }}"><i class="icon-pencil"></i></a>
        @endcan
        {{-- @can('view leads')
            <a class="btn btn-primary me-2" href="{{ route('leads.show', $model->id) }}"><i class="icon-eye"></i></a>
        @endcan --}}
        @can('delete leads')
            <form action="{{ route('leads.destroy', $model->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="icon-trash"></i></button>
            </form>
        @endcan
    </div>
</td>
