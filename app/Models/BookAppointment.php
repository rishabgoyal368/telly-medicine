<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAppointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'doctor_id', 'date', 'time'
    ];
    protected $table = 'book_appointment';

    public static function addEdit($data)
    {
        return BookAppointment::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'doctor_id' => $data['doctor_id'],
            ],
            [
                'date' => $data['date'],
                'time' => $data['time'],
            ]
        );
    }

    public function getDateAttribute($value)
    {
        return is_numeric($value) ? date('d-m-Y', $value) : $value;
    }
}
