<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    //
    protected $table = 'productos';
        
    protected $primarykey = 'id';

    protected $fillable = ['id_parametrica_producto','codigo_actividad','codigo_producto'
    ,'id_grupo_porcentual','id_grupo','descripcion_producto','id_medida','unidad_medida'
    ,'cantidad_litros_x_unidad','estado','unidad_por_paquete','precio_compra','usuario','fecha'];

    public $timestamps = false;
    
}
