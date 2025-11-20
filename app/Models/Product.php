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

    // Add tags relationship
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Your existing methods...
    public function increaseQuantity(int $amount = 1): bool
    {
        $this->increment('quantity', $amount);
        
        if ($this->status === 'out_of_stock' && $this->quantity > 0) {
            $this->update(['status' => 'active']);
        }
        
        return true;
    }

    public function decreaseQuantity(int $amount = 1): bool
    {
        if ($this->quantity < $amount) {
            return false;
        }
        
        $this->decrement('quantity', $amount);
        
        if ($this->quantity === 0) {
            $this->update(['status' => 'out_of_stock']);
        }
        
        return true;
    }

    public function safeDecrement(): bool
    {
        return $this->decreaseQuantity(1);
    }

        public function syncTags(array $tagNames)
    {
        $tagIds = [];
    
        foreach ($tagNames as $tagName) {
            if (trim($tagName) !== '') {
                $tag = Tag::firstOrCreate(
                    ['name' => trim($tagName)],
                    ['color' => $this->generateRandomColor()]
                );
                $tagIds[] = $tag->id;
            }
        }
    
        $this->tags()->sync($tagIds);
    }

    private function generateRandomColor()
    {
        $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1'];
        return $colors[array_rand($colors)];
    }
}