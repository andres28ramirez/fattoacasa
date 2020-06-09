<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CPCMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Notificación de Pago';

    public $venta;

    public function __construct($venta)
    {
        //
        $this->venta = $venta;
    }

    public function build()
    {
        return $this->view('mails.CPCMail')->with(['venta' => $this->venta]);    
    }
}
