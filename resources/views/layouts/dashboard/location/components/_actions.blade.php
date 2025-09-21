<td>
    <div>
        @can('edit locations')

        <a class="btn btn-primary" href="{{route($path,$model->id)}}"> <i class="icon-pencil"></i></a>
        @endcan

        @can('delete locations')
        <button class="btn btn-danger" role="button"
            onclick="destroy('{{route('locations.destroy', $model->id)}}')"> <i class="icon-trash"></i></button>
        @endcan
    </div>
</td>
