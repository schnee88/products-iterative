<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_increment_quantity()
    {
        $product = Product::factory()->create(['quantity' => 5]);
        $product->increment('quantity');
        $this->assertEquals(6, $product->fresh()->quantity);
    }

    /** @test */
    public function it_can_decrement_quantity()
    {
        $product = Product::factory()->create(['quantity' => 5]);
        $product->decrement('quantity');
        $this->assertEquals(4, $product->fresh()->quantity);
    }

    /** @test */
    public function quantity_cannot_be_negative()
    {
        $product = Product::factory()->create(['quantity' => 0]);
        $product->decrement('quantity');
        $this->assertEquals(0, $product->fresh()->quantity);
    }
}
