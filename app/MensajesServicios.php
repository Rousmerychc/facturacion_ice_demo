<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MensajesServicios extends Model
{
    //
    protected $table = 'parametrica_mensajes_servicios';
        
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
