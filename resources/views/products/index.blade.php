@extends('layouts.master')


@section('top')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Data Produk</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        @role('admin')
        <a onclick="addForm()" class="btn btn-primary">Tambah Produk</a>
        @endrole

        @role('admin')
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-export">
            Export
        </button>
        @endrole
<br></br>
        

            <table id="products-table" class="table table-striped" style="margin-top: 10px;">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>

                    @role('admin')
                    <th>Aksi</th>
                    @endrole

                </tr>
                </thead>
                <tbody></tbody>
            </table>
            
        </div>
        <!-- /.box-body -->
    </div>

    @include('products.form')

<!-- modal -->
<!-- modal -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel">
  <div class="modal-dialog" role="document">
    <form action="" method="GET" target="_blank" id="form-export">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title" id="modalExportLabel">Export Data Produk</h3>
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

    <script type="text/javascript">
        var table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.products') }}",
            columns: [
                {data: 'kode_produk', name: 'kode_produk'},
                {data: 'nama', name: 'nama'},
                {data: 'harga', name: 'harga'},
                {data: 'qty', name: 'qty'},
                @role('admin')
                {data: 'action', name: 'action', orderable: false, searchable: false}
                @endrole
            ]
        });

        function addForm() {
            save_method = "add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Tambah Produk');
        }

        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            $.ajax({
                url: "{{ url('products') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);  // Menampilkan data ke konsol untuk pengecekan
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit Produk');
                    $('#id').val(data.id);
                    $('#nama').val(data.nama);
                    $('#harga').val(data.harga);
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
                confirmButtonText: 'Ya, Hapus Produk!'
            }).then(function () {
                $.ajax({
                    url : "{{ url('products') }}" + '/' + id,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success : function(data) {
                        table.ajax.reload();
                        swal({
                            title: 'Produk Berhasil Terhapus!',
                            text: data.message,
                            type: 'success',
                            // timer: '1500'
                        })
                    },
                    error : function () {
                        swal({
                            title: 'Gagal Menghapus Produk',
                            text: data.message,
                            type: 'error',
                            timer: '1500'
                        })
                    }
                });
            });
        }

        $(function(){
            $('#modal-form form').validator().on('submit', function (e) {
                if (!e.isDefaultPrevented()){
                    var id = $('#id').val();
                    if (save_method == 'add') url = "{{ url('products') }}";
                    else url = "{{ url('products') . '/' }}" + id;

                    $.ajax({
                        url : url,
                        type : "POST",
                        //hanya untuk input data tanpa dokumen
                        data : $('#modal-form form').serialize(),
                        data: new FormData($("#modal-form form")[0]),
                        contentType: false,
                        processData: false,
                        success : function(data) {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                            swal({
                                title: 'Produk Berhasil Disimpan!',
                                text: data.message,
                                type: 'success',
                                timer: '1500'
                            })
                        },
                        error : function(data){
                            swal({
                                title: 'Gagal Menyimpan',
                                text: 'Periksa kembali isi data',
                                type: 'error',
                                timer: '1500'
                            })
                        }
                    });
                    return false;
                }
            });
        });
    </script>
<script>
    function setExportRoute(type) {
        let form = document.getElementById('form-export');
        if (type === 'pdf') {
            form.action = "{{ route('exportPDF.productsAll') }}";
        } else if (type === 'excel') {
            form.action = "{{ route('exportExcel.productsAll') }}";
        }
    }
</script>

@endsection
