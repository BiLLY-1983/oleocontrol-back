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
            max-width: 700px;
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
            margin-bottom: 1.5rem;
        }

        .section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--color-olive-200);
            border-radius: 8px;
        }

        .section h3 {
            margin-top: 0;
            color: var(--color-olive-800);
            border-bottom: 1px solid var(--color-olive-300);
            padding-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .section li {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .oil-highlight {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-olive-700);
            background-color: var(--color-olive-300);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
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
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>

        <h2>Análisis de Entrada Nº {{ $analysis['entry']['entry_id'] }}</h2>
        <p style="text-align: center;">Se ha registrado el análisis de la siguiente entrada:</p>

        <div class="section">
            <h3>Información general</h3>
            <ul>
                <li><strong>Fecha del análisis:</strong> {{ \Carbon\Carbon::parse($analysis['analysis_date'])->format('d/m/Y') }}</li>
                <li><strong>Empleado:</strong> {{ $analysis['employee']['name'] }}</li>
            </ul>
        </div>

        <div class="section">
            <h3>Datos de la entrada</h3>
            <ul>
                <li><strong>ID de entrada:</strong> {{ $analysis['entry']['entry_id'] }}</li>
                <li><strong>Agricultor:</strong> {{ $analysis['member']['name'] }} (Socio Nº {{ $analysis['member']['member_number'] }})</li>
                <li><strong>Kilos de aceituna:</strong> {{ number_format($analysis['entry']['olive_quantity'], 2) }} kg</li>
            </ul>
        </div>

        <div class="section">
            <h3>Datos del análisis</h3>
            <ul>
                <li><strong>Acidez:</strong> {{ number_format($analysis['acidity'], 2) }} %</li>
                <li><strong>Humedad:</strong> {{ number_format($analysis['humidity'], 2) }} %</li>
                <li><strong>Rendimiento:</strong> {{ number_format($analysis['yield'], 2) }} %</li>
            </ul>
        </div>

        <div class="oil-highlight">
            Aceite resultante: {{ $analysis['oil']['name'] }}
        </div>

        <div class="footer">
            OleoControl © {{ now()->year }}<br>
            Este correo fue generado automáticamente, por favor no respondas directamente.
        </div>
    </div>
</body>
</html>
