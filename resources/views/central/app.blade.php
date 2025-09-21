<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Nowa – Laravel Bootstrap 5 Admin & Dashboard Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin dashboard, admin dashboard laravel, admin panel template, blade template, blade template laravel, bootstrap template, dashboard laravel, laravel admin, laravel admin dashboard, laravel admin panel, laravel admin template, laravel bootstrap admin template, laravel bootstrap template, laravel template" />
    <!-- Title -->


    <link rel="stylesheet" href="https://kit.fontawesome.com/f938b78ffc.css" crossorigin="anonymous" />

    <!-- Not required if jQuery is already loaded -->
    <title>CRM - Central app </title>

    @include('layouts.components.styles')
    <livewire:styles />
    @yield('after_styles')

    {{-- @vite(['resources/js/app.js', 'resources/css/app.css']) --}}
</head>

<body class="ltr main-body app sidebar-mini">

    <!-- Loader -->
    <div id="global-loader">
        <img src="{{asset('assets/images/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    <!-- Page -->
    <div class="page" style="background-color: #ffffff; ">

        <div>
            @include('layouts.components.app-header')

            @include('layouts.components.app-sidebar')

        </div>

        <!-- main-content -->
        <div class="main-content app-content">

            <!-- container -->
            <div class="main-container container-fluid">

                @yield('content')

            </div>
            <!-- Container closed -->
        </div>
        <!-- main-content closed -->

        @include('layouts.components.sidebar-right')

        @include('layouts.components.modal')


        @yield('modal')

        @include('layouts.components.footer')

    </div>
    <!-- End Page -->

    @include('layouts.components.scripts')
    @include('layouts.components.toastr')


    <!-- Include all pushed scripts here -->

    <script>
        function destroy(url) {
        swal({
            title: "{{__('app.are_you_sure')}}",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((confirmed) => {
            if (confirmed) {
                $.ajax({
                    method: 'DELETE',
                    url: url,
                    dataType: 'json',
                    data:{
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(result) {
                        if (result.status)
                        {
                            toastr.success(result.message);
                            if ($('.dataTable').length) {
                                $('.dataTable').DataTable().ajax.reload(null, false);
                            }else{
                                window.location.reload();
                            }
                        }
                        else
                            toastr.error(result.message);
                    } ,
                    error: function(jqXHR, textStatus, errorThrown) {

                        var errorMessage = jqXHR.responseJSON.message;
                        toastr.error(errorMessage);
                    }
                });
            }
        });
    }
    </script>

    <!-- JavaScript for Modal -->
    <script>
        Livewire.on('close-modal', (data) => {
            var modalElement = document.getElementById('modaldemo3');
            var modal = bootstrap.Modal.getInstance(modalElement); // Get the existing modal instance

            if (modal) {
                modal.hide(); // Hide the modal
            } else {
                console.error("Bootstrap modal instance not found! Trying to create a new instance...");
                modal = new bootstrap.Modal(modalElement);
                modal.hide();
            }

            $('.dataTable').DataTable().ajax.reload(null, false);
            toastr.success(data[0].message);
        });
    </script>
    @yield('script_footer')
    @stack('scripts')
</body>

</html>