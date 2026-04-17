<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unavailability extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start',
        'end',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
