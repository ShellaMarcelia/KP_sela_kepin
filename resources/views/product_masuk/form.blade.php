<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Tambah Produk Masuk</h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Kode</label>
                                    <input type="text" class="form-control" id="supplier_kode" name="supplier_kode" readonly>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-md-10">
                            <div class="form-group" style="position: relative;">
                                <label>Nama Supplier</label>
                                <div style="position: relative;">
                                    <input type="text" class="form-control" id="supplier_search" name="supplier_search" placeholder="Nama Supplier" autocomplete="off" required>
                                    <div id="supplier-suggestions" class="suggestions-box"></div>
                                </div>
                                <input type="hidden" id="supplier_id" name="supplier_id">
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                        </div>

                        <!-- Product Search -->
                        <div id="product-fields-container">
                            <div class="row product-row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label-product">Kode</label>
                                        <input type="text" class="form-control product-id-hidden" name="product_id_hidden[]" readonly>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label-product">Nama Produk</label>
                                        <div style="position: relative;">
                                            <input type="text" class="form-control product-search" name="product_search[]" placeholder="Nama Produk" autocomplete="off" required>
                                            <div class="suggestions-box product-suggestions"></div>
                                        </div>
                                        <input type="hidden" class="product-id" name="product_id[]">
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-4">
                                    <label class="form-label-product">Quantity</label>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <input type="number" class="form-control qty-input" name="qty[]" min="1" required style="width: 100px;">
                                        <div class="button-group d-flex gap-1">
                                            <button class="btn btn-primary add-product-btn" type="button">
                                                Tambah
                                            </button>
                                            <button class="btn btn-danger remove-product-btn" type="button">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Field -->
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input data-date-format='yyyy-mm-dd' type="text" class="form-control" id="tanggal" name="tanggal" required>
                            <span class="help-block with-errors"></span>
                        </div>

                    </div>
                    <!-- /.box-body -->

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
.product-search {
    position: relative; /* Penting untuk mengatur posisi suggestions-box */
}

/* Styling untuk kotak saran produk */
.suggestions-box {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: white;
    border: 1px solid #ccc;
    max-height: 200px;  /* Membatasi tinggi kotak saran */
    overflow-y: auto;   /* Memungkinkan scroll vertikal */
    z-index: 9999;      /* Pastikan kotak saran berada di atas elemen lainnya */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Memberikan bayangan kotak */
    border-radius: 0 0 5px 5px; /* Membulatkan sudut */
    margin-top: 0px;
}

/* Styling untuk setiap item saran */
.suggestion-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #f1f1f1;
}

/* Efek hover */
.suggestion-item:hover {
    background-color: #f0f0f0;
}

/* Efek saat item dipilih (active) */
.suggestion-item:active {
    background-color: #e0e0e0;
}

/* Jika tidak ada hasil pencarian */
.suggestions-box .no-results {
    padding: 10px;
    color: #999;
    font-style: italic;
    text-align: center;
}
</style>

<!-- supplier -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const supplierSearch = document.getElementById('supplier_search');
    const suggestionsBox = document.getElementById('supplier-suggestions');

    // Listen for input changes to trigger supplier search
    supplierSearch.addEventListener('input', function () {
        const query = supplierSearch.value;

        if (query.length > 2) {
            fetch(`/search-suppliers?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';

                    if (!data.suppliers || data.suppliers.length === 0) {
                        suggestionsBox.innerHTML = '<div class="no-results">Supplier Tidak Ditemukan</div>';
                        return;
                    }

                    data.suppliers.forEach(supplier => {
                        const suggestionDiv = document.createElement('div');
                        suggestionDiv.classList.add('suggestion-item');
                        suggestionDiv.textContent = `${supplier.nama}`;
                        suggestionDiv.dataset.supplierId = supplier.id;
                        suggestionsBox.appendChild(suggestionDiv);
                    });
                })
                .catch(error => {
                    suggestionsBox.innerHTML = '<div class="no-results">Gagal Memuat Data Supplier</div>';
                });
        } else {
            suggestionsBox.innerHTML = '';
        }
    });

    // Listen for clicking a suggestion to autofill the input
    suggestionsBox.addEventListener('click', function (event) {
        if (event.target.classList.contains('suggestion-item')) {
            const selectedSupplier = event.target;
            const supplierId =selectedSupplier.dataset.supplierId;

            fetch(`/productsIn/supplier/${supplierId}`)
                .then(async res => {
                    const contentType = res.headers.get("content-type");
                    const status = res.status;

                    if (!res.ok) {
                        throw new Error(`HTTP Error ${status}`);
                    }

                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await res.text();  // tampilkan HTML kalau bukan JSON
                        console.error("Received non-JSON response:", text);
                        throw new Error("Invalid JSON response");
                    }

                    return res.json();
                })
                .then(data => {
                    console.log("Received supplier data:", data);
                    supplierSearch.value = `${data.nama}`;
                    document.getElementById('supplier_id').value = data.id;
                    document.getElementById('supplier_kode').value = data.kode_supplier;
                    suggestionsBox.innerHTML = '';
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    suggestionsBox.innerHTML = '<div class="no-results">Gagal Memuat Data Supplier</div>';
                });


        }
    });
});
</script>

<!-- produk -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('product-fields-container');

    // Fungsi: atur visibilitas tombol + dan -
    function updateButtonVisibility() {
        const rows = container.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            row.querySelector('.add-product-btn').style.display = index === rows.length - 1 ? 'inline-block' : 'none';
            row.querySelector('.remove-product-btn').style.display = index === rows.length - 1 ? 'none' : 'inline-block';
        });
    }

    // Fungsi: hanya tampilkan label di baris pertama
    function updateLabelVisibility() {
        const rows = container.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            const labels = row.querySelectorAll('.form-label-product');
            labels.forEach(label => {
                label.style.display = index === 0 ? 'block' : 'none';
            });
        });
    }

    // Tambah baris
    container.addEventListener('click', function (e) {
        if (e.target.closest('.add-product-btn')) {
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);

            // Bersihkan input dan suggestion
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            newRow.querySelectorAll('.suggestions-box').forEach(box => box.innerHTML = '');
            newRow.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));

            container.appendChild(newRow);

            updateButtonVisibility();
            updateLabelVisibility();
        }

        // Hapus baris
        if (e.target.closest('.remove-product-btn')) {
            const row = e.target.closest('.product-row');
            
            if (container.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateButtonVisibility();
                updateLabelVisibility();
            } else {
                alert("Minimal satu baris produk harus ada.");
            }
        }
    });

    // Autocomplete produk
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
                        suggestionsBox.innerHTML = '<div class="no-results">Gagal Memuat Data Produk</div>';
                    });
            } else {
                suggestionsBox.innerHTML = '';
            }
        }
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('suggestion-item')) {
            const selected = e.target;
            const row = selected.closest('.product-row');
            const input = row.querySelector('.product-search');
            const hiddenKode = row.querySelector('.product-id-hidden');
            const hiddenId = row.querySelector('.product-id');

            const productId = selected.dataset.productId;

            fetch(`/productsIn/product/${productId}`)
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

    // Inisialisasi awal
    updateButtonVisibility();
    updateLabelVisibility();
});
</script>

