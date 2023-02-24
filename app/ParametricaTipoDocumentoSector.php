<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaTipoDocumentoSector extends Model
{
    //
    protected $table = 'parametrica_tipo_documento_sector';
    
    protected $primarykey = 'id';

    protected $fillable = ['codigo_clasificador','descripcion'];

    public $timestamps = false;

}
