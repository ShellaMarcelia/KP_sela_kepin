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
            <h3 class="box-title">Data Produk Masuk</h3>
        </div>
        <div class="box-header">
        @role('admin')
            <a onclick="addForm()" class="btn btn-primary">Tambah Produk Masuk</a>
            @endrole
            @role('admin','manajer')
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-export">
                Export
            </button>
            @endrole
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="products-in-table" class="table table-striped">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Nama Supplier</th>
                    <th>QTY</th>
                    <th>Tanggal Masuk</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>


    @include('product_masuk.form')
<!-- Modal Export -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel">
    <div class="modal-dialog" role="document">
        <form action="" method="GET" target="_blank" id="form-export">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" id="modalExportLabel">Export Produk Masuk</h3>
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
    <script src=" {{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }} "></script>


    <!-- InputMask -->
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    
    <!-- date-range-picker -->
    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
   
    <!-- bootstrap datepicker -->
    <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
   
    <!-- bootstrap color picker -->
    <script src="{{ asset('assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
   
    <!-- bootstrap time picker -->
    <script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
   
    {{-- Validator --}}
    <script src="{{ asset('assets/validator/validator.min.js') }}"></script>

    {{--<script>--}}
    {{--$(function () {--}}
    {{--$('#items-table').DataTable()--}}
    {{--$('#example2').DataTable({--}}
    {{--'paging'      : true,--}}
    {{--'lengthChange': false,--}}
    {{--'searching'   : false,--}}
    {{--'ordering'    : true,--}}
    {{--'info'        : true,--}}
    {{--'autoWidth'   : false--}}
    {{--})--}}
    {{--})--}}
    {{--</script>--}}

    <script>
        $(function () {

            //Date picker
            $('#tanggal').datepicker({
                autoclose: true,
                // dateFormat: 'yyyy-mm-dd'
            })

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            //Timepicker
            $('.timepicker').timepicker({
                showInputs: false
            })
        })
    </script>

    <script type="text/javascript">
        var table = $('#products-in-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.productsIn') }}",
            columns: [
                {data: 'kode_produk_masuk', name: 'kode_produk_masuk'},
                {data: 'products_name', name: 'products_name'},
                {data: 'supplier_name', name: 'supplier_name'},
                {data: 'qty', name: 'qty'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Tambah Produk Masuk');
        }

        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            $.ajax({
                url: "{{ url('productsIn') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit Produk Masuk');

                    $('#id').val(data.id);
                    $('#product_id').val(data.product_id);
                    $('#supplier_id').val(data.supplier_id);
                    $('#qty').val(data.qty);
                    $('#tanggal').val(data.tanggal);
                },
                error : function() {
                    alert("Data Tidak Ditemukan");
                }
            });
        }

        function deleteData(id){
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: 'Apakah anda yakin?',
                text: "Data yang terhapus tidak bisa dikembalikan lagi!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Produk Masuk!'
            }).then(function () {
                $.ajax({
                    url : "{{ url('productsIn') }}" + '/' + id,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success : function(data) {
                        table.ajax.reload();
                        swal({
                            title: 'Produk Masuk Berhasil Terhapus!',
                            text: data.message,
                            type: 'success',
                        })
                    },
                    error : function () {
                        swal({
                            title: 'Gagal Menghapus Produk Masuk',
                            text: data.message,
                            type: 'error',
                        })
                    }
                });
            });
        }

        $(function(){
    $('#modal-form form').validator().on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            let id = $('#id').val();
            let url;

            if (save_method == 'add') {
                url = "{{ url('productsIn') }}";
            } else {
                url = "{{ url('productsIn') . '/' }}" + id;
            }

            // ✅ Bersihkan baris kosong (jika product_id atau qty tidak diisi)
            $('#product-fields-container .product-row').each(function () {
                let productId = $(this).find('input[name="product_id[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();

                if (!productId || !qty || parseInt(qty) <= 0) {
                    $(this).remove(); // hapus baris tidak valid
                }
            });

            // ✅ Validasi ulang setelah dibersihkan
            const validProductIds = $('input[name="product_id[]"]').map(function () {
                return $(this).val();
            }).get();

            const validQtys = $('input[name="qty[]"]').map(function () {
                return $(this).val();
            }).get();

            if (validProductIds.length === 0 || validQtys.includes('') || validQtys.some(q => parseInt(q) <= 0)) {
                swal({
                    title: 'Gagal!',
                    text: 'Pastikan semua produk dan jumlah (qty) diisi dengan benar.',
                    type: 'error',
                });
                return false;
            }

            // ✅ Kirim data
            $.ajax({
                url: url,
                type: "POST",
                data: new FormData($("#modal-form form")[0]),
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();
                    swal({
                        title: 'Produk Masuk Berhasil Disimpan!',
                        text: data.message,
                        type: 'success',
                    });
                },
                error: function (data) {
                    let errMsg = data.responseJSON?.message || "Terjadi kesalahan saat mengirim data.";
                    swal({
                        title: 'Produk Masuk Gagal Tersimpan',
                        text: errMsg,
                        type: 'error',
                    });
                }
            });

            return false;
        }
    });
});

    </script>


