<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    //
    protected $table = 'grupos';
        
    protected $primarykey = 'id';

    protected $fillable = ['descripcion','ice_porcentual','ice_especifico','usuario','fecha'];

    public $timestamps = false;
}
