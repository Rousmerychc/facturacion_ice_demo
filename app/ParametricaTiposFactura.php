<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTiposFactura extends Model
{
    //
    protected $table = 'parametrica_tipo_factura';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;
}
