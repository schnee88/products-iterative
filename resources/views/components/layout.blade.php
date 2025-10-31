<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: red !important;
        }
    </style>
</head>
<body>

    <x-flash-success />
    <x-flash-errors />
    
    <div class="holy-grail">
        <header class="header">
            <h1>MyStore Logo</h1>
        </header>

        <aside class="left-sidebar">
            <nav class="navigation">
                <ul>
                    <li><a href="{{ route('products.index') }}">All Products</a></li>
                    <li><a href="{{ route('products.create') }}">Create Product</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            {{ $slot }}
        </main>

        <aside class="right-sidebar">
            <div class="ad-section">
                <h3>Advertisement</h3>
                <p>Special Offer! Get 20% off on all products this week!</p>
            </div>
        </aside>

        <footer class="footer">
            <p>&copy; 2025 MyStore. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>