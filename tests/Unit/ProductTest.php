<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'quantity' => 10,
        ]);
    }

    /** @test */
    public function it_can_increment_quantity()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $product->increment('quantity');

        $this->assertEquals(6, $product->fresh()->quantity);
    }

    /** @test */
    public function it_can_decrement_quantity_but_not_below_zero()
    {
        $product = Product::factory()->create(['quantity' => 1]);

        $product->decrement('quantity');
        $this->assertEquals(0, $product->fresh()->quantity);

        $product->decrement('quantity');
        $this->assertEquals(0, $product->fresh()->quantity, "Quantity cannot go below 0");
    }
}
