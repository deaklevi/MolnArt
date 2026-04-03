<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'user_id','day','start','end'
    ];

    // protected $casts = [
    //     'start' => 'datetime',
    //     'end'   => 'datetime',
    // ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
