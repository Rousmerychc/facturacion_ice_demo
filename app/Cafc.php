<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cafc extends Model
{
    //
    protected $table = 'cafc';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_sucursal','id_punto_venta','codigo_cafc','nro_cafc_emitidas','nro_inicial','nro_final','fecha_vigencia','fecha','usuario'];

    public $timestamps = false;
}
