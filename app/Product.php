<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['kode_produk','nama', 'harga'];


    protected $hidden = ['created_at','updated_at'];

}
