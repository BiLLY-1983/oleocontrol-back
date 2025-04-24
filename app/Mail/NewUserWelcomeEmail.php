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
     * Nombre completo del nuevo usuario.
     *
     * @var string
     */
    public string $full_name;

    /**
     * Nombre de usuario del nuevo usuario.
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
    public function __construct(string $full_name, string $username, string $temporaryPassword)
    {
        $this->full_name = $full_name;
        $this->username = $username;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Genera el contenido HTML del correo de bienvenida.
     *
     * @return view HTML del correo.
     */
    public function build()
    {
        return $this->subject('Bienvenido al sistema')
            ->view('emails.new_user_welcome')
            ->with([
                'full_name' => $this->full_name,
                'username' => $this->username,
                'temporaryPassword' => $this->temporaryPassword,
            ]);
    }
}
