<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header img {
            width: 70px;
            height: auto;
        }

        .header .company-info {
            flex: 1;
            text-align: center;
        }

        .company-info h2 {
            margin: 0;
            font-size: 18px;
        }

        .company-info p {
            margin: 0;
            font-size: 12px;
        }

        .meta-info {
            margin-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table tr:nth-child(even) { background-color: #f2f2f2; }

        table tr:hover { background-color: #ddd; }

        table th {
            background-color: #4CAF50;
            color: white;
            text-align: left;
            padding-top: 12px;
            padding-bottom: 12px;
        }
    </style>
</head>
<body>

    <div class="header">
    <img src="{{ public_path('assets2/img/logo.png') }}" width="70" alt="Logo Perusahaan">
        <div class="company-info">
            <h2>TB Griya Bintang Surya</h2>
            <p>Jalan Depati Barin, Kelurahan Kenten, Sumatera Selatan.</p>
        </div>
    </div>

    <div class="meta-info">
        <strong>Periode:</strong> {{ $from ?? '-' }} s/d {{ $to ?? '-' }}<br>
        <strong>Tanggal Cetak:</strong> {{ $printed_date ?? now()->format('d M Y') }}
    </div>

    <table width="100%" border="1" cellspacing="0" cellpadding="4">
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
        @foreach($grouped as $group)
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



</body>
</html>
