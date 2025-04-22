<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Clase que representa un correo electrónico para el restablecimiento de contraseña.
 *
 * Esta clase es responsable de generar el contenido HTML de un correo electrónico que se envía al usuario 
 * cuando su contraseña ha sido restablecida correctamente. Incluye el nombre de usuario y la nueva contraseña.
 * 
 * @package App\Mail
 */
class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Nombre de usuario del destinatario.
     *
     * @var string
     */
    public string $username;

    /**
     * Nueva contraseña asignada al usuario.
     *
     * @var string
     */
    public string $newPassword;

    /**
     * Constructor para inicializar los datos necesarios para el correo electrónico.
     *
     * @param string $username El nombre de usuario del destinatario.
     * @param string $newPassword La nueva contraseña que se asigna al usuario.
     */
    public function __construct(string $username, string $newPassword)
    {
        $this->username = $username;
        $this->newPassword = $newPassword;

        // Establecer el HTML directamente
        $this->html($this->buildHtmlContent());
    }

    /**
     * Genera el contenido HTML del correo electrónico.
     *
     * Esta función construye el cuerpo del correo en formato HTML, que incluye el nombre del usuario y su nueva contraseña.
     * 
     * @return string El contenido HTML del correo electrónico.
     */
    public function buildHtmlContent(): string
    {
        return <<<HTML
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
        <h2>Hola {$this->username},</h2>
        <p>Tu contraseña ha sido restablecida correctamente.</p>
        <p>Tu nueva contraseña es:</p>
        <div class="password">{$this->newPassword}</div>
        <p>Te recomendamos cambiarla una vez accedas a tu cuenta.</p>
        <p>Un saludo,<br><strong>El equipo de soporte</strong></p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Define los archivos adjuntos que se incluirán en el correo electrónico.
     *
     * En este caso, no se adjuntan archivos, por lo que el array está vacío.
     * 
     * @return array Un array de los archivos adjuntos (vacío en este caso).
     */
    public function attachments(): array
    {
        return [];
    }
}
