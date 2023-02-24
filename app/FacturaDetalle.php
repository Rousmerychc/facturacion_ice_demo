<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    //
    protected $table = 'factura_detalle';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','id_tabla_factura','id_sucursal','punto_venta','id_producto_sin','codigo_producto_empresa','descripcion','unidad_medida_des','cantidad','precio',
                            'subtotal','alicouta_iva','neto_ice','alicuota_esp','alicuota_por','ice_esp','ice_por','cantidad_l'];

    public $timestamps = false;
}
