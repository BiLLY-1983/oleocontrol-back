<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSettlementUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public array $settlement;
    public string $pdf;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(array $settlement, string $pdf)
    {
        $this->settlement = $settlement;
        $this->pdf = $pdf;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        return $this->subject('Actualización de la liquidación solicitada')
            ->view('emails.new_settlement_updated')
            ->with(['settlement' => $this->settlement])
            ->attachData($this->pdf, 'informe-liquidacion.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
