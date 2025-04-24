<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Liquidación - OleoControl</title>
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

    <!-- Datos del Socio -->
    <div class="card">
        <div class="card-title">Datos del Socio</div>
        <div class="row">
            <span class="label">Fecha de solicitud: </span>
            <span class="value">{{ $settlement['settlement_date'] ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Nombre: </span>
            <span class="value">{{ $settlement['member']['name'] ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Número de socio: </span>
            <span class="value">{{ $settlement['member']['member_number'] ?? '-' }}</span>
        </div>
    </div>

    <!-- Datos del Empleado -->
    @if(isset($settlement['employee']))
    <div class="card">
        <div class="card-title">Datos del Empleado</div>
        <div class="row">
            <span class="label">Nombre: </span>
            <span class="value">{{ $settlement['employee']['name'] }}</span>
        </div>
        <div class="row">
            <span class="label">Fecha de resolución: </span>
            <span class="value">{{ $settlement['settlement_date_res'] ?? '-' }}</span>
        </div>
    </div>
    @endif

    <!-- Resultados de la Liquidación -->
    <div class="card">
        <div class="card-title">Resultado</div>
        <div class="row">
            <span class="label">Cantidad de aceite: </span>
            <span class="value">{{ $settlement['amount'] }} L</span>
        </div>
        <div class="row">
            <span class="label">Tipo de aceite: </span>
            <span class="value">{{ $settlement['oil']['name'] ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Precio por L: </span>
            <span class="value">{{ $settlement['price'] }} €</span>
        </div>
        <div class="row">
            <span class="label">Total liquidación: </span>
            <span class="value">{{ $settlement['amount'] * $settlement['price'] }} €</span>
        </div>
        <div class="row">
            <span class="label">Estado: </span>
            <span class="status-{{ strtolower($settlement['settlement_status']) }}">
                {{ $settlement['settlement_status'] }}
            </span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Generado automáticamente por el sistema de gestión de almazara OleoControl © {{ now()->year }}
    </div>
</body>
</html>
