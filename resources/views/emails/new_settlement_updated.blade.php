<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación de Aceite</title>
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

        .status-accepted {
            font-weight: bold;
            color: green;
        }

        .status-cancelled {
            font-weight: bold;
            color: red;
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
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo" width="80px">
        </div>

        <h2>Liquidación de Aceite</h2>
        <p style="text-align: center;">Se ha actualizado el estado de la siguiente liquidación:</p>

        <div class="section">
            <h3>Información general</h3>
            <ul>
                <li><strong>Id de solicitud:</strong> {{ $settlement['id']}}</li>
                <li><strong>Fecha de solicitud:</strong> {{ \Carbon\Carbon::parse($settlement['settlement_date'])->format('d/m/Y') }}</li>
                <li><strong>Fecha de resolución:</strong> {{ \Carbon\Carbon::parse($settlement['settlement_date_res'])->format('d/m/Y') }}</li>
            </ul>
        </div>

        <div class="section">
            <h3>Datos del agricultor</h3>
            <ul>
                <li><strong>Nombre:</strong> {{ $settlement['member']['name'] }}</li>
                <li><strong>Número de socio:</strong> {{ $settlement['member']['member_number'] }}</li>
            </ul>
        </div>

        <div class="section">
            <h3>Detalles de la liquidación</h3>
            <ul>
                <li><strong>Empleado que la ha gestionado:</strong> {{ $settlement['employee']['name'] }}</li>
                <li><strong>Cantidad de aceite:</strong> {{ number_format($settlement['amount'], 2) }} L</li>
                <li><strong>Tipo de aceite:</strong> {{ $settlement['oil']['name'] }}</li>
                <li><strong>Precio por L:</strong> {{ number_format($settlement['price'], 2) }} €</li>
                <li><strong>Total de la liquidación:</strong> {{ number_format($settlement['amount'] * $settlement['price'], 2) }} €</li>
            </ul>
        </div>

        <div class="section">
            <h3>Estado de la liquidación</h3>
            <p class="{{ $settlement['settlement_status'] === 'Aceptada' ? 'status-accepted' : 'status-cancelled' }}">
                {{ $settlement['settlement_status'] }}
            </p>
        </div>

        <div class="footer">
            OleoControl © {{ now()->year }}<br>
            Este correo fue generado automáticamente, por favor no respondas directamente.
        </div>
    </div>
</body>
</html>
