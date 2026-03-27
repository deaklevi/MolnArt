<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = true;
    
    public $fillable = [
        'name', 'time'
    ];
}
