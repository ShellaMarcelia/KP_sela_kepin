<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['kode_customer','nama', 'alamat', 'email', 'telepon'];

    protected $hidden = ['created_at', 'updated_at'];
}
