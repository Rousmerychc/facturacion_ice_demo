<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    //
    protected $table = 'parametrica_unidad_medida';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion','codigo','estado'];

    public $timestamps = false;
}
