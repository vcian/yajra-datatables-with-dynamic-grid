<!DOCTYPE html>
<html>
<head>
    <title>Laravel 7 Datatables Tutorial - ItSolutionStuff.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Laravel 7 Datatables</h1>
    <div>
        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
        <div style="display:none" class="alert alert-success col-sm-12"></div>
        <br/>
        <div class="container">
        <span>Show/Hide Columns</span><br>
            <input type="checkbox" data-id="No" name='hide_columns[]' {{ (in_array('DT_RowIndex',$checkedFields)) ? 'checked': ''}} value='DT_RowIndex'> No
            <input type="checkbox" data-id="Name" name='hide_columns[]' {{ (in_array('name',$checkedFields)) ? 'checked': '' }} value='name'> Name
            <input type="checkbox" data-id="Email" name='hide_columns[]' {{ (in_array('email',$checkedFields)) ? 'checked': '' }} value='email'> Email
            <input type="checkbox" data-id="Action" name='hide_columns[]' {{ (in_array('action',$checkedFields)) ? 'checked': '' }} value='action'> Action
        <input type="button" class="btn btn-success btn-sm" id="but_showhide" value='save grids'>
    </div>
    <table class="table table-bordered data-table">
        <thead>
        <tr class="data-id-info">
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

    <script type="text/javascript">

        $(function () {
            var selected_arr = [];
            function setColumns() {
                $(".data-id-info").html('');
                selected_arr = [];
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    if(this.value=='DT_RowIndex') {
                        selected_arr.push({data: this.value, name: this.value})
                    } else if(this.value=='action') {
                        selected_arr.push({data: this.value, name: this.value, orderable: false, searchable: false})
                    } else {
                        selected_arr.push({data: this.value, name: this.value});
                    }
                    $(".data-id-info").append('<th>'+$(this).attr('data-id')+'</th>');
                });
            }
            setColumns();
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: selected_arr,
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'pageLength','copy', 'csv', 'excel', 'pdf', 'print',
                ]
            });

            // Hide & show columns
            $('#but_showhide').click(function(){

                if($('input[type="checkbox"]:checked').length == 0) {
                    alert('You must select at least one column!')
                    return false;
                }
                var checked_arr = [];var unchecked_arr = [];var checked_arrTr = [];
                // Read all checked checkboxes
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    checked_arr.push(this.value);
                    checked_arrTr.push($(this).attr('data-id'));
                });

                $.ajax({
                    url: "{{ url('/post-column-list/users') }}",
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        fields: checked_arr,
                        fieldname: checked_arrTr,
                        user_id: 1,
                    },
                    beforeSend: function() {
                        $('.alert-success,.alert-danger').html('');
                        $('.alert-success,.alert-danger').hide();
                    },
                    success: function(data){
                        if(data.code=='200') {
                            $('.alert-success').show();
                            $('.alert-success').append(data.status);
                        } else {
                            $('.alert-danger').show();
                            $('.alert-danger').append(data.status);
                        }
                    },
                    error: function (jqXHR) {
                        $('.alert-danger').show();
                        $('.alert-danger').append(JSON.parse(jqXHR.responseText).message);
                    }
                });

                // Read all unchecked checkboxes
                $.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
                    unchecked_arr.push(this.value);
                });

                // Hide the checked columns
                table.columns(checked_arr).visible(false);

                // Show the unchecked columns
                table.columns(unchecked_arr).visible(true);
                reloadDataTable();

            });
            // reload the datatable after save new grid
            function reloadDataTable() {
                table.destroy();
                // after destroy table we need to add new table
                $('.data-table').empty();
                $('.data-table').html('<thead><tr class="data-id-info"></tr></thead><tbody></tbody>');
                // set header information
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    $(".data-id-info").append('<th>'+$(this).attr('data-id')+'</th>');
                });
                setColumns();
                table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('users.index') }}",
                    columns: selected_arr,
                });

            }
        });

    </script>
</body>

</html>
