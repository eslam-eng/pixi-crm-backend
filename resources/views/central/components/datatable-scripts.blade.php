<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
<script src="{{asset('assets/js/datatable-filter.js')}}"></script>

{{-- select box --}}
<script>
    $(document).ready(function () {
        var selected_ids = [];
        // $('.datatable-checkboxes').change(function() {
        //     console.log('Checkbox is checked.');
        //     if ($(this).is(':checked')) {
        //         // Perform action when checkbox is checked
        //         selected_ids.push($(this).val())
        //         console.log('Checkbox is checked.');

        //     } else {
        //         // Perform action when checkbox is unchecked
        //         selected_ids.pop($(this).val())
        //         console.log('Checkbox is unchecked.');
        //     }
        // });
        // let valueToAddOrRemove = $(this).val();

        // $(".checkAll").click(function(){
        //     $('.datatable-checkboxes').not(this).prop('checked', this.checked);
        //     $('input[name="awbs[]"]').each(function(i){
        //         if ($(this).is(':checked')) {
        //             // Perform action when checkbox is checked
        //             selected_ids.push($(this).val())
        //             console.log('Checkbox is checked.');

        //         } else {
        //             // Perform action when checkbox is unchecked
        //             selected_ids.pop($(this).val())
        //             console.log('Checkbox is unchecked.');
        //         }
        //     });
        //     console.log(selected_ids);
        // });
        

        $(document).on('change','.datatable-checkboxes',function() {
            let valueToAddOrRemove = $(this).val();
            let index = selected_ids.indexOf(valueToAddOrRemove);
            if ($(this).is(':checked')) {
                // Checkbox is checked
                if (index !== -1) {
                    // Value is already in the array, remove it
                    selected_ids.splice(index, 1);
                }
                // Add the value to the end of the array
                selected_ids.push(valueToAddOrRemove);
                console.log(`Checkbox is checked. ${valueToAddOrRemove}`);

            } else {
                // Checkbox is unchecked
                if (index !== -1) {
                    // Value is in the array, remove it
                    selected_ids.splice(index, 1);
                    console.log('Checkbox is unchecked. Value removed.');
                } else {
                    // Value is not in the array
                    console.log('Checkbox is unchecked. Value not found in the array.');
                }
            }
            // console.log(selected_ids);
            // console.log(JSON.stringify(selected_ids));
            console.log(selected_ids);
        });

        $(".checkAll").click(function () {
            $('.datatable-checkboxes').not(this).prop('checked', this.checked);
            $('input[name="awbs[]"]').each(function (i) {
                let valueToAddOrRemove = $(this).val();
                
                if ($(this).is(':checked')) {
                    // Checkbox is checked
                    if (selected_ids.indexOf(valueToAddOrRemove) === -1) {
                        // Value is not in the array, add it
                        selected_ids.push(valueToAddOrRemove);
                    }
                    console.log('Checkbox is checked.');
                } else {
                    // Checkbox is unchecked
                    let indexToRemove = selected_ids.indexOf(valueToAddOrRemove);
                    if (indexToRemove !== -1) {
                        // Value is in the array, remove it
                        selected_ids.splice(indexToRemove, 1);
                        console.log('Checkbox is unchecked. Value removed.');
                    } else {
                        // Value is not in the array
                        console.log('Checkbox is unchecked. Value not found in the array.');
                    }
                }
            });
            // console.log(selected_ids);
        });

        

        $(".delete-selected-btn").click(function () {
            if (selected_ids.length)
            {
                var url = $(this).data('url');
                var csrf = $(this).data('csrf')
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        _token:csrf,
                        ids:selected_ids,
                        _method:'delete'
                    },
                    success: function(response) {
                        if (response.status)
                        {
                            toastr.success(response.message);
                            $('.dataTable').DataTable().ajax.reload(null, false);
                        }
                        else
                            toastr.error(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr);
                    }
                });
            }else{
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to delete!',
                })
            }

        });
        //
        // console.log(JSON.stringify(selected_ids));
        $(".print_awbs").click(function (event) {
            console.log(selected_ids);
            event.preventDefault();
            if (selected_ids.length)
            {
                $('#awbs_ids').val(JSON.stringify(selected_ids));
                $('#default_print_awbs').submit();
            }else{
                $('#print_awbs_modal').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to print!',
                })
            }

        });

        $(".print_awbs_2").click(function (event) {
            event.preventDefault();
            if (selected_ids.length)
            {
                $('#awbs_ids_2').val(JSON.stringify(selected_ids));
                $('#default_print_awbs_2').submit();
            }else{
                $('#print_awbs_modal').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to print!',
                })
            }

        });

        $(".print_courierSheet").click(function (event) {
            event.preventDefault();
            if (selected_ids.length)
            {
                $('#awbs_ids_2').val(JSON.stringify(selected_ids));
                $('#default_print_awbs_2').submit();
            }else{
                
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to print!',
                })
            }

        });



        $(".print_duplicated").click(function (event) {
            event.preventDefault();
            if (selected_ids.length)
            {
                $('#awbs_ids_duplicate').val(JSON.stringify(selected_ids));
                $('#print_duplicate_awbs').submit();
            }else{
                $('#print_awbs_modal').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to print!',
                })
            }
        });

        $(".print_duplicated_2").click(function (event) {
            event.preventDefault();
            if (selected_ids.length)
            {
                $('#awbs_ids_duplicate_2').val(JSON.stringify(selected_ids));
                $('#print_duplicate_awbs_2').submit();
            }else{
                $('#print_awbs_modal').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to print!',
                })
            }
        });

        $("#change_awb_status").click(function () {
            if (selected_ids.length)
            {

                var url = $(this).data('url');
                var csrf = $(this).data('csrf')
                var reload = $(this).data('reload');
                var status_id = $("#awb_status").val();
                var date = $("#date").val();
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        _token:csrf,
                        ids:selected_ids,
                        status:status_id,
                        date:date
                    },
                    success: function(response) {
                        if (response.status)
                        {
                            $('#changeAwbsStatus').modal('toggle');
                            toastr.success(response.message);
                            if(reload != true)
                                $('.dataTable').DataTable().ajax.reload(null, false);
                            else
                                window.location.reload();
                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON.message);
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }else{
                $('#changeAwbsStatus').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to change Status !',
                })
            }

        });
    });
</script>

