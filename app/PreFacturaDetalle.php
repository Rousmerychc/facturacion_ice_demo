<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreFacturaDetalle extends Model
{
    //
    protected $table = 'pre_factura_detalle';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_factura','id_producto','cantidad','precio_unitorio','valor_agregado','valor_re_exportacion','precio_unitario_sin','subtotal','dessubpro','codigosubpro'];

    public $timestamps = false;
}
