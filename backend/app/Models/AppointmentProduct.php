<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AppointmentProduct extends Pivot
{
    protected $table    = 'appointment_product';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['appointment_id','product_id','quantity'];
}
