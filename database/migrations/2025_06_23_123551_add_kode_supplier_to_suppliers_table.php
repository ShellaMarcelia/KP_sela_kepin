<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKodeSupplierToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('kode_supplier')->unique()->nullable(); 
        });
    }
    
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('kode_supplier');
        });
    }
    
}
