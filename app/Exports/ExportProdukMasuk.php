<?php

namespace App\Exports;

use App\Product_Masuk;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ExportProdukMasuk implements FromView
{
    use Exportable;

    protected $from, $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        $query = Product_Masuk::with(['product', 'supplier']);

        if ($this->from && $this->to) {
            $query->whereBetween('tanggal', [$this->from, $this->to]);
        }

        $produkMasuk = $query->get();

        // Grupkan data berdasarkan nama produk
        $grouped = $produkMasuk->groupBy('product.nama')->map(function ($items, $nama_produk) {
            return [
                'nama_produk' => $nama_produk,
                'qty_total' => $items->sum('qty'),
                'rowspan' => $items->count(),
                'items' => $items
            ];
        });

        return view('product_masuk.productMasukAllExcel', [
            'data' => $grouped
        ]);
    }
}

