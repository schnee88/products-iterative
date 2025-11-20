<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    // Test AJAX responses for quantity routes
    /** @test */
    public function test_controller_can_increase_quantity_via_ajax()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->postJson(route('products.increase-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Quantity increased successfully!',
            'quantity' => 3,
            'status' => $product->fresh()->status
        ]);
        $this->assertEquals(3, $product->fresh()->quantity);
    }

    /** @test */
    public function test_controller_can_decrease_quantity_via_ajax()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->postJson(route('products.decrease-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Quantity decreased successfully!',
            'quantity' => 1,
            'status' => $product->fresh()->status
        ]);
        $this->assertEquals(1, $product->fresh()->quantity);
    }

    /** @test */
    public function test_controller_decrease_quantity_fails_gracefully_at_zero_via_ajax()
    {
        $product = Product::factory()->create(['quantity' => 0]);

        $response = $this->postJson(route('products.decrease-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Quantity cannot go below 0.',
            'quantity' => 0
        ]);
        $this->assertEquals(0, $product->fresh()->quantity);
    }

    /** @test */
    public function test_controller_decrease_quantity_updates_status_to_out_of_stock_via_ajax()
    {
        $product = Product::factory()->create([
            'quantity' => 1,
            'status' => 'active'
        ]);

        $response = $this->postJson(route('products.decrease-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'quantity' => 0,
            'status' => 'out_of_stock'
        ]);
        $this->assertEquals(0, $product->fresh()->quantity);
        $this->assertEquals('out_of_stock', $product->fresh()->status);
    }

    /** @test */
    public function test_controller_increase_quantity_activates_product_via_ajax()
    {
        $product = Product::factory()->create([
            'quantity' => 0,
            'status' => 'out_of_stock'
        ]);

        $response = $this->postJson(route('products.increase-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'quantity' => 1,
            'status' => 'active'
        ]);
        $this->assertEquals(1, $product->fresh()->quantity);
        $this->assertEquals('active', $product->fresh()->status);
    }

    // Test regular HTTP responses for quantity routes (backward compatibility)
    /** @test */
    public function test_controller_can_increase_quantity_via_regular_http()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->post(route('products.increase-quantity', $product));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Quantity increased successfully!');
        $this->assertEquals(3, $product->fresh()->quantity);
    }

    /** @test */
    public function test_controller_can_decrease_quantity_via_regular_http()
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->post(route('products.decrease-quantity', $product));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Quantity decreased successfully!');
        $this->assertEquals(1, $product->fresh()->quantity);
    }

    /** @test */
    public function test_controller_decrease_quantity_fails_gracefully_at_zero_via_regular_http()
    {
        $product = Product::factory()->create(['quantity' => 0]);

        $response = $this->post(route('products.decrease-quantity', $product));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Quantity cannot go below 0.');
        $this->assertEquals(0, $product->fresh()->quantity);
    }

    // Test standard resource routes
    /** @test */
    public function test_index_method_returns_view_with_products()
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    /** @test */
    public function test_show_method_returns_view_with_product()
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', $product);
    }

    /** @test */
    public function test_create_method_returns_view()
    {
        $response = $this->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('products.create');
    }

    /** @test */
    public function test_store_method_creates_product_with_expiration_date()
    {
        $productData = [
            'name' => 'Test Product',
            'quantity' => 10,
            'description' => 'Test description',
            'expiration_date' => '2025-12-31',
            'status' => 'active'
        ];

        $response = $this->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product created successfully!');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'quantity' => 10,
            'description' => 'Test description',
            'status' => 'active'
        ]);
        
        $product = Product::first();
        $this->assertEquals('2025-12-31', $product->expiration_date->format('Y-m-d'));
    }

    /** @test */
    public function test_store_method_creates_product_without_expiration_date()
    {
        $productData = [
            'name' => 'Test Product',
            'quantity' => 10,
            'description' => 'Test description',
            'expiration_date' => null,
            'status' => 'active'
        ];

        $response = $this->post(route('products.store'), $productData);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product created successfully!');
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'quantity' => 10,
            'description' => 'Test description',
            'status' => 'active',
            'expiration_date' => null
        ]);
    }

    /** @test */
    public function test_store_method_validates_required_fields()
    {
        $response = $this->post(route('products.store'), [
            'name' => '',
            'quantity' => '',
            'status' => ''
        ]);

        $response->assertSessionHasErrors(['name', 'quantity', 'status']);
    }

    /** @test */
    public function test_edit_method_returns_view_with_product()
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product', $product);
    }

    /** @test */
    public function test_update_method_updates_product()
    {
        $product = Product::factory()->create(['name' => 'Old Name']);
        $updateData = [
            'name' => 'Updated Product',
            'quantity' => 15,
            'description' => 'Updated description',
            'expiration_date' => '2025-12-31',
            'status' => 'inactive'
        ];

        $response = $this->put(route('products.update', $product), $updateData);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product updated successfully!');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Updated Product',
            'quantity' => 15,
            'description' => 'Updated description',
            'status' => 'inactive'
        ]);
        
        $updatedProduct = Product::first();
        $this->assertEquals('2025-12-31', $updatedProduct->expiration_date->format('Y-m-d'));
    }

    /** @test */
    public function test_update_method_validates_required_fields()
    {
        $product = Product::factory()->create();

        $response = $this->put(route('products.update', $product), [
            'name' => '',
            'quantity' => '',
            'status' => ''
        ]);

        $response->assertSessionHasErrors(['name', 'quantity', 'status']);
    }

    /** @test */
    public function test_destroy_method_deletes_product()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product deleted successfully!');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function test_ajax_request_detection_works_correctly()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        // Test with AJAX header
        $ajaxResponse = $this->postJson(route('products.increase-quantity', $product), [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $ajaxResponse->assertJsonStructure([
            'success', 'message', 'quantity', 'status'
        ]);

        // Test without AJAX header (regular HTTP)
        $httpResponse = $this->post(route('products.increase-quantity', $product));
        $httpResponse->assertRedirect();
    }
}