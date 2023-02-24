<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrePaqueteBultos extends Model
{
    //
    protected $table = 'pre_paquete_bultos';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','id_factura','detalle','valor_detalle'];

    public $timestamps = false;
}
