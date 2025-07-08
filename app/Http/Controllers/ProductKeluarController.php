<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Exports\ExportProdukKeluar;
use App\Product;
use App\Product_Keluar;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;
use Excel;


class ProductKeluarController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,staff,manajer');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productsQuery = Product::orderBy('nama', 'ASC');

        // Check if there's a search query for products
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $productsQuery->where('nama', 'like', "%{$searchTerm}%");
        }

        $products = $productsQuery->get();

        $customers = Customer::orderBy('nama', 'ASC')->get()->pluck('nama', 'id');
        $invoice_data = Product_Keluar::all();

        return view('product_keluar.index', compact('products', 'customers', 'invoice_data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        \Log::info('DATA DITERIMA', $request->all());

        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'tanggal' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
        ]);
        
        

        foreach ($request->product_id as $key => $productId) {
            $product = Product::findOrFail($productId);
            $quantity = (int) $request->qty[$key];

            if ($product->qty < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi untuk ' . $product->nama,
                ], 400);
            }

            /* ==== BUAT KODE OTOMATIS ==== */
            /* === sebelum create() di dalam foreach === */
            $last = Product_Keluar::orderBy('kode_produk_keluar', 'desc')->first();

            if ($last && preg_match('/PM(\d{4})/', $last->kode_produk_keluar, $match)) {
                $lastNumber = (int) $match[1];
            } else {
                $lastNumber = 0;
            }

            $newNumber = $lastNumber + 1;
            $kode_produk_keluar = 'PM' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);


            /* === parameter create() === */
            Product_Keluar::create([
                'product_id'         => $product->id,
                'customer_id'        => $request->customer_id,
                'qty'                => $quantity,
                'tanggal'            => $request->tanggal,
                'kode_produk_keluar' => $kode_produk_keluar,          // ⬅️ tambah kolom ini
            ]);

            // Kurangi stok produk
            $product->qty -= $quantity;
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua produk berhasil disimpan.',
        ]);

       

    }



    




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product_keluar = Product_Keluar::find($id);
        return $product_keluar;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $this->validate($request, [
        'product_id'     => 'required',
        'customer_id'    => 'required',
        'qty'            => 'required|integer|gt:0',
        'tanggal'        => 'required|date',
    ]);

    $product_keluar = Product_Keluar::findOrFail($id);
    $product = Product::findOrFail($request->product_id);

    // Check if the requested quantity is greater than the available stock
    if ($product->qty < $request->qty) {
        return response()->json([
            'success' => false,
            'message' => 'Stok Tidak Mencukupi!'
        ]);
    }

    // Update Product Keluar entry
    $product_keluar->update($request->all());

    // Reduce the stock after the update
    $product->qty -= $request->qty;
    $product->update();

    return response()->json([
        'success'    => true,
        'message'    => 'Produk Keluar Diperbarui'
    ]);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product_Keluar::destroy($id);

        return response()->json([
            'success'    => true,
            'message'    => ''
        ]);
    }

    // Controller
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('nama', 'like', "%{$query}%")
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'kode_produk' => $product->kode_produk,
                ];
            });

        return response()->json(['products' => $products]);
    }





    public function searchCustomers(Request $request)
    {
        $query = $request->input('query');

        $customers = Customer::where('nama', 'like', "%{$query}%")
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'nama' => $customer->nama,
                    'kode_customer' => 'C' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                ];
            });

        return response()->json(['customers' => $customers]);
    }




    

    public function apiProductsOut(){
    $product = Product_Keluar::all();

    return Datatables::of($product)
        ->addColumn('kode_produk_keluar', function ($product) {
            return $product->kode_produk_keluar;   // ambil langsung dari kolom di DB
        })
        ->addColumn('products_name', function ($product){
            return $product->product->nama; // hanya nama produk saja
        })
        ->addColumn('customer_name', function ($product){
            return $product->customer->nama; // hanya nama customer saja
        })            
        ->addColumn('action', function($product){
            if (auth()->user()->role === 'admin') {
            return 
                '<a onclick="deleteData('. $product->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Hapus</a>';
            }
            return '';
        })
        ->rawColumns(['products_name','customer_name','action'])->make(true);
}

    public function exportProductKeluarAll(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $printed_date = now()->format('d M Y');

        // Filter the data based on the selected dates
        $productsOut = Product_Keluar::whereBetween('tanggal', [$from, $to])->get();

        return PDF::loadView('product_keluar.productKeluarAllPDF', compact('productsOut', 'from', 'to', 'printed_date'))
            ->download('product_keluar_' . now()->format('Ymd_His') . '.pdf');
    }




        public function exportExcelProductKeluar(Request $request)
        {
            $from = $request->input('from_date');
            $to = $request->input('to_date');

            return (new ExportProdukKeluar($from, $to))->download('product_keluar_' . now()->format('Ymd_His') . '.xlsx');
        }

    



    public function getCustomerById($id)
    {
        $customer = Customer::findOrFail($id);
    
        return response()->json([
            'id' => $customer->id,
            'nama' => $customer->nama,
            'kode_customer' => $customer->kode_customer,
        ]);
    }
    public function getProductById($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'nama' => $product->nama,
            'kode_produk' => $product->kode_produk
        ]);
    }

    

}
