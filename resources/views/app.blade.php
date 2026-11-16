<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#090a0c">
    <link rel="icon" type="image/svg+xml" sizes="any" href="{{ asset('favicon.svg') }}">
    @fonts
    <script>try{if(localStorage.getItem('mau-star-wars-theme')==='light'){document.documentElement.dataset.theme='light';document.querySelector('meta[name="theme-color"]')?.setAttribute('content','#f5f3ed')}}catch{}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-inertia::head />
</head>
<body>
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <x-inertia::app />
    <footer class="site-footer">
        <span>Star Wars · Archivo de personajes</span>
        <span>Que la Fuerza te acompañe.</span>
    </footer>
</body>
</html>
