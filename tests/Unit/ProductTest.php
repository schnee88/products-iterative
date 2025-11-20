<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_can_create_a_product()
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
    public function test_it_can_increment_quantity()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $product->increment('quantity');

        $this->assertEquals(6, $product->fresh()->quantity);
    }

    /** @test */
    public function test_it_can_decrement_quantity_but_not_below_zero()
    {
        $product = Product::factory()->create(['quantity' => 1]);

        // First decrement
        $product->safeDecrement();
        $this->assertEquals(0, $product->fresh()->quantity);

        // Second decrement — should not go below 0
        $product->safeDecrement();
        $this->assertEquals(0, $product->fresh()->quantity, "Quantity cannot go below 0");
    }

    /** @test */
    public function test_increase_quantity_method()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $result = $product->increaseQuantity(3);

        $this->assertTrue($result);
        $this->assertEquals(8, $product->fresh()->quantity);
    }

    /** @test */
    public function test_increase_quantity_default_amount()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $product->increaseQuantity();

        $this->assertEquals(6, $product->fresh()->quantity);
    }

    /** @test */
    public function test_increase_quantity_activates_out_of_stock_product()
    {
        $product = Product::factory()->create([
            'quantity' => 0,
            'status' => 'out_of_stock'
        ]);

        $product->increaseQuantity(2);

        $this->assertEquals(2, $product->fresh()->quantity);
        $this->assertEquals('active', $product->fresh()->status);
    }

    /** @test */
    public function test_decrease_quantity_method()
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $result = $product->decreaseQuantity(3);

        $this->assertTrue($result);
        $this->assertEquals(7, $product->fresh()->quantity);
    }

    /** @test */
    public function test_decrease_quantity_default_amount()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $product->decreaseQuantity();

        $this->assertEquals(4, $product->fresh()->quantity);
    }

    /** @test */
    public function test_decrease_quantity_fails_when_insufficient_quantity()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $result = $product->decreaseQuantity(5);

        $this->assertFalse($result);
        $this->assertEquals(2, $product->fresh()->quantity); // Quantity unchanged
    }

    /** @test */
    public function test_decrease_quantity_sets_status_to_out_of_stock_at_zero()
    {
        $product = Product::factory()->create([
            'quantity' => 2,
            'status' => 'active'
        ]);

        $product->decreaseQuantity(2);

        $this->assertEquals(0, $product->fresh()->quantity);
        $this->assertEquals('out_of_stock', $product->fresh()->status);
    }

    /** @test */
    public function test_safe_decrement_is_alias_for_decrease_quantity()
    {
        $product = Product::factory()->create(['quantity' => 3]);

        $result = $product->safeDecrement();

        $this->assertTrue($result);
        $this->assertEquals(2, $product->fresh()->quantity);
    }

    /** @test */
    public function test_safe_decrement_fails_gracefully_at_zero()
    {
        $product = Product::factory()->create(['quantity' => 0]);

        $result = $product->safeDecrement();

        $this->assertFalse($result);
        $this->assertEquals(0, $product->fresh()->quantity);
    }
}