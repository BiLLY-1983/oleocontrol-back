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
    }

    /**
     * Genera el contenido HTML del correo electrónico.
     *
     * Esta función construye el cuerpo del correo en formato HTML, que incluye el nombre del usuario y su nueva contraseña.
     * 
     * @return string El contenido HTML del correo electrónico.
     */
    public function build()
    {
        return $this->subject('Restablecimiento de contraseña')
            ->view('emails.password_reset')
            ->with([
                'username' => $this->username,
                'newPassword' => $this->newPassword,
            ]);
    }
}
