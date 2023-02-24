<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaPaisOrigen extends Model
{
    //
    protected $table = 'parametrica_pais_origen';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_clasificador','descripcion'];

    public $timestamps = false;
}
