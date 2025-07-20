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
        $this->middleware('role:admin,staff');
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id'    => 'required|exists:suppliers,id',
            'tanggal'        => 'required|date',
            'product_id'     => 'required|array|min:1', 
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
        ]);

        foreach ($request->product_id as $key => $productId) {
            $product = Product::findOrFail($productId);
            $quantity = (int) $request->qty[$key];

            $last = Product_Masuk::orderBy('kode_produk_masuk', 'desc')->first();

            if ($last && preg_match('/PM(\d{4})/', $last->kode_produk_masuk, $match)) {
                $lastNumber = (int) $match[1];
            } else {
                $lastNumber = 0;
            }

            $newNumber = $lastNumber + 1;
            $kode_produk_masuk = 'PM' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            Product_Masuk::create([
                'product_id'  => $product->id,
                'supplier_id' => $request->supplier_id,
                'qty'         => $quantity,
                'tanggal'     => $request->tanggal,
                'kode_produk_masuk'   => $kode_produk_masuk,
            ]);

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productMasuk = Product_Masuk::findOrFail($id);
        $product = Product::findOrFail($productMasuk->product_id);
        $product->qty -= $productMasuk->qty;
        $product->save();
        $productMasuk->delete();

        return response()->json([
            'success'    => true,
            'message'    => ''
        ]);
    }
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('nama', 'like', "%{$query}%")
                            ->orderBy('nama', 'asc')
                            ->get(['id', 'nama']); 
        return response()->json(['products' => $products]);
    }



    public function searchSuppliers(Request $request)
    {
        $query = $request->input('query');
        $suppliers = Supplier::where('nama', 'like', "%{$query}%")
                            ->orderBy('nama', 'asc')
                            ->get(['id', 'nama']); 
        
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

        // public function exportProductMasukAll(Request $request)
        // {
        //     $from = $request->input('from_date');
        //     $to = $request->input('to_date');
        //     $printed_date = now()->format('d M Y');
        //     $product_masuk = Product_Masuk::whereBetween('tanggal', [$from, $to])->get();
    
        //     return PDF::loadView('product_masuk.productMasukAllPDF', compact('product_masuk', 'from', 'to', 'printed_date'))
        //         ->download('product_masuk_' . now()->format('Ymd_His') . '.pdf');
        // }
        
        public function exportProductMasukAll(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('to_date');
    $printed_date = now()->format('d M Y');

    $grouped = Product_Masuk::with(['product', 'supplier'])
        ->whereBetween('tanggal', [$from, $to])
        ->get()
        ->groupBy('product_id');

    $data = [];

    foreach ($grouped as $productId => $items) {
        $qty_total = $items->sum('qty');
        $rows = [];

        foreach ($items as $item) {
            $rows[] = [
                'qty' => $item->qty,
                'tanggal' => $item->tanggal,
                'supplier' => $item->supplier->nama,
            ];
        }

        $data[] = [
            'nama_produk' => $items->first()->product->nama,
            'qty_total' => $qty_total,
            'items' => $rows,
            'rowspan' => count($rows)
        ];
    }

    return PDF::loadView('product_masuk.productMasukAllPDF', [
        'data' => $data,
        'from' => $from,
        'to' => $to,
        'printed_date' => $printed_date
    ])->download('product_masuk_' . now()->format('Ymd_His') . '.pdf');
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
