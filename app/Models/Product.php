<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'description',
        'expiration_date',
        'status',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function increaseQuantity(int $amount = 1): bool
    {
        $this->increment('quantity', $amount);
        
        // Update status if it was out of stock
        if ($this->status === 'out_of_stock' && $this->quantity > 0) {
            $this->update(['status' => 'active']);
        }
        
        return true;
    }

    public function decreaseQuantity(int $amount = 1): bool
    {
        if ($this->quantity < $amount) {
            return false; // Cannot decrease below 0
        }
        
        $this->decrement('quantity', $amount);
        
        // Auto-update status if quantity reaches 0
        if ($this->quantity === 0) {
            $this->update(['status' => 'out_of_stock']);
        }
        
        return true;
    }

    public function safeDecrement(): bool
    {
        return $this->decreaseQuantity(1);
    }
}