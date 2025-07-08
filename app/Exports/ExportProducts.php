<?php

namespace App\Exports;

use App\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExportProducts implements FromView
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
        $query = Product::query();

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return view('products.ProductsAllExcel', [
            'products' => $query->get()
        ]);
    }
}



