<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    //
    protected $table = 'factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_traspaso','id_sucursal_origen', 'id_sucursal_destino','fecha','fecha_hora', 'monto_total',
                            'id_usuario','estado'];

    public $timestamps = false;
}
