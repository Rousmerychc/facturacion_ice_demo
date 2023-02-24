<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoMoneda extends Model
{
    //
    protected $table = 'parametrica_tipo_moneda';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
