<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEntryCreated extends Mailable
{
    use Queueable, SerializesModels;

    public array $entry;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(array $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        return $this->subject('Nueva entrada de aceituna registrada')
            ->view('emails.new_entry_created')
            ->with([
                'entry' => $this->entry,
            ]);
    }
}
