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
        'name', 'email', 'password', 'mobile_number', 'gender', 'location', 'speciality', 'description', 'status', 'profile_image',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public static function addEdit($data)
    {
        return Doctor::updateOrCreate(
            ['id' => @$data['id']],
            [
                'name' => $data['name'] ?: '',
                'email' => $data['email'] ?: '',
                'password' => $data['password'] ?: '',
                'mobile_number' => $data['mobile_number'] ?: '',
                'gender' => $data['gender'] ?: '',
                'location' => $data['location'] ?: '',
                'speciality' => $data['speciality'] ?: '',
                'description' => $data['description'] ?: '',
                'status' => $data['status'] ?: '',
                'profile_image' => $data['profile_image'] ?: '',
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
