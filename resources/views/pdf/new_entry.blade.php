<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de recepción de aceituna</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #1f2937;
            margin-bottom: 30px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
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

        .status-aceptada {
            font-size: 18px;
            color: green;
        }

        .status-cancelada {
            font-size: 18px;
            color: red;
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
    <div class="logo-container">
        <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Logo">
    </div>
    <h1 class="title">Informe de Liquidación</h1>

    <div class="card">
        <div class="card-title">Fecha de entrada</div>
        <div class="row">
            <span class="value">{{ \Carbon\Carbon::parse($entry['entry_date'])->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Datos del Socio</div>
        <div class="row">
            <span class="label">Nombre: </span>
            <span class="value">{{ $entry['member']['name'] ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Número de socio: </span>
            <span class="value">{{ $entry['member']['member_number'] ?? '-' }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Resultados</div>
        <div class="row">
            <span class="label">Cantidad de aceituna: </span>
            <span class="value">{{ number_format($entry['olive_quantity'], 2) }} kg</span>
        </div>
    </div>

    <div class="footer">
        Generado automáticamente por el sistema de gestión de almazara<br>
        OleoControl © {{ now()->year }}
    </div>
</body>
</html>
