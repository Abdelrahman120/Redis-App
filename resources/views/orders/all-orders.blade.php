<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">All Orders</h1>
    <div class="table-responsive">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/get-orders-data',
            type: 'GET',
        },
        columns: [
            { data: 'id', title: 'ID' },
            { data: 'customer_name', title: 'Customer Name' },
            { data: 'products', title: 'Products' },
            { data: 'total_quantity', title: 'Total Quantity' },
            { data: 'created_at', title: 'Created At' }
        ],
        pageLength: 10,
        language: {
            emptyTable: "No orders available",
            loadingRecords: "Loading...",
        },
    });
});
</script>
</body>
</html>
