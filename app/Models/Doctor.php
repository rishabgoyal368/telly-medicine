<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Mail;

class Doctor extends Authenticatable implements JWTSubject
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
        'name', 'email', 'password', 'profile_image', 'location', 'speciality', 'description', 'status'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'deleted_at', 'status',  'created_at', 'updated_at'
    ];

    public static function addEdit($data)
    {
        return Doctor::updateOrCreate(
            ['id' => @$data['id']],
            [
                'user_id' => @$data['user_id'],
                'user_name' => @$data['user_name'],
                'email' => @$data['email'],
                'mobile_number' => @$data['mobile_number'],
                'password' => @$data['password'],
                'type' => @$data['type'] ?: 1,
                'status' => @$data['status'],

                'gender' => @$data['gender'] ?: '',
                'secret_key' => @$data['secret_key'] ?: '',
                'weight' => @$data['weight'] ?: '',
                'height' => @$data['height'] ?: '',
                'age' => @$data['age'] ?: '',
                'hmo_id_no' => @$data['hmo_id_no'] ?: '',
                'hmo_id_name' => @$data['hmo_id_name'] ?: '',
                'profile_image' => @$data['profile_image'] ?: '',
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

    public function getProfileImage()
    {
        return $this->profile_image ? env('APP_URL') . 'uploads/users/' . $this->profile_image : env('APP_URL') . 'images/profile/dumy.png';
    }
}
