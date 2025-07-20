@extends('layouts.master')

@section('top')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Data Produk Keluar</h3>
        </div>
        <div class="box-header">
            @role('admin', 'staff')
            <a onclick="addForm()" class="btn btn-primary">Tambah Produk Keluar</a>
            @endrole
            @role('admin')
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-export">
                Export
            </button>
            @endrole
        </div>
        <div class="box-body">
            <table id="products-out-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Nama Pelanggan</th>
                        <th>QTY</th>
                        <th>Tanggal Pembelian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    @include('product_keluar.form')
<!-- Modal Export -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel">
    <div class="modal-dialog" role="document">
        <form action="" method="GET" target="_blank" id="form-export">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" id="modalExportLabel">Export Produk Keluar</h3>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="from_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="to_date" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" onclick="setExportRoute('pdf')">Export PDF</button>
                    <button type="submit" class="btn btn-success" onclick="setExportRoute('excel')">Export Excel</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('bot')
    <!-- DataTables -->
    <script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <!-- Date-range-picker -->
    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Validator -->
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    <script>
    $(function () {
        $('#tanggal').datepicker({ autoclose: true });

        var table = $('#products-out-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.productsOut') }}",
            columns: [
                {data: 'kode_produk_keluar', name: 'kode_produk_keluar'},
                {data: 'products_name', name: 'products_name'},
                {data: 'customer_name', name: 'customer_name'},
                {data: 'qty', name: 'qty'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        window.addForm = function() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#form-item')[0].reset();
            $('.modal-title').text('Tambah Produk Keluar');
        }


        window.deleteData = function(id) {
            let csrf_token = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: 'Apakah Anda Yakin?',
                text: "Data yang terhapus tidak bisa dikembalikan lagi!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Produk Keluar!'
            }).then(() => {
                $.post("{{ url('productsOut') }}/" + id, {_method: 'DELETE', _token: csrf_token}, function(data) {
                    table.ajax.reload();
                    swal('Produk Keluar Berhasil Terhapus!', data.message, 'success');
                }).fail(function() {
                    swal('Oops...', 'Something went wrong!', 'error');
                });
            });
        }

        $('#form-item').validator().on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                e.preventDefault();

                let id = $('#id').val();
                let url = save_method === 'add' ? "{{ url('productsOut') }}" : "{{ url('productsOut') }}/" + id;

                $.ajax({
                    url: url,
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        swal('Berhasil!', data.message, 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        swal('Gagal', 'Mohon Periksa QTY Produk', 'error');
                    }
                });
            }
        });
    });
</script>

<!-- cust -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const customerSearch = document.getElementById('customer_search');
    const suggestionsBox = document.getElementById('customer-suggestions');

    // Listen for input changes to trigger customer search
    customerSearch.addEventListener('input', function () {
        const query = customerSearch.value;

        if (query.length > 2) {
            fetch(`/search-customers?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear previous suggestions
                    suggestionsBox.innerHTML = '';

                    // If no cust are found, display a message
                    if (data.customers.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.classList.add('no-results');
                        noResults.textContent = 'Pelanggan Tidak Ditemukan';
                        suggestionsBox.appendChild(noResults);
                    } else {
                        // Show cust suggestions
                        data.customers.forEach(customer => {
                            const suggestionDiv = document.createElement('div');
                            suggestionDiv.classList.add('suggestion-item');
                            suggestionDiv.textContent = customer.nama;
                            suggestionDiv.dataset.customerId = customer.id;

                            // Append the suggestion to the suggestions box
                            suggestionsBox.appendChild(suggestionDiv);
                        });
                    }
                });
        } else {
            suggestionsBox.innerHTML = ''; // Clear suggestions if the input is short
        }
    });

    // Listen for clicking a suggestion to autofill the input
    suggestionsBox.addEventListener('click', function (event) {
        if (event.target.classList.contains('suggestion-item')) {
            const selectedCustomer = event.target;
            customerSearch.value = selectedCustomer.textContent;
            document.getElementById('customer_id').value = selectedCustomer.dataset.customerId;

            // Clear suggestions after selecting one
            suggestionsBox.innerHTML = '';
        }
    });
});

    </script>
    
    <!-- produk -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const productSearch = document.getElementById('product_search');
    const suggestionsBox = document.getElementById('product-suggestions');

    // Listen for input changes to trigger product search
    productSearch.addEventListener('input', function () {
        const query = productSearch.value;

        if (query.length > 2) {
            fetch(`/search-products?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear previous suggestions
                    suggestionsBox.innerHTML = '';

                    // If no products are found, display a message
                    if (data.products.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.classList.add('no-results');
                        noResults.textContent = 'Produk Tidak Ditemukan';
                        suggestionsBox.appendChild(noResults);
                    } else {
                        // Show product suggestions
                        data.products.forEach(product => {
                            const suggestionDiv = document.createElement('div');
                            suggestionDiv.classList.add('suggestion-item');
                            suggestionDiv.textContent = product.nama;
                            suggestionDiv.dataset.productId = product.id;

                            // Append the suggestion to the suggestions box
                            suggestionsBox.appendChild(suggestionDiv);
                        });
                    }
                });
        } else {
            suggestionsBox.innerHTML = ''; // Clear suggestions if the input is short
        }
    });

    // Listen for clicking a suggestion to autofill the input
    suggestionsBox.addEventListener('click', function (event) {
        if (event.target.classList.contains('suggestion-item')) {
            const selectedProduct = event.target;
            productSearch.value = selectedProduct.textContent;
            document.getElementById('product_id').value = selectedProduct.dataset.productId;
            document.getElementById('product_id_hidden').value = selectedProduct.dataset.productId; // <-- tambahkan ini

            // Clear suggestions after selecting one
            suggestionsBox.innerHTML = '';
        }
    });
});

    </script>

<script>
    function setExportRoute(type) {
        let form = document.getElementById('form-export');

        // Check which export type is selected
        if (type === 'pdf') {
            form.action = "{{ route('exportPDF.productKeluarAll') }}"; // Route for PDF
        } else if (type === 'excel') {
            form.action = "{{ route('exportExcel.productKeluarAll') }}"; // Route for Excel
        }

        // Debugging line: Log the action to see if it's set correctly
        console.log('Form action set to:', form.action);

        // Submit the form after setting the action
        form.submit();
    }


</script>
@endsection
