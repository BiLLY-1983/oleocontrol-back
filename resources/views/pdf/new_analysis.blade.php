<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Análisis</title>
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
            font-family: 'Roboto', sans-serif;
            background-color: var(--color-olive-100);
            padding: 40px;
            color: var(--color-olive-900);
        }

        .container {
            background-color: var(--color-olive-50);
            border-radius: 12px;
            padding: 20px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }

        h2 {
            color: var(--color-olive-700);
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 8px;
        }

        .row {
            margin-bottom: 6px;
        }

        .label {
            font-weight: bold;
            color: #374151;
        }

        .value {
            color: #1f2937;
        }

        .oil-highlight {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-olive-700);
            background-color: var(--color-olive-300);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>

        <h2>Informe de Análisis</h2>

        <!-- Datos del Socio -->
        <div class="card">
            <div class="card-title">Datos del Socio</div>
            <div class="row">
                <span class="label">Nombre:</span>
                <span class="value">{{ $analysis['member']['name'] ?? '-' }}</span>
            </div>
            <div class="row">
                <span class="label">Número de socio:</span>
                <span class="value">{{ $analysis['member']['member_number'] ?? '-' }}</span>
            </div>
        </div>

        <!-- Datos del Empleado -->
        @if(isset($analysis['employee']))
            <div class="card">
                <div class="card-title">Datos del Empleado</div>
                <div class="row">
                    <span class="label">Nombre:</span>
                    <span class="value">{{ $analysis['employee']['name'] }}</span>
                </div>
                <div class="row">
                    <span class="label">Fecha del análisis:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($analysis['analysis_date'])->format('d/m/Y') }}</span>
                </div>
            </div>
        @endif

        <!-- Datos del Análisis -->
        <div class="card">
            <div class="card-title">Resultados del Análisis</div>
            <div class="row">
                <span class="label">Cantidad de aceituna:</span>
                <span class="value">{{ number_format($analysis['entry']['olive_quantity'], 2) }} kg</span>
            </div>
            <div class="row">
                <span class="label">Acidez:</span>
                <span class="value">{{ number_format($analysis['acidity'], 2) }} %</span>
            </div>
            <div class="row">
                <span class="label">Humedad:</span>
                <span class="value">{{ number_format($analysis['humidity'], 2) }} %</span>
            </div>
            <div class="row">
                <span class="label">Rendimiento:</span>
                <span class="value">{{ number_format($analysis['yield'], 2) }} %</span>
            </div>
            <div class="row">
                <span class="label">Cantidad estimada de aceite:</span>
                <span class="value">{{ number_format(($analysis['entry']['olive_quantity'] * $analysis['yield']) / 100, 2) }} L</span>
            </div>
        </div>

        <!-- Aceite Resultante -->
        <div class="oil-highlight">
            Aceite resultante: {{ $analysis['oil']['name'] ?? '-' }}
        </div>

        <!-- Footer -->
        <div class="footer">
            OleoControl © {{ now()->year }}<br>
            Este documento fue generado automáticamente, por favor no responda.
        </div>
    </div>
</body>
</html>
