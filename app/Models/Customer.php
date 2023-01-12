<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = ['first_name', 'last_name', 'phone', 'status'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
