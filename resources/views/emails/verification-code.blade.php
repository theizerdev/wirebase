<!DOCTYPE html>
<html>
<head>
    <title>Código de verificación</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            color: #333;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <h2>Verificación de correo electrónico</h2>
        </div>
        
        <p>Hola,</p>
        
        <p>Gracias por registrarte en {{ config('app.name') }}. Para completar el proceso de verificación de correo electrónico, por favor utiliza el siguiente código de verificación:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>Este código será válido por 30 minutos. Si no solicitaste este código, puedes ignorar este correo electrónico.</p>
        
        <div class="footer">
            <p>Atentamente,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>