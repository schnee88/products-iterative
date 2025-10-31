<x-layout>
    <h1>Product Details</h1>

    {{-- Flash notifications --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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

            {{-- Increment button --}}
            <form action="{{ route('products.increment', $product) }}" method="POST">
    @csrf
    @method('PATCH')
    <button type="submit">+</button>
</form>

<form action="{{ route('products.decrement', $product) }}" method="POST">
    @csrf
    @method('PATCH')
    <button type="submit">-</button>
</form>
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
        <a href="{{ route('products.edit', $product) }}" class="btn-edit">Edit</a>
        <a href="{{ route('products.index') }}" class="btn-back">Back to List</a>
        
        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="btn-delete">Delete</button>
        </form>
    </div>

    {{-- Simple CSS for buttons and flash messages --}}
    <style>
        .btn-action {
            padding: 2px 6px;
            margin-left: 4px;
            cursor: pointer;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-actions a, .form-actions button {
            margin-right: 10px;
            text-decoration: none;
        }
        .form-actions .btn-delete {
            color: red;
            border: none;
            background: none;
            cursor: pointer;
        }
    </style>
</x-layout>
