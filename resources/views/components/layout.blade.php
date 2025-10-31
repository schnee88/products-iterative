<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Products App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="holy-grail">
    <header class="hg-header">
        <h1>🛒 Products Store</h1>
    </header>

    <div class="hg-container">
        <nav class="hg-left-sidebar">
            {{ $navigation ?? '' }}
        </nav>

        <main class="hg-main">
            {{ $slot }}
        </main>

        <aside class="hg-right-sidebar">
            {{ $sidebar ?? '' }}
        </aside>
    </div>

    <footer class="hg-footer">
        <p>&copy; 2024 Products Iterative. Visas tiesības aizsargātas.</p>
    </footer>
</body>
</html>