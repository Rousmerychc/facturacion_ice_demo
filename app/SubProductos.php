<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubProductos extends Model
{
    //
    protected $table = 'sub_productos';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion','user'];

    public $timestamps = false;
    
}
