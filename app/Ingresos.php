<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingresos extends Model
{
    //
    protected $table = 'ingresos';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_sucursal','nro_por_sucursal','fecha','fecha_hora','monto_total','estado','usuario'];

    public $timestamps = false;
}
