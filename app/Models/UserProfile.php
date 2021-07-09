<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'blood_group', 'genotype', 'is_smoking', 'is_alcohol', 'is_diet', 'last_medical_checkup', 'antibiotics', 'blood_presure', 'antacid', 'hormone_therapy', 'anti_asthma', 'arpirin', 'diet_pill', 'supplement', 'herbal_product', 'herbal_', 'arpirin', 'exercise_level', 'is_any_disease','user_id'
    ];

    protected $table = 'user_profile';
}
