<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaqueteBultos extends Model
{
    //
    protected $table = 'paquete_bultos';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_factura','detalle','valor_detalle'];

    public $timestamps = false;
}
