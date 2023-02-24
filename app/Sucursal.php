<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    //
    protected $table = 'sucursal';
    
    protected $primarykey = 'id';

    protected $fillable = ['nro_sucursal','municipio','descripcion','direccion','telefono','estado','usuario','fecha'];

    public $timestamps = false;
}
