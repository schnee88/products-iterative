<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function store(Request $request, Product $product)
    {
        \Log::info('Tag store method called', [
            'product_id' => $product->id,
            'request_data' => $request->all(),
            'ajax' => $request->ajax()
        ]);

        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            \Log::info('Validation passed');

            // Generate a random color for the tag
            $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1'];
            $randomColor = $colors[array_rand($colors)];

            // Find or create the tag
            $tag = Tag::firstOrCreate(
                ['name' => $request->name],
                ['color' => $randomColor]
            );

            \Log::info('Tag found/created', ['tag_id' => $tag->id]);

            // Attach tag to product (will ignore if already attached due to unique constraint)
            $product->tags()->syncWithoutDetaching([$tag->id]);

            \Log::info('Tag attached to product');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag added successfully!',
                    'tag' => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Tag added successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in TagController store method: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error adding tag: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, Tag $tag)
    {
        \Log::info('Tag destroy method called', [
            'product_id' => $product->id,
            'tag_id' => $tag->id
        ]);

        try {
            $product->tags()->detach($tag->id);

            \Log::info('Tag detached from product');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag removed successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Tag removed successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in TagController destroy method: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error removing tag: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        \Log::info('Tag search called', ['query' => $query]);

        try {
            $tags = Tag::where('name', 'like', "%{$query}%")
                      ->limit(10)
                      ->get()
                      ->map(function ($tag) {
                          return [
                              'id' => $tag->id,
                              'name' => $tag->name,
                              'color' => $tag->color
                          ];
                      });

            \Log::info('Tag search results', ['count' => $tags->count()]);

            return response()->json($tags);

        } catch (\Exception $e) {
            \Log::error('Error in TagController search method: ' . $e->getMessage());

            return response()->json([], 500);
        }
    }
}