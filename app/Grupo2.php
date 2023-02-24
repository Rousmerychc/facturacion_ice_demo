<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo2 extends Model
{
    //
    protected $table = 'grupo2';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion_grupo'];

    public $timestamps = false;
}
