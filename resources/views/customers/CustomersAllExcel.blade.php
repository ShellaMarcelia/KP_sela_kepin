<style>
    #categories {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td, #categories th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even){background-color: #f2f2f2;}

    #categories tr:hover {background-color: #ddd;}

    #categories th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<table id="categories" width="100%">
    <thead>
    <tr>
        <td>Kode Pelanggan</td>
        <td>Nama</td>
        <td>Alamat</td>
        <td>Email</td>
    </tr>
    </thead>
    @foreach($customers as $c)
        <tbody>
        <tr>
            <td>{{ $c->kode_customer }}</td> 
            <td>{{ $c->nama }}</td>
            <td>{{ $c->alamat }}</td>
            <td>{{ $c->email }}</td>
        </tr>
        </tbody>
    @endforeach

</table>


