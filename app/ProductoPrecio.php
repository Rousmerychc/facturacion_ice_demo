<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoPrecio extends Model
{
    //
    protected $table = 'productos_precio';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_sucursal','id_producto','precio1','precio2','precio3', '	precio_unitario1','	precio_unitario2','	precio_unitario3'];

    public $timestamps = false;
}
