<?php

namespace App\Models;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password',
        'role',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isRetailer() {
        return $this->role === 'retailer';
    }

    public function isEmployee() {
        return $this->role === 'employee';
    }

    public function isDistributor() {
        return $this->role === 'distributor';
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
