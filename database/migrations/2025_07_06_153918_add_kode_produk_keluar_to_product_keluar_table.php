<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKodeProdukKeluarToProductKeluarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/…_add_kode_produk_keluar_to_product_keluar_table.php
public function up()
{
    Schema::table('product_keluar', function (Blueprint $table) {
        $table->string('kode_produk_keluar')->unique()->after('id');
    });
}




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('product_keluar', function (Blueprint $table) {
        $table->dropColumn('kode_produk_keluar');
    });
}
}
