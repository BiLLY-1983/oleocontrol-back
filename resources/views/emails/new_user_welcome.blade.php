<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido al sistema</title>
    <style>
        :root {
            --color-olive-50: #f8f9f3;
            --color-olive-100: #eef0e5;
            --color-olive-200: #dde2cc;
            --color-olive-300: #c3cda6;
            --color-olive-400: #a5b47c;
            --color-olive-500: #8a9c5d;
            --color-olive-600: #6b7c46;
            --color-olive-700: #556339;
            --color-olive-800: #475231;
            --color-olive-900: #3d462c;
            --color-olive-950: #1f2615;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--color-olive-100);
            padding: 2rem;
            color: var(--color-olive-900);
        }

        .container {
            background-color: var(--color-olive-50);
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        h2 {
            color: var(--color-olive-700);
        }

        .credentials {
            background-color: var(--color-olive-200);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            color: var(--color-olive-800);
        }

        .credentials p {
            font-weight: bold;
            margin: 0.5rem 0;
        }

        .btn-login {
            display: inline-block;
            background-color: var(--color-olive-500);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: var(--color-olive-600);
        }

        .footer {
            font-size: 0.9rem;
            color: var(--color-olive-600);
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px;">
        </div>
        <h2>¡Bienvenido, {{ $full_name }}!</h2>
        <p>Gracias por unirte a nuestra plataforma. A continuación te proporcionamos tus credenciales de acceso iniciales:</p>
        <div class="credentials">
            <p>Usuario: {{ $username }}</p>
            <p>Contraseña temporal: {{ $temporaryPassword }}</p>
        </div>
        <p>Por favor, cambia tu contraseña tras tu primer acceso.</p>
        <p>Para comenzar, puedes iniciar sesión desde el siguiente botón:</p>
        <p style="text-align: center;">
            <a href="{{ url('/login') }}" class="btn-login">Iniciar sesión</a>
        </p>
        <p>Si necesitas asistencia, no dudes en contactar con nuestro equipo de soporte.</p>
        <div class="footer">
            <p>— El equipo de soporte</p>
        </div>
    </div>
</body>
</html>
