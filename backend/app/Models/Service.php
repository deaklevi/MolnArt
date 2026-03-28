<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    
    public $fillable = [
        'name', 'time'
    ];

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }


}
