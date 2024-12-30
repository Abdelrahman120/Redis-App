<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Orders</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Search Orders</h1>
    <div class="mb-3 text-center">
        <input type="text" id="search-input" class="form-control d-inline-block w-50" placeholder="Enter search term">
        <button id="search-btn" class="btn btn-primary mx-2">Search</button>
    </div>
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
<script>
$(document).ready(function () {
    let dataTable;

    $('#search-btn').on('click', function () {
        const searchQuery = $('#search-input').val().trim();

        if (!searchQuery) {
            alert('Please enter a search term.');
            return;
        }

        if (dataTable) {
            dataTable.destroy();
            $('#orders-table tbody').empty();
        }

        $('#table-container').removeClass('d-none');

        dataTable = $('#orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/get-search-orders',
                type: 'GET',
                data: { searchQuery: searchQuery },
            },
            columns: [
                { data: 'id' },
                { data: 'customer_name' },
                { data: 'products' },
                { data: 'total_quantity' },
                { data: 'created_at' }
            ],
            pageLength: 10,
        });
    });
});
</script>
</body>
</html>
