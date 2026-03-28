<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_from',
        'appointment_to',
        'service',
        'customer_id',
    ];
    
    // protected $casts = [
    //     'appointment_from' => 'datetime:Y-m-d H:i:s',
    //     'appointment_to' => 'datetime:Y-m-d H:i:s',
    // ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
