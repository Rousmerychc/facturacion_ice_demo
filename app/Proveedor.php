<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    //
    protected $table = 'proveedor';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion','fecha','usuario'];

    public $timestamps = false;
}
