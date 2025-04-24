<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAnalysisUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public array $analysis;
    public string $pdf;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(array $analysis, string $pdf)
    {
        $this->analysis = $analysis;
        $this->pdf = $pdf;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        return $this->subject('Nuevo informe de análisis disponible')
            ->view('emails.new_analysis_updated')
            ->with([
                'entry' => $this->analysis,
            ])
            ->attachData($this->pdf, 'informe-analisis.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
