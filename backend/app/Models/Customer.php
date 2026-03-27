<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name','email','phone_number','user_id'
    ];

    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
}
