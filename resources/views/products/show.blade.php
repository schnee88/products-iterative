<x-layout>
    <h1>Product Details</h1>

    <div class="product-details">
        <div class="detail-row">
            <strong>ID:</strong>
            <span>{{ $product->id }}</span>
        </div>

        <div class="detail-row">
            <strong>Name:</strong>
            <span>{{ $product->name }}</span>
        </div>

        <div class="detail-row">
            <strong>Quantity:</strong>
            <span>{{ $product->quantity }}</span>
        </div>

        <div class="detail-row">
            <strong>Description:</strong>
            <span>{{ $product->description ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <strong>Expiration Date:</strong>
            <span>{{ $product->expiration_date?->format('Y-m-d') ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <strong>Status:</strong>
            <span>{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
        </div>

        <div class="detail-row">
            <strong>Created:</strong>
            <span>{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
        </div>

        <div class="detail-row">
            <strong>Updated:</strong>
            <span>{{ $product->updated_at->format('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('products.edit', $product) }}">Edit</a>
        <a href="{{ route('products.index') }}">Back to List</a>
        
        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="btn-delete">Delete</button>
        </form>
    </div>
</x-layout>