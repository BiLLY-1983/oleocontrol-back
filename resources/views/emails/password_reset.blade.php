<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecimiento de contraseña</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 2rem;
            color: #333;
        }

        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .password {
            font-size: 1.5rem;
            color: #111;
            background: #f3f3f3;
            padding: 1rem;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        p {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola {{ $username }},</h2>
        <p>Tu contraseña ha sido restablecida correctamente.</p>
        <p>Tu nueva contraseña es:</p>
        <div class="password">{{ $newPassword }}</div>
        <p>Te recomendamos cambiarla una vez accedas a tu cuenta.</p>
        <p>Un saludo,<br><strong>El equipo de soporte</strong></p>
    </div>
</body>
</html>
