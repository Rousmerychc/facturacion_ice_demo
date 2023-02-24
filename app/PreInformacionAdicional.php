<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreInformacionAdicional extends Model
{
    //
    protected $table = 'pre_informacion_adicional';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_factura','descripcion','descripcion_detalle'];

    public $timestamps = false;
}
