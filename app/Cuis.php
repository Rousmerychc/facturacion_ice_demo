<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuis extends Model
{
    //
    protected $table = 'cuis';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_usuario','id_sucursal','punto_venta','codigo_cuis','fecha_vigencia','fecha_hora'];

    public $timestamps = false;
}
