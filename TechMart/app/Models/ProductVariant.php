<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $primaryKey = 'variant_id';

    protected $fillable = [
        'product_id',
        'variant_name',
        'additional_price',
        'stock_quantity',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the cart items for the variant.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id', 'variant_id');
    }

    /**
     * Get the order items for the variant.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id', 'variant_id');
    }
}
