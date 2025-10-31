<x-layout title="Visi produkti" 
          :navigation="'
            <div class=\"nav-flex\">
                <a href=\"' . route('products.index') . '\" class=\"nav-item\">Visi produkti</a>
                <a href=\"' . route('products.create') . '\" class=\"nav-item\">Pievienot produktu</a>
            </div>
          '"
          :sidebar="'
            <h3>🔥 Īpašais piedāvājums!</h3>
            <p>Šonedēļ -20% uz visiem produktiem!</p>
          '">
    
    <div class="test-red">
        ✅ CSS tests: Šim tekstam vajadzētu būt sarkanam!
    </div>

    <h1>Produktu saraksts</h1>
    
    <a href="{{ route('products.create') }}" style="display: inline-block; padding: 10px 15px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 20px;">
        + Pievienot jaunu produktu
    </a>

    @if(session('success'))
        <div style="padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; gap: 1rem;">
        @foreach($products as $product)
            <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 8px; background: white;">
                <h3>{{ $product->name }}</h3>
                <p><strong>Daudzums:</strong> {{ $product->quantity }}</p>
                <p><strong>Statuss:</strong> {{ $product->status }}</p>
                <p><strong>Derīgs līdz:</strong> {{ $product->expiration_date }}</p>
                
                <div style="margin-top: 10px;">
                    <a href="{{ route('products.show', $product) }}" style="padding: 5px 10px; background: #3498db; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">Skatīt</a>
                    <a href="{{ route('products.edit', $product) }}" style="padding: 5px 10px; background: #f39c12; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">Rediģēt</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 3px; cursor: pointer;" onclick="return confirm('Vai tiešām vēlaties dzēst šo produktu?')">Dzēst</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>