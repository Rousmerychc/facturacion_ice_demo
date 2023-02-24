<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreSubProductosFactura extends Model
{
    //
    protected $table = 'pre_sub_productos_factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','id_producto','id_sub_producto'];

    public $timestamps = false;
}
