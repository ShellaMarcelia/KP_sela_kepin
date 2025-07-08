@extends('layouts.master')

@section('top')
@endsection

@section('content')
<div class="row">

   

    {{-- Admin & Staff --}}
    @role('admin', 'staff', 'manajer')
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ \App\Customer::count() }}</h3>
                <p>Pelanggan</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('customers.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>{{ \App\Product_Masuk::count() }}</h3>
                <p>Produk Masuk</p>
            </div>
            <div class="icon">
                <i class="fa fa-plus"></i>
            </div>
            <a href="{{ route('productsIn.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ \App\Product_Keluar::count()  }}</h3>
                <p>Produk Keluar</p>
            </div>
            <div class="icon">
                <i class="fa fa-list"></i>
            </div>
            <a href="{{ route('productsOut.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>


    {{-- Admin & Manajer --}}
  
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ \App\Product::count() }}</h3>
                <p>Produk</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('products.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>{{ \App\Supplier::count() }}</h3>
                <p>Supplier</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('suppliers.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>


    {{-- Warning Stok Menipis untuk semua --}}
    @if($lowStockProducts->isNotEmpty())
        <div class="col-lg-12 col-xs-12">
            <div class="alert alert-warning">
                <strong>Warning!</strong> Beberapa produk kehabisan stok.
                <ul>
                    @foreach($lowStockProducts as $product)
                        <li>{{ $product->nama }} ({{ $product->qty }} unit tersisa)</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @endrole
</div>
@endsection
