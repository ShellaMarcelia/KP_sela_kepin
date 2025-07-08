<style>
    #product-masuk {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #product-masuk td, #product-masuk th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #product-masuk tr:nth-child(even){background-color: #f2f2f2;}

    #product-masuk tr:hover {background-color: #ddd;}

    #product-masuk th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<table id="product-keluar" width="100%">
    <thead>
        <tr>
            <td>Kode Produk Keluar</td>
            <td>Nama Produk</td>
            <td>Nama Pelanggan</td>
            <td>QTY</td>
            <td>Tanggal Pembelian</td>
        </tr>
    </thead>
    @foreach($product_keluar as $pk)
        <tbody>
        <tr>
            <td>{{ $pk->kode_produk_keluar }}</td>
            <td>{{ $pk->product->nama }}</td>
            <td>{{ $pk->customer->nama }}</td>
            <td>{{ $pk->qty }}</td>
            <td>{{ $pk->tanggal }}</td>
        </tr>
        </tbody>
    @endforeach
</table>
