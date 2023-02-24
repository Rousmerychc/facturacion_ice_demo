<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cufd extends Model
{
    //
    protected $table = 'cufd';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_usuario','id_sucursal','punto_venta','codigo_cufd','codigo_control','fecha_vigencia','fecha_hora','fecha'];

    public $timestamps = false;
}
