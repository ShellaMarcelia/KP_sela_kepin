<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKodeProdukMasukToProductMasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('product_masuk', function (Blueprint $table) {
        $table->string('kode_produk_masuk')->unique()->after('id');
    });
}




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_masuk', function (Blueprint $table) {
            $table->dropColumn('kode_produk_masuk');
        });
    }
}
