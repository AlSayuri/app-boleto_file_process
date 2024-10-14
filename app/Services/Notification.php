<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class Notification
{
    public function notificationBoleto($boleto)
    {
        // Simulação do envio de e-mail, comentado o log pois estava demorando muito o processamento
        //Log::info( "E-mail enviado para " . $boleto["mail"]);
    }
}
