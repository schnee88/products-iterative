<?php

namespace Tests\Unit;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function controller_can_increase_quantity()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $controller = new ProductController();
        $response = $controller->increaseQuantity($product);

        $this->assertEquals(3, $product->fresh()->quantity);
    }

    /** @test */
    public function controller_can_decrease_quantity()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $controller = new ProductController();
        $response = $controller->decreaseQuantity($product);

        $this->assertEquals(1, $product->fresh()->quantity);
    }
}
