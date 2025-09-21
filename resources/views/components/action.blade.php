<div>
    <a class="btn btn-primary" href="{{ route($model . '.edit', $id) }}">@lang('app.edit')</a>
    <button class="btn btn-danger" role="button" onclick="destroy('{{ route($model . '.destroy', $id) }}')">@lang('app.delete')</button>
</div>
