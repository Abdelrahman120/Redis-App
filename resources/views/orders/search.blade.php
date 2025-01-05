<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Search Orders</h1>
    <div class="mb-3 text-center">
        <!-- Search input field for customer -->
        <select id="search-input" class="form-control d-inline-block w-50">
            <option value="">Select Customer</option>
        </select>
        <button id="search-btn" class="btn btn-primary mx-2">Search</button>
    </div>
    <!-- Table container -->
    <div class="table-responsive d-none" id="table-container">
        <table id="orders-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Products</th>
                    <th>Total Quantity</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#search-input').select2({
        ajax: {
            url: '/get-customers',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (customer) {
                        return {
                            id: customer.text, 
                            text: customer.text,
                        };
                    }),
                };
            },
            cache: true,
        },
        placeholder: 'Search for a customer...',
        minimumInputLength: 1,
    });

    let dataTable;

    $('#search-btn').on('click', function () {
        const customerName = $('#search-input').val();

        if (!customerName) {
            alert('Please select a customer.');
            return;
        }

        if (dataTable) {
            dataTable.destroy();  
            $('#orders-table tbody').empty();  
        }

        $('#table-container').removeClass('d-none');

        $.ajax({
            url: '/get-search-orders',
            type: 'GET',
            data: { customerName: customerName },
            success: function (response) {
                dataTable = $('#orders-table').DataTable({
                    data: response.data,
                    columns: [
                        { data: 'id' },
                        { data: 'customer_name' },
                        { data: 'products' },
                        { data: 'total_quantity' },
                        { data: 'created_at' },
                    ],
                    pageLength: 10, 
                    searching: true, 
                    ordering: true,
                    paging: true, 
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            },
        });
    });
});

</script>
</body>
</html>
