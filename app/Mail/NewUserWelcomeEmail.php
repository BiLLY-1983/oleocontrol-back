<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Clase que representa un correo de bienvenida para nuevos usuarios.
 *
 * Este correo se envía automáticamente cuando un nuevo usuario es creado en el sistema, dándole la bienvenida 
 * y proporcionando sus credenciales de acceso iniciales.
 * 
 * @package App\Mail
 */
class NewUserWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Nombre del nuevo usuario.
     *
     * @var string
     */
    public string $username;

    /**
     * Contraseña generada para el nuevo usuario.
     *
     * @var string
     */
    public string $temporaryPassword;

    /**
     * Constructor que inicializa el correo con los datos necesarios.
     *
     * @param string $username Nombre del nuevo usuario.
     * @param string $temporaryPassword Contraseña asignada temporalmente.
     */
    public function __construct(string $username, string $temporaryPassword)
    {
        $this->username = $username;
        $this->temporaryPassword = $temporaryPassword;

        $this->html($this->buildHtmlContent());
    }

    /**
     * Genera el contenido HTML del correo de bienvenida.
     *
     * @return string HTML del correo.
     */
    public function buildHtmlContent(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido al sistema</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 2rem;
            color: #333;
        }

        .container {
            background: #ffffff;
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .credentials {
            background: #f7f7f7;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .credentials p {
            margin: 0.5rem 0;
            font-weight: bold;
        }

        h2 {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>¡Bienvenido, {$this->username}!</h2>
        <p>Nos alegra que te unas a nuestra plataforma. A continuación, te proporcionamos tus credenciales de acceso iniciales:</p>
        <div class="credentials">
            <p>Usuario: {$this->username}</p>
            <p>Contraseña temporal: {$this->temporaryPassword}</p>
        </div>
        <p>Te recomendamos cambiar la contraseña en cuanto accedas al sistema por primera vez.</p>
        <p>Si tienes alguna duda o necesitas ayuda, no dudes en ponerte en contacto con nuestro equipo de soporte.</p>
        <p>¡Gracias por confiar en nosotros!</p>
        <p><strong>El equipo de soporte</strong></p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Define los archivos adjuntos que se incluirán en el correo.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
