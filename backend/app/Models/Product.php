<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name','unit','type','stock',];

    public function appointments() :BelongsToMany{
        return $this->belongsToMany(Appointment::class)->withPivot('quantity');
    }
}
