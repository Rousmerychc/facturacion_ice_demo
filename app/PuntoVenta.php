<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    //
    protected $table = 'punto_venta';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_sucursal','tipo_punto_venta','descripcion','estado','usuario'];

    public $timestamps = false;
}
