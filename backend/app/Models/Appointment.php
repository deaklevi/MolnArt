<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_from',
        'appointment_to',
        'service',
        'customer_id',
        'user_id',
    ];
    
    protected $casts = [
        'appointment_from' => 'datetime',
        'appointment_to'   => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function products():BelongsToMany{
        return $this->belongsToMany(Product::class,'appointment_product')->using(AppointmentProduct::class)->withPivot('quantity')->as('usage');
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
}
