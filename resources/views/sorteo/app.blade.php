<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inversiones Danger 3000 C.A - Sorteo Interactivo</title>
    @viteReactRefresh
    @vite(['resources/js/sorteo/main.jsx'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        body { margin: 0; padding: 0; overflow: hidden; }
    </style>
</head>
<body>
    <div id="sorteo-root"></div>
</body>
</html>
