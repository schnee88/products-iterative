<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: red !important;
        }
        
        /* AJAX Button Styles */
        .btn-action {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            cursor: pointer;
            border-radius: 3px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-action:hover {
            background: #e9ecef;
        }

        .btn-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-increase {
            color: #28a745;
            border-color: #28a745;
        }

        .btn-decrease {
            color: #dc3545;
            border-color: #dc3545;
        }

        /* Flash message styles for AJAX */
        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .flash-success {
            background-color: #28a745;
        }

        .flash-error {
            background-color: #dc3545;
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