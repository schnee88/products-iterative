<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_a_product()
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Test Product',
            'quantity' => 10,
            'description' => 'Some description',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product', 'quantity' => 10]);
    }

    /** @test */
    public function user_can_edit_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->put(route('products.update', $product), [
            'name' => 'Updated Name',
            'quantity' => 5,
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name', 'quantity' => 5]);
    }

    /** @test */
    public function user_can_increment_and_decrement_quantity_via_routes()
    {
        $product = Product::factory()->create(['quantity' => 3]);

        // Increment
        $this->patch(route('products.increment', $product));
        $this->assertEquals(4, $product->fresh()->quantity);

        // Decrement
        $this->patch(route('products.decrement', $product));
        $this->assertEquals(3, $product->fresh()->quantity);
    }
}
