<?php

namespace App\Http\Controllers;


use App\Customer;
use App\Exports\ExportCustomers;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Excel;
use PDF;

class CustomerController extends Controller
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
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index');
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
        $this->validate($request, [
            'nama'      => 'required',
            'alamat'    => 'required',
            'email'     => 'required|unique:customers,email',
            'telepon'   => 'required|string|min:10|max:13',
        ]);
    
        $lastCustomer = Customer::orderBy('kode_customer', 'desc')->first();

        if ($lastCustomer && preg_match('/C(\d{4})/', $lastCustomer->kode_customer, $matches)) {
            $lastNumber = (int) $matches[1];
        } else {
            $lastNumber = 0; // Jika tidak ada data, mulai dari 0
        }
    
        // Buat kode baru
        $newNumber = $lastNumber + 1;
        $kode_customer = 'C' . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // Misal: C0001
    
        //  Store the new customer
        Customer::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'kode_customer' => $kode_customer,  // Store the generated or passed customer code
        ]);
    
        return response()->json([
            'success'    => true,
            'message'    => ''
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
        $customer = Customer::find($id);
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'nama'      => 'required|string|min:2',
            'alamat'    => 'required|string|min:2',
            'email'     => 'required|string|email|max:255|unique:customers,email,' . $id,  // Correct the validation rule
            'telepon'   => 'required|string|min:10|max:13',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update($request->all());

        return response()->json([
            'success'    => true,
            'message'    => ''
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
        Customer::destroy($id);

        return response()->json([
            'success'    => true,
            'message'    => ''
        ]);
    }

    public function search(Request $request)
{
    $query = $request->get('query');

    $customers = Customer::where('nama', 'like', '%' . $query . '%')
        ->orWhere('kode_customer', 'like', '%' . $query . '%')
        ->get(['id', 'nama', 'kode_customer']); // hanya ambil kolom yang dibutuhkan

    return response()->json(['customers' => $customers]);
}

    public function apiCustomers(Request $request)
    {
        $customer = Customer::query(); // INI WAJIB PAKAI query(), bukan all()

        return Datatables::of($customer)
            ->addColumn('action', function($customer){
                if (auth()->user()->role === 'admin') {
                return 
                    '<a onclick="editForm('. $customer->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                    '<a onclick="deleteData('. $customer->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Hapus</a>';
                }
                return '';
            })
            ->rawColumns(['action'])
            ->make(true); // generate sesuai struktur yg diharapkan DataTables
    }


    public function exportCustomersAll(Request $request) {
		$from = $request->input('from_date');
		$to = $request->input('to_date');
		$printed_date = now()->format('d M Y');
	
		$query = Customer::query();
	
		if ($from && $to) {
			$query->whereBetween('created_at', [$from, $to]);
		}
	
		$customers = $query->get();
	
		return PDF::loadView('customers.CustomersAllPDF', [
			'customers' => $customers,
			'from_date' => $from,
			'to_date' => $to,
			'printed_date' => $printed_date
		])->download('Data Pelanggan.pdf');
	}

    public function exportExcel(Request $request) {
		$from = $request->input('from_date');
		$to = $request->input('to_date');
	
		return (new ExportCustomers($from, $to))->download('Data Pelanggan.xlsx');
	}
}
