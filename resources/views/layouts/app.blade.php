<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inversiones Danger 3000 C.A - Panel Cliente</title>
    @viteReactRefresh
    @vite(['resources/css/app.css','resources/js/whatsapp/main.jsx'])
    @php
        $u = auth()->user();
        $userData = ['name' => $u->name, 'email' => $u->email, 'initials' => strtoupper(substr($u->name, 0, 1))];
    @endphp
    <script>window.__USER__ = @json($userData);</script>
</head>
<body>
    <div id="whatsapp-root"></div>
</body>
</html>
