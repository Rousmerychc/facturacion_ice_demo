<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoHabitacion extends Model
{
    //
    protected $table = 'parametrica_tipo_habitacion';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
