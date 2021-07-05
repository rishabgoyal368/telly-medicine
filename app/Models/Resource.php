<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title', 'name', 'description', 'image'
    ];
    protected $table = 'resource';

    public static function addEdit($data)
    {
        return Resource::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'title' => @$data['title'],
                'name' => @$data['name'],
                'description' => @$data['description'],
                'image' => @$data['image'],

            ]
        );
    }

    public function getImage()
    {
        if (!empty($this->image)) {
            $image = env('APP_URL') . 'uploads/resource' . '/' . $this->image;
        } else {
            $image = DefaultImgPath;
        }
        return $image;
    }


}
