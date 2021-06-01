<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Mail;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    const STATUSINACTIVE = 'inactive';
    const STATUSACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_name', 'email', 'password', 'mobile_number', 'otp', 'is_verified_phone', 'is_verified_email', 'profile_image', 'type', 'gender',
        'secret_key'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function addEdit($data)
    {
        return User::updateOrCreate(
            ['id' => @$data['id']],
            [
                'user_id' => @$data['user_id'],
                'user_name' => @$data['user_name'],
                'email' => @$data['email'],
                'mobile_number' => @$data['mobile_number'],
                'password' => @$data['password'],
                'type' => @$data['type'] ?: 1,
                'status' => @$data['status'],

            ]
        );
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