<!-- supplier -->
<script>
        document.addEventListener('DOMContentLoaded', function () {
    const supplierSearch = document.getElementById('supplier_search');
    const suggestionsBox = document.getElementById('supplier-suggestions');

    // Listen for input changes to trigger supplier search
    supplierSearch.addEventListener('input', function () {
        const query = supplierSearch.value;

        if (query.length > 2) {
            fetch(`/search-suppliers?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear previous suggestions
                    suggestionsBox.innerHTML = '';

                    // If no supplier are found, display a message
                    if (data.suppliers.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.classList.add('no-results');
                        noResults.textContent = 'Supplier Tidak Ditemukan';
                        suggestionsBox.appendChild(noResults);
                    } else {
                        // Show supplier suggestions
                        data.suppliers.forEach(supplier => {
                            const suggestionDiv = document.createElement('div');
                            suggestionDiv.classList.add('suggestion-item');
                            suggestionDiv.textContent = supplier.nama;
                            suggestionDiv.dataset.supplierId = supplier.id;

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
            const selectedSupplier = event.target;
            supplierSearch.value = selectedSupplier.textContent;
            document.getElementById('supplier_id').value = selectedSupplier.dataset.supplierId;

            // Clear suggestions after selecting one
            suggestionsBox.innerHTML = '';
        }
    });
});

    </script>
    
    <!-- produk -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('product-fields-container');

    // AUTOCOMPLETE INPUT PRODUK
    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('product-search')) {
            const input = e.target;
            const query = input.value;
            const parentGroup = input.closest('.form-group');
            const suggestionsBox = parentGroup.querySelector('.product-suggestions');

            if (query.length > 2) {
                fetch(`/search-products?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data.products.length === 0) {
                            suggestionsBox.innerHTML = '<div class="no-results">Produk Tidak Ditemukan</div>';
                        } else {
                            data.products.forEach(product => {
                                const div = document.createElement('div');
                                div.classList.add('suggestion-item');
                                div.textContent = `${product.nama}`;
                                div.dataset.productId = product.id;
                                suggestionsBox.appendChild(div);
                            });
                        }
                    })
                    .catch(() => {
                        suggestionsBox.innerHTML = '<div class="no-results">Gagal Mencari Data</div>';
                    });
            } else {
                suggestionsBox.innerHTML = '';
            }
        }
    });

    // SAAT KLIK ITEM PRODUK DARI SUGGESTION
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('suggestion-item')) {
            const selected = e.target;
            const row = selected.closest('.product-row');
            const input = row.querySelector('.product-search');
            const hiddenKode = row.querySelector('.product-id-hidden');
            const hiddenId = row.querySelector('.product-id');

            const productId = selected.dataset.productId;

            fetch(`/product-keluar/product/${productId}`)
                .then(res => res.json())
                .then(data => {
                    input.value = data.nama;
                    hiddenId.value = data.id;
                    hiddenKode.value = data.kode_produk;
                    selected.closest('.product-suggestions').innerHTML = '';
                })
                .catch(() => {
                    selected.closest('.product-suggestions').innerHTML = '<div class="no-results">Gagal Memuat Data Produk</div>';
                });
        }
    });
});


    </script>

<script>
    function setExportRoute(type) {
        let form = document.getElementById('form-export');

        // Check which export type is selected
        if (type === 'pdf') {
            form.action = "{{ route('exportPDF.productMasukAll') }}"; // Route for PDF
        } else if (type === 'excel') {
            form.action = "{{ route('exportExcel.productMasukAll') }}"; // Route for Excel
        }

        // Debugging line: Log the action to see if it's set correctly
        console.log('Form action set to:', form.action);

        // Submit the form after setting the action
        form.submit();
    }


</script>

@endsection

