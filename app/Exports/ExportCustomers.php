<?php

namespace App\Exports;

use App\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportCustomers implements FromView
{
    use Exportable;

    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        $query = Customer::query();

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return view('customers.CustomersAllExcel', [
            'customers' => $query->get()
        ]);
    }
}

