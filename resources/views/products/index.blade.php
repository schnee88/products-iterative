<x-layout>
    <h1>Products List</h1>

    <a href="{{ route('products.create') }}" class="btn-create">Create New Product</a>

    @if($products->isEmpty())
        <p>No products found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Expiration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="quantity-{{ $product->id }}">{{ $product->quantity }}</td>
                        <td class="status-{{ $product->id }}">{{ ucfirst(str_replace('_', ' ', $product->status)) }}</td>
                        <td>{{ $product->expiration_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('products.show', $product) }}">View</a>
                            <a href="{{ route('products.edit', $product) }}">Edit</a>

                            {{-- AJAX Increment button --}}
                            <button type="button" 
                                    class="btn-increase btn-action" 
                                    data-product-id="{{ $product->id }}"
                                    title="Increase Quantity">
                                +
                            </button>

                            {{-- AJAX Decrement button --}}
                            <button type="button" 
                                    class="btn-decrease btn-action" 
                                    data-product-id="{{ $product->id }}"
                                    title="Decrease Quantity">
                                -
                            </button>

                            {{-- Delete product --}}
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-layout>