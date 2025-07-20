<?php

namespace App\Http\Controllers;

use App\Exports\ExportSuppliers;
use App\Supplier;
use Excel;
use Illuminate\Http\Request;
use PDF;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller {
	public function __construct()
	{
		$this->middleware('role:admin,staff')->only(['index', 'apiSuppliers']);
	}

	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$suppliers = Supplier::all();
		return view('suppliers.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$this->validate($request, [
			'nama' => 'required',
			'alamat' => 'required',
			'email'     => 'required|unique:suppliers,email',
			'telepon'   => 'required|string|min:10|max:13',
		]);
    
		$lastSupplier = Supplier::orderBy('kode_supplier', 'desc')->first();
		if ($lastSupplier && preg_match('/S(\d{4})/', $lastSupplier->kode_supplier, $matches)) {
			$lastNumber = (int) $matches[1]; 
		} else {
			$lastNumber = 0; 
		}

		$newNumber = $lastNumber + 1;
		$kode_supplier = 'S' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); 
		$supplier = Supplier::create([
			'nama' => $request->nama,
			'alamat' => $request->alamat,
			'email' => $request->email,
			'telepon' => $request->telepon,
			'kode_supplier' => '' 
		]);

		$supplier->kode_supplier = 'S' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
		$supplier->save();
	
		return response()->json([
			'success' => true,
			'message' => '',
		]);
	}
	

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$supplier = Supplier::find($id);
		return $supplier;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$this->validate($request, [
			'nama' => 'required|string|min:2',
			'alamat' => 'required|string|min:2',
			'email'     => 'required|string|email|max:255|unique:suppliers,email,' . $id,  
            'telepon'   => 'required|string|min:10|max:13',
		]);

		$supplier = Supplier::findOrFail($id);

		$supplier->update($request->all());

		return response()->json([
			'success' => true,
			'message' => '',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		Supplier::destroy($id);

		return response()->json([
			'success' => true,
			'message' => '',
		]);
	}

	public function apiSuppliers() {
		$suppliers = Supplier::all();

		return Datatables::of($suppliers)
			->addColumn('action', function ($supplier) {
				if (auth()->user()->role === 'admin') {
					return '
						<a onclick="editForm('.$supplier->id.')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>
						<a onclick="deleteData('.$supplier->id.')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a>';
				}
				return ''; 
			})
			->make(true);
	}

	

	public function exportSuppliersAll(Request $request) {
		$from = $request->input('from_date');
		$to = $request->input('to_date');
		$printed_date = now()->format('d M Y');
	
		$query = Supplier::query();
	
		if ($from && $to) {
			$query->whereBetween('created_at', [$from, $to]);
		}
	
		$suppliers = $query->get();
	
		return PDF::loadView('suppliers.SuppliersAllPDF', [
			'suppliers' => $suppliers,
			'from_date' => $from,
			'to_date' => $to,
			'printed_date' => $printed_date
		])->download('Data Supplier.pdf');
	}
	
	

	public function exportExcel(Request $request) {
		$from = $request->input('from_date');
		$to = $request->input('to_date');
	
		return (new ExportSuppliers($from, $to))->download('Data Supplier.xlsx');
	}
	
}
