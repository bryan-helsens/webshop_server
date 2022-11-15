<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderProducts;
use App\Models\Address;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $guarded = [];

    public function orderItems()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }

    public function orderBillingAddress()
    {
        return $this->hasOne(Address::class, "order_id", "id")->where('type', 0);
    }

    public function orderShippingAddress()
    {
        return $this->hasOne(Address::class, "order_id", "id")->where('type', 1);
    }
}
