<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facturacion extends Model
{
    //

    protected $table = 'factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_factura','id_sucursal','punto_venta','nro_fac_manual','nro_nota_venta','fecha','fecha_hora','hora_impuestos','razon_social','cuf','cufd','tipo_documento_identidad',
                            'nro_documento','complemento','codigo_cliente','id_metodo_pago','nro_tarjeta','monto_total','monto_total_sujeto_iva','ice_porcentual','ice_especial','descuento_adicional',
                            'monto_total_moneda','codigo_excepcion','cafc','id_leyenda','id_usuario','fuera_linea','fac_manual','tipo_emision_n','pago','codigo_recepcion','estado'];

    public $timestamps = false;
}
