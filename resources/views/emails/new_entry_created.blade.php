<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de recepción de aceituna</title>
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
            font-family: Arial, sans-serif;
            background-color: var(--color-olive-50);
            padding: 20px;
            color: var(--color-olive-900);
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--color-olive-100);
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: var(--color-olive-700);
            margin-bottom: 20px;
        }

        .content {
            font-size: 14px;
            line-height: 1.6;
            color: var(--color-olive-800);
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: var(--color-olive-600);
            margin-top: 30px;
        }

        .highlight {
            font-weight: bold;
            color: var(--color-olive-700);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Logo">
            <div class="title">Informe de recepción de aceituna</div>
        </div>

        <div class="content">
            <p>Hola {{ $entry['member']['name'] }},</p>

            <p>Te informamos de que se ha registrado una nueva entrada de aceituna en el sistema.</p>

            <p>
                <span class="highlight">Fecha de entrada:</span> {{ \Carbon\Carbon::parse($entry['entry_date'])->format('d/m/Y') }}<br>
                <span class="highlight">Cantidad de aceituna:</span> {{ number_format($entry['olive_quantity'], 2) }} kg
            </p>

            <p>Adjunto a este correo encontrarás el informe en formato PDF con todos los detalles.</p>

            <p>Gracias por utilizar nuestro sistema de gestión de almazara.</p>
        </div>

        <div class="footer">
            OleoControl © {{ now()->year }}<br>
            Este correo fue generado automáticamente, por favor no respondas directamente.
        </div>
    </div>
</body>
</html>
