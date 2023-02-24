<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacturaTasaCeroDetalle extends Model
{
    //
    protected $table = 'factura_detalle_tasa_cero';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','id_tabla_factura','id_sucursal','punto_venta','id_producto_sin','codigo_producto_empresa','descripcion','unidad_medida_des','cantidad','precio',
                            'subtotal'];

    public $timestamps = false;
}
