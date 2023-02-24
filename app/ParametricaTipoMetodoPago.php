<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoMetodoPago extends Model
{
    //
    protected $table = 'parametrica_tipo_pago';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion', 'estado'];

    public $timestamps = false;
}
