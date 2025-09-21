@extends('layouts.app')

@section('content')
{{-- breadcrumb --}}
@include('layouts.components.breadcrumb', [
'title' => trans('app.role_title'),
'first_list_item' => trans('app.role'),
'last_list_item' => trans('app.add_role'),
])
{{-- end breadcrumb --}}
<div class="row">
    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
        <!--div-->
        <div class="card">
            <div class="card-body">
                <div class="card-header">@lang('app.Create New Role')</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('role-permissions.store') }}">
                        @csrf

                        <!-- Role Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('app.Role Name')</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label for="permissions" class="form-label">@lang('app.Assign Permissions')</label>
                            <div class="form-check">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                                <label class="form-check-label" for="selectAll"><strong>@lang('app.Select All')</strong></label>
                            </div>

                            @foreach ($permissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input permission-checkbox">
                                    <label class="form-check-label">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="card-footer mt-4">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save pe-2"></i>@lang('app.submit')</button>
                                    <a role="button" href="{{ URL::previous() }}" class="btn btn-secondary"><i class="fa fa-backward pe-2"></i>@lang('app.back')</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Select all permissions
    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>

@endsection
