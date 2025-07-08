<?php

namespace App\Http\Controllers;

use App\Product; // Make sure to import the Product model
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Fetch products with stock less than 20
        $lowStockProducts = Product::where('qty', '<', 20)->get();

        // Return the view with lowStockProducts data
        return view('home', compact('lowStockProducts'));
    }
}
