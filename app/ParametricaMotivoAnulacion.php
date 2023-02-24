<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaMotivoAnulacion extends Model
{
    //
    protected $table = 'parametricas_motivo_anulacion';
        
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
