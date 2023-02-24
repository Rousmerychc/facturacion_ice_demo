<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiviadesSector extends Model
{
    // 
    protected $table = 'parametrica_lista_actividades_documento_sector';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_actividad','codigo_documento_sector','tipo_documento_sector'];

    public $timestamps = false;
}
