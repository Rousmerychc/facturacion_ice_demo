<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoPendiente extends Model
{
    //
    protected $table = 'codigo_pendiente';
        
    protected $primarykey = 'id';

    protected $fillable = ['codigo','estado'];

    public $timestamps = false;
}
