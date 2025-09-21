@extends('layouts.app')

@section('content')
{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.permission_title'),
'first_list_item' => trans('app.permission'),
'last_list_item' => trans('app.permission'),
])
{{-- end breadcrumb --}}


    <div class="row">
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <!--div-->
            <div class="card">
                <div class="card-body">
                    <h1>@lang('app.Permissions'): {{ $role->name }}</h1>
                    <form action="{{ route('role-permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label>@lang('app.Permissions')</label>
                            @foreach ($permissions as $permission)
                            <div class="form-check">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                <label for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div class="card-footer mt-4">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                <button type="submit" class="btn btn-primary"><i
                                    class="fa fa-save pe-2"></i>@lang('app.Update Permissions')</button>
                                <a role="button" href="{{ URL::previous() }}" class="btn btn-primary"><i
                                    class="fa fa-backward pe-2"></i>@lang('app.back')</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
