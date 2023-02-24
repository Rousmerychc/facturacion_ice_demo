<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeyendasFacturacion extends Model
{
    //
    protected $table = 'parametrica_leyendas_factura';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_actividad','descripcion_leyenda'];

    public $timestamps = false;
}
