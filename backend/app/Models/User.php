<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'password',
        'description',
        'profile_image',
    ];

    // Ezzel mondjuk meg a Laravelnek, hogy ne az emailt keresse login-kor
    public function findForPassport($username) {
        return $this->where('user_name', $username)->first();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function services():HasMany
    {
        return $this->hasMany(Service::class);
    }
}
