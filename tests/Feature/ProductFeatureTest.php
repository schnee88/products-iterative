<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_add_edit_and_delete_product()
    {
        // Create product
        $response = $this->post(route('products.store'), [
            'name' => 'Feature Product',
            'quantity' => 3,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Feature Product']);

        $product = Product::first();

        // Update product
        $response = $this->put(route('products.update', $product), [
            'name' => 'Updated Feature Product',
            'quantity' => 10,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Feature Product']);

        // Increment quantity
        $response = $this->patch(route('products.increment', $product));
        $this->assertEquals(11, $product->fresh()->quantity);

        // Decrement quantity
        $response = $this->patch(route('products.decrement', $product));
        $this->assertEquals(10, $product->fresh()->quantity);

        // Delete product
        $response = $this->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $this->assertDeleted($product);
    }
}
