<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoEmision extends Model
{
    //
    protected $table = 'parametrica_tipo_emision';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
