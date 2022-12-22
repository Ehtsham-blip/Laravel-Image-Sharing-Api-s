<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPasswords extends Model
{
    use HasFactory;
    protected $table='reset_passwords';
    protected $fillable = [
        'email',
        'code',
        'created_at',
        'created_by',

    ];


    public function isExpire()
    {
        if ($this->created_at > now()->addHour()) {
            $this->delete();
        };
    }
}
