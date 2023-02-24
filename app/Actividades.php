<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actividades extends Model
{
    //
    protected $table = 'parametrica_actividades';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo','descripcion','tipo_actividad'];

    public $timestamps = false;
}
