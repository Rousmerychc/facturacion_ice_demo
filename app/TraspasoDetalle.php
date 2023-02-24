<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraspasoDetalle extends Model
{
    //

    protected $table = 'traspaso_detalle';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_traspaso','id_producto','cantidad_paquete','cantidad_unidades','cantidad','precio_unitario','subtotal'];

    public $timestamps = false;

}
