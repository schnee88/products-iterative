<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'expiration_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,out_of_stock',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    // In app/Http/Controllers/ProductController.php

public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'expiration_date' => 'nullable|date',
        'status' => 'required|in:active,inactive,out_of_stock',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:255'
    ]);

    $product->update($validated);
    
    // Sync tags if they are provided
    if ($request->has('tags')) {
        $product->syncTags($request->tags);
    }

    return redirect()->route('products.index')
        ->with('success', 'Product updated successfully!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // In ProductController.php

public function increaseQuantity(Product $product)
{
    $product->increaseQuantity();
    
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Quantity increased successfully!',
            'quantity' => $product->fresh()->quantity,
            'status' => $product->fresh()->status
        ]);
    }
    
    return redirect()->back()->with('success', 'Quantity increased successfully!');
}

public function decreaseQuantity(Product $product)
{
    if ($product->decreaseQuantity()) {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Quantity decreased successfully!',
                'quantity' => $product->fresh()->quantity,
                'status' => $product->fresh()->status
            ]);
        }
        
        return redirect()->back()->with('success', 'Quantity decreased successfully!');
    }

    if (request()->ajax()) {
        return response()->json([
            'success' => false,
            'message' => 'Quantity cannot go below 0.',
            'quantity' => $product->fresh()->quantity
        ], 422);
    }
    
    return redirect()->back()->with('error', 'Quantity cannot go below 0.');
}

}