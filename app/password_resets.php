<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class password_resets extends Model
{
    //
    protected $table = 'password_resets';
        
            protected $primarykey = 'email';
        
            protected $fillable = ['token','create_at'];

            public $timestamps = false;
}
