<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class BoletoService
{
    public function generateBoleto($boleto)
    {
        // Simulacao da geracao de um boleto, comentado o log pois estava demorando muito o processamento
        //Log::info("Boleto gerado para " .$boleto["name"]. " com o id " . $boleto["debt_id"]);
    }
}
