<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricasEventosSignificativos extends Model
{
    //
    protected $table = 'parametrica_eventos_significativos';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_clasificador','descripcion','paquete_manual'];

    public $timestamps = false;
}
