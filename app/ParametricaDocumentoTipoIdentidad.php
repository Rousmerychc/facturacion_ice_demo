<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParametricaDocumentoTipoIdentidad extends Model
{
    //
    protected $table = 'parametrica_tipo_documento_identidad';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_clasificador','descripcion'];

    public $timestamps = false;
}
