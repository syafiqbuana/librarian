<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
    
}
