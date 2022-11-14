<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderProducts;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $guarded = [];

    public function orderItems()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }
}
