<?php

namespace App\Http\Controllers;


use App\Exports\ExportProdukMasuk;
use App\Product;
use App\Product_Masuk;
use App\Supplier;
use PDF;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class ProductMasukController extends Controller
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

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $productsQuery->where('nama', 'like', "%{$searchTerm}%");
        }
    
        $products = $productsQuery->get();
    
        $suppliers = Supplier::orderBy('nama', 'ASC')->get()->pluck('nama', 'id');
        $invoice_data = Product_Masuk::all();
        return view('product_masuk.index', compact('products','suppliers','invoice_data'));
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
        
        // Validate the input arrays
        $this->validate($request, [
            'supplier_id'    => 'required|exists:suppliers,id',
            'tanggal'        => 'required|date',
            'product_id'     => 'required|array|min:1', // Array for product IDs
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
        ]);

        // Loop through the products and quantities arrays
        foreach ($request->product_id as $key => $productId) {
            $product = Product::findOrFail($productId);
            $quantity = (int) $request->qty[$key];

            /// Ambil kode terakhir
            $last = Product_Masuk::orderBy('kode_produk_masuk', 'desc')->first();

            if ($last && preg_match('/PM(\d{4})/', $last->kode_produk_masuk, $match)) {
                $lastNumber = (int) $match[1];
            } else {
                $lastNumber = 0;
            }

            $newNumber = $lastNumber + 1;
            $kode_produk_masuk = 'PM' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);


            // Create the Product Masuk entry
            Product_Masuk::create([
                'product_id'  => $product->id,
                'supplier_id' => $request->supplier_id,
                'qty'         => $quantity,
                'tanggal'     => $request->tanggal,
                'kode_produk_masuk'   => $kode_produk_masuk,
            ]);

            // Increase the stock after receiving products (Product Masuk = stock increases)
            $product->qty += $quantity;
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua produk masuk berhasil disimpan.'
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
            'supplier_id'    => 'required',
            'qty'            => 'required|integer|gt:0',
            'tanggal'        => 'required|date',
        ]);

        $product_masuk = Product_Masuk::findOrFail($id);
        $product = Product::findOrFail($request->product_id);

        // Check if the requested quantity is greater than the available stock
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the Product_Masuk entry to be deleted
        $productMasuk = Product_Masuk::findOrFail($id);
        
        // Find the associated product
        $product = Product::findOrFail($productMasuk->product_id);
        
        // Decrease the product quantity by the quantity of the deleted entry
        // (because we're removing a "product masuk" record, stock should decrease)
        $product->qty -= $productMasuk->qty;
        $product->save();
        
        // Now delete the Product_Masuk entry
        $productMasuk->delete();

        return response()->json([
            'success'    => true,
            'message'    => ''
        ]);
    }
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');

        // Ambil produk yang sesuai dengan query dan batasi hasil menjadi 5
        $products = Product::where('nama', 'like', "%{$query}%")
                            ->orderBy('nama', 'asc')
                            ->get(['id', 'nama']); // Mengambil hanya id dan nama produk
        
        return response()->json(['products' => $products]);
    }



    public function searchSuppliers(Request $request)
    {
        $query = $request->input('query');

        // Ambil supplier yang sesuai dengan query dan batasi hasil menjadi 5
        $suppliers = Supplier::where('nama', 'like', "%{$query}%")
                            ->orderBy('nama', 'asc')
                            ->get(['id', 'nama']); // Mengambil hanya id dan nama supplier
        
        return response()->json(['suppliers' => $suppliers]);
    }




    public function apiProductsIn(){
        $product = Product_Masuk::all();

        return Datatables::of($product)
        ->addColumn('kode_produk_masuk', function($product) {
            return $product->kode_produk_masuk;
        })
        
            ->addColumn('products_name', function ($product){
                return $product->product->nama;
            })
            ->addColumn('supplier_name', function ($product){
                return $product->supplier->nama;
            })
            ->addColumn('action', function($product){
                if (auth()->user()->role === 'admin') {
                return 
                    '<a onclick="deleteData('. $product->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Hapus</a> ';
                }
                return '';

            })
            ->rawColumns(['products_name','supplier_name','action'])->make(true);

    }

        public function exportProductMasukAll(Request $request)
        {
            $from = $request->input('from_date');
            $to = $request->input('to_date');
            $printed_date = now()->format('d M Y');
    
            // Filter the data based on the selected dates
            $product_masuk = Product_Masuk::whereBetween('tanggal', [$from, $to])->get();
    
            return PDF::loadView('product_masuk.productMasukAllPDF', compact('product_masuk', 'from', 'to', 'printed_date'))
                ->download('product_masuk_' . now()->format('Ymd_His') . '.pdf');
        }
        
        

        public function exportExcel(Request $request)
        {
            $from = $request->input('from_date');
            $to = $request->input('to_date');

            return (new ExportProdukMasuk($from, $to))->download('product_masuk_' . now()->format('Ymd_His') . '.xlsx');
        }


        public function getSupplierById($id)
        {
            $supplier = Supplier::findOrFail($id);

            return response()->json([
                'id' => $supplier->id,
                'nama' => $supplier->nama,
                'kode_supplier' => $supplier->kode_supplier,
            ]);
        }


        public function getProductById($id)
        {
            $product = Product::findOrFail($id);

            return response()->json([
                'id' => $product->id,
                'nama' => $product->nama,
                'kode_produk' => $product->kode_produk,
            ]);
        }


    }
