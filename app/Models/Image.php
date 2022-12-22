<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'image';
    protected $fillable=[
        'title',
        'path',
        'status',
        'extension'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'image_user', 'image_id', 'user_id');
    }
}
