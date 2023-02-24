<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacturaTasaCero extends Model
{
    protected $table = 'factura_tasa_cero';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_factura','id_sucursal','punto_venta','nro_fac_manual','nro_nota_venta','fecha','fecha_hora','hora_impuestos','razon_social','cuf','cufd','tipo_documento_identidad',
                            'nro_documento','complemento','codigo_cliente','id_metodo_pago','nro_tarjeta','monto_total','monto_total_sujeto_iva','descuento_adicional',
                            'monto_total_moneda','codigo_excepcion','cafc','id_leyenda','id_usuario','fuera_linea','fac_manual','tipo_emision_n','estado'];

    public $timestamps = false;
}
