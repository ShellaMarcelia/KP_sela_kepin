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

<table border="1" width="100%">
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>QTY</th>
            <th>QTY Total</th>
            <th>Nama Pelanggan</th>
            <th>Tanggal Pembelian</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $group)
            @foreach($group['items'] as $index => $pk)
                <tr>
                    @if($index === 0)
                        <td rowspan="{{ $group['rowspan'] }}">{{ $group['nama_produk'] }}</td>
                    @endif

                    <td>{{ $pk->qty }}</td>

                    @if($index === 0)
                        <td rowspan="{{ $group['rowspan'] }}">{{ $group['qty_total'] }}</td>
                    @endif

                    <td>{{ $pk->customer->nama }}</td>
                    <td>{{ $pk->tanggal }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
