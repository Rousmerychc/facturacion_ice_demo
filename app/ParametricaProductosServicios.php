<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaProductosServicios extends Model
{
    //
    protected $table = 'parametrica_productos_servicios';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_actividad','codigo_producto','descripcion_producto'];

    public $timestamps = false;
}
