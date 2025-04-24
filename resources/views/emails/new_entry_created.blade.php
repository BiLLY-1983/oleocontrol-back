<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Entrada de Aceituna</title>
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
            text-align: center;
        }

        ul {
            background-color: var(--color-olive-200);
            padding: 1rem;
            border-radius: 8px;
            color: var(--color-olive-800);
        }

        ul li {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .btn-view {
            display: inline-block;
            background-color: var(--color-olive-500);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .btn-view:hover {
            background-color: var(--color-olive-600);
        }

        .footer {
            font-size: 0.9rem;
            color: var(--color-olive-600);
            margin-top: 2rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px;">
        </div>
        <h2>Nueva entrada registrada</h2>
        <p>Se ha registrado una nueva entrada de aceituna con los siguientes datos:</p>

        <ul>
            <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($entry['entry_date'])->format('d/m/Y H:i') }}</li>
            <li><strong>Agricultor:</strong> {{ $entry['member']['name'] }} (Socio Nº {{ $entry['member']['member_number'] }})</li>
            <li><strong>Kilos de aceituna:</strong> {{ number_format($entry['olive_quantity'], 2) }} kg</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ url('https://oleocontrol-front.alwaysdata.net/members/' . $entry['member']['id'] . '/entries/' . $entry['id']) }}" class="btn-view">Ver entrada</a>
        </p>

        <div class="footer">
            <p>— Sistema de Gestión de Almazara</p>
        </div>
    </div>
</body>

</html>