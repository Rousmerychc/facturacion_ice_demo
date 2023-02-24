<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoPuntoVenta extends Model
{
    //
    protected $table = 'parametrica_tipo_punto_venta';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
