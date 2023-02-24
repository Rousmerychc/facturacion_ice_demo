<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    //
    protected $table = 'cliente';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion','id_categoria_precio','responsable','direccion',
    'id_tipo_documento','razon_social','nro_documento','complemento','email','excepcion',
    'usuario','fecha','estado'];

    public $timestamps = false;
}
