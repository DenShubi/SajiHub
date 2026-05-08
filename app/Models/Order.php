<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'payment_proof_url',
        'notes',
    ];

    /**
     * The user (member) who placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The items in this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
