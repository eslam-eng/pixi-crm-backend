<td class="text-end">
    <label class="custom-control custom-checkbox custom-control-md">
        <input type="checkbox" class="custom-control-input datatable-checkboxes"
               name="{{$name}}" value="{{$value}}">
        <span
            class="custom-control-label custom-control-label-md  tx-17"></span>
    </label>
</td>
{{--todo move this to another place for load once not loadded with every row in datatatble--}}
{{-- <script>
    $(document).ready(function () {
        var selected_ids = [];
        $('.datatable-checkboxes').change(function() {
            if ($(this).is(':checked')) {
                // Perform action when checkbox is checked
                selected_ids.push($(this).val())
                console.log('Checkbox is checked.');

            } else {
                // Perform action when checkbox is unchecked
                selected_ids.pop($(this).val())
                console.log('Checkbox is unchecked.');
            }
        });

        $(".checkAll").click(function(){
            $('.datatable-checkboxes').not(this).prop('checked', this.checked);
            $('input[name="awbs[]"]').each(function(i){
                if ($(this).is(':checked')) {
                    // Perform action when checkbox is checked
                    selected_ids.push($(this).val())
                    console.log('Checkbox is checked.');

                } else {
                    // Perform action when checkbox is unchecked
                    selected_ids.pop($(this).val())
                    console.log('Checkbox is unchecked.');
                }
            });
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
        $(".print_awbs").click(function (event) {
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
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        _token:csrf,
                        ids:selected_ids,
                        status:status_id
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
                        }
                        else
                            toastr.error(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr);
                    }
                });
            }else{
                $('#changeAwbsStatus').modal('toggle');
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'please select at least one to change Status.php!',
                })
            }

        });
    });
</script> --}}
