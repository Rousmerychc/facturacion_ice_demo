<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nandina extends Model
{
    //
    protected $table = 'nandina';
        
    protected $primarykey = 'id';

    protected $fillable = ['id','codigo_producto','nandina'];

    public $timestamps = false;
}
