<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Address extends Model
{
    use HasFactory;
    //protected $table = "addresses";
    protected $fillable = [
        "title",
        "firstName",
        "lastName",
        "street",
        "number",
        "city",
        "country",
        "zipcode",
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class)->withPivot('shipping_address', 'billing_address');
    }
}
