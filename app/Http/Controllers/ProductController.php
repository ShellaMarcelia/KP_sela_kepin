<?php

namespace App\Http\Controllers;

use App\Product;
use App\Exports\ExportProducts;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\PriceHelper;
use Excel;
use PDF;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,staff')->only(['index', 'apiProducts']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $producs = Product::all();
        return view('products.index');
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
        $this->validate($request , [
            'nama'  => 'required|string',
            'harga' => 'required|min:3',
        ]);
    
        $lastProduct = Product::orderBy('kode_produk', 'desc')->first();

        if ($lastProduct && preg_match('/P(\d{4})/', $lastProduct->kode_produk, $matches)) {
            $lastNumber = (int) $matches[1];
        } else {
            $lastNumber = 0;
        }
    
        $newNumber = $lastNumber + 1;
        $kode = 'P' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); 
    
        $input = $request->all();
        $input['kode_produk'] = $kode;
    
        Product::create($input);
    
        return response()->json([
            'success' => true,
            'message' => ''
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
    public function edit($id)
    {
        $product = Product::find($id);
        return $product;
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
            'nama'  => 'required|string',
            'harga' => 'required|min:4',
        ]);

        $produk = Product::findOrFail($id);
        $produk->nama = $request->nama;
        $produk->harga = $request->harga;
        $produk->save(); 

        return response()->json([
            'success' => true,
            'message' => ''
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
        $product = Product::findOrFail($id);

        Product::destroy($id);

        return response()->json([
            'success' => true,
            'message' => ''
        ]);
    }

    public function apiProducts()
    {
        $products = Product::all();

        return Datatables::of($products)
            ->addColumn('harga', function ($product) {
                return PriceHelper::formatPrice($product->harga);
            })
            ->addColumn('action', function ($product) {
                if (auth()->user()->role === 'admin') {
                    return '
                        <a onclick="editForm('.$product->id.')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
                        <a onclick="deleteData('.$product->id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a>';
                }
                return ''; 
            })
            ->make(true);
    }

    public function exportProductsAll(Request $request) {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $printed_date = now()->format('d M Y');
    
        $query = Product::query();
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    
        $products = $query->get();
    
        return PDF::loadView('products.ProductsAllPDF', [
            'products' => $products,
            'from_date' => $from,
            'to_date' => $to,
            'printed_date' => $printed_date
        ])->download('Data Produk.pdf');
    }
    
    public function exportExcel(Request $request) {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
    
        return (new ExportProducts($from, $to))->download('Data Produk.xlsx');
    }
    
    
}



