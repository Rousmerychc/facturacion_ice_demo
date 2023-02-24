<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubProductosFactura extends Model
{
    //
    protected $table = 'sub_productos_factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','id_sub_producto','id_sub_producto'];

    public $timestamps = false;
}
