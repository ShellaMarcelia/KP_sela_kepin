<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('dashboard', function () {
	return view('layouts.master');
});

// Grup semua route setelah login
Route::group(['middleware' => 'auth'], function () {

	// Customers
	Route::resource('customers', 'CustomerController');
	Route::get('/apiCustomers', 'CustomerController@apiCustomers')->name('api.customers');

	// Products Out
	Route::resource('productsOut', 'ProductKeluarController');
	Route::get('/apiProductsOut', 'ProductKeluarController@apiProductsOut')->name('api.productsOut');
	Route::get('/product-keluar/customer/{id}', 'ProductKeluarController@getCustomerById');
	Route::get('/product-keluar/product/{id}', 'ProductKeluarController@getProductById');
	Route::get('/search-customers', 'ProductKeluarController@searchCustomers')->name('search.customers');
	Route::get('/search-products', 'ProductKeluarController@searchProducts')->name('search.products');

	// Products In
	Route::resource('productsIn', 'ProductMasukController');
	Route::get('/apiProductsIn', 'ProductMasukController@apiProductsIn')->name('api.productsIn');
	Route::get('/productsIn/supplier/{id}', 'ProductMasukController@getSupplierById');
	Route::get('/productsIn/product/{id}', 'ProductMasukController@getProductById');
	Route::get('/search-suppliers', 'ProductMasukController@searchSuppliers')->name('search.suppliers');
	Route::get('/search-products-in', 'ProductMasukController@searchProducts')->name('search.products');

	// Suppliers
	Route::get('/suppliers', 'SupplierController@index')->name('suppliers.index');
	Route::get('/apiSuppliers', 'SupplierController@apiSuppliers')->name('api.suppliers');

	// Products
	Route::get('/products', 'ProductController@index')->name('products.index');
	Route::get('/apiProducts', 'ProductController@apiProducts')->name('api.products');

	// Export
	Route::get('/exportCustomersAll', 'CustomerController@exportCustomersAll')->name('exportPDF.customersAll');
	Route::get('/exportCustomersAllExcel', 'CustomerController@exportExcel')->name('exportExcel.customersAll');

	Route::get('/exportSuppliersAll', 'SupplierController@exportSuppliersAll')->name('exportPDF.suppliersAll');
	Route::get('/exportSuppliersAllExcel', 'SupplierController@exportExcel')->name('exportExcel.suppliersAll');

	Route::get('/exportProductsAll', 'ProductController@exportProductsAll')->name('exportPDF.productsAll');
	Route::get('/exportProductsAllExcel', 'ProductController@exportExcel')->name('exportExcel.productsAll');

	
	Route::get('/exportProductKeluarAll',        'ProductKeluarController@exportProductKeluarAll')->name('exportPDF.productKeluarAll');
	Route::get('/exportProductKeluarAllExcel',   'ProductKeluarController@exportExcelProductKeluar')->name('exportExcel.productKeluarAll');


	Route::get('/exportProductMasukAll', 'ProductMasukController@exportProductMasukAll')->name('exportPDF.productMasukAll');
	Route::get('/exportProductMasukAllExcel', 'ProductMasukController@exportExcel')->name('exportExcel.productMasukAll');

	// CRUD Supplier
	Route::get('/suppliers/create', 'SupplierController@create')->name('suppliers.create');
	Route::post('/suppliers', 'SupplierController@store')->name('suppliers.store');
	Route::get('/suppliers/{supplier}/edit', 'SupplierController@edit')->name('suppliers.edit');
	Route::patch('/suppliers/{supplier}', 'SupplierController@update')->name('suppliers.update');
	Route::delete('/suppliers/{supplier}', 'SupplierController@destroy')->name('suppliers.destroy');

	// CRUD Produk
	Route::get('/products/create', 'ProductController@create')->name('products.create');
	Route::post('/products', 'ProductController@store')->name('products.store');
	Route::get('/products/{product}/edit', 'ProductController@edit')->name('products.edit');
	Route::patch('/products/{product}', 'ProductController@update')->name('products.update');
	Route::delete('/products/{product}', 'ProductController@destroy')->name('products.destroy');

	// User management
	Route::resource('user', 'UserController');
	Route::get('/apiUser', 'UserController@apiUsers')->name('api.users');
});
