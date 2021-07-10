<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;
    protected $table = 'vital';
    protected $fillable = [
        'date',
        'type',
        'low_bp',
        'high_bp',
        'low_sugar',
        'high_sugar',
        'weight',
        'user_id',
        'result'
    ];

    public static function addEdit($data)
    {
        Vital::updateOrCreate(
            [
                'id' => @$data['id'],
            ],
            [
                'date' => $data['date'],
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'low_bp' => @$data['low_bp'],
                'high_bp' => @$data['high_bp'],
                'low_sugar' => @$data['low_sugar'],
                'high_sugar' => @$data['high_sugar'],
                'weight' => @$data['weight'],
                'result' => @$data['result'],                
            ]
        );
    }
}
