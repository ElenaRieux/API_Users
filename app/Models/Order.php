<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_id', 'amount', 'currency', 'status'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product_details')
            ->withPivot('quantity');
    }
}
