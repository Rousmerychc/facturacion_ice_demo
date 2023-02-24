<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformacionAdicional extends Model
{
    //
    protected $table = 'informacion_adicional';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','descripcion','descripcion_detalle'];

    public $timestamps = false;
}
