<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreFactura extends Model
{
    //
    protected $table = 'pre_factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['fecha','fecha_hora','razon_social','tipo_documento_identidad','nro_documento','complemento','incoterm','icoterm_detalle',
                            'puerto_destino','lugar_destino','codigo_pais','codigo_cliente','direccion_cliente','metodo_pago','monto_total','total_gastos_nacionales_fob',
                            'total_gastos_internacionales','monsto_detalle','total_menos_iva','codigo_moneda','tipo_cambio','monto_total_moneda','numero_descripcion_paquetes_bultos',
                            'informacion_adicional','codigo_excepcion','cafc','codigo_documento_serctor','id_leyenda','forma_pago','ritex','ususario','estado'];

    public $timestamps = false;
}
