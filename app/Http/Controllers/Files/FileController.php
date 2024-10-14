<?php

namespace App\Http\Controllers\Files;
use App\Http\Controllers\Controller;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use App\Models\Boleto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\BoletoService;
use App\Services\Notification;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function processBoletoCSV (Request $request)
    {
        // valida o request se existe o csv
        $validated =  Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'success'       => false,
                'message'       => $validated->messages()->toArray(),
                'status_code'   => 400
            ], 400);
        }

        $filePath = $request->file->getRealPath();

        $qtdBoletoProcessed = 0;

        // Abre o arquivo para leitura
        if (($csv = fopen($filePath, 'r')) !== false) {
            // obtem os cabeçalhos do CSV
            $header = fgetcsv($csv, 1000, ',');

            // valida o cabecalho
            // Verifica se os cabeçalhos foram obtidos
            if ($header === false) {
                return response()->json([
                    'success'       => false,
                    'message'       => "Erro ao ler os cabeçalhos do CSV.",
                    'status_code'   => 400
                ], 400);
            }

            $ExpectedColumns = ['name', 'governmentId', 'email', 'debtAmount', 'debtDueDate','debtId'];

            // Valida o cabeçalho
            if (!$this->validateHeader($header, $ExpectedColumns)) {
                return response()->json([
                    'success'       => false,
                    'message'       => "O arquivo CSV não contém todas as colunas necessárias.",
                    'status_code'   => 400
                ], 400);
            }

            $batchSize = 1000; // tamanho do lote para salvar no banco
            $batch = []; // array do lote
            $errors = []; // array de erros

            while (($row = fgetcsv($csv, 1000, ',')) !== false) {
                //processar linhas aqui
                $row_header = array_combine($header, $row);

                if(empty($row_header["name"])
                    || empty($row_header["governmentId"])
                    || empty($row_header["email"])
                    || (empty($row_header["debtAmount"]) || !is_numeric($row_header["debtAmount"]))
                    || (empty($row_header["debtDueDate"]) || !$this->validateDate($row_header["debtDueDate"]))
                    || empty($row_header["debtId"])){
                    // se o boleto estiver invalido continua o processamento dos outros
                    $errors[] = "Boleto com informações inválidas: " . $row_header["debtId"];
                    continue;
                }else{
                    $batch[] = [
                        'name'          => $row_header["name"],
                        'government_id' => $row_header["governmentId"],
                        'email'         => $row_header["email"],
                        'debt_amount'   => $row_header["debtAmount"],
                        'debt_due_date' => $row_header["debtDueDate"],
                        'debt_id'       => $row_header["debtId"]
                    ];

                    // Insere em lotes
                    if (count($batch) === $batchSize) {
                        $this->insertBatchBoleto($batch);
                        $batch = [];
                    }

                    $qtdBoletoProcessed ++;
                }
            }

            // Insere os boletos restantes
            if (!empty($batch)) {
                $this->insertBatchBoleto($batch);

                $qtdBoletoProcessed =+ count($batch);
            }

            $respose = [
                'success'       => true,
                'erros'         => $errors,
                'status_code'   => 200
            ];

            if(!empty($errors)){
                return response()->json([
                    'success'       => true,
                    'message'       => "Foram processados $qtdBoletoProcessed boletos com sucesso e " . count($errors). " erro(s)",
                    'erros'         => $errors,
                    'status_code'   => 400
                ], 400);
            }else{
                return response()->json([
                    'success'       => true,
                    'message'       => "Foram processados $qtdBoletoProcessed boletos.",
                    'status_code'   => 200
                ], 200);
            }
        }
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function validateHeader($header, $ExpectedColumns) {
        // Verifica se todas as colunas esperadas estão presentes no cabeçalho
        foreach ($ExpectedColumns as $column) {
            if (!in_array($column, $header)) {
                return false;
            }
        }
        return true;
    }
    function insertBatchBoleto($batch) {
        // insere no banco os boletos do csv
        $totalIinsert = Boleto::insertOrIgnore($batch);

        $id_boleto = array_column($batch, 'debt_id');

        if($totalIinsert !== count($batch)){
            // se a quantidade de insercao for diferete da quantidade do lote eh porque teve dados repetidos e nao foi
            // inserido boletos com o mesmo debt_id

            //busca no banco os boletos do lote
            $boletoSaved = Boleto::whereIn('debt_id', $id_boleto)->where('processed', 'N')->get();

            foreach ($boletoSaved as $boleto) {
                $boletoInfo = [
                    'name'          => $boleto['name'],
                    'governmentId'  => $boleto['government_id'],
                    'email'         => $boleto['email'],
                    'debtAmount'    => $boleto['debt_amount'],
                    'debtDueDate'   => $boleto['debt_due_date'],
                    'debtId'        => $boleto['debt_id']
                ];

                // Gera o boleto
                (new BoletoService())->generateBoleto($boletoInfo);
                // Envia o e-mail
                (new Notification())->notificationBoleto($boletoInfo);
            }
        }else{
            foreach ($batch as $row){
                //processar boleto
                $boletoInfo = [
                    'name'          => $row['name'],
                    'governmentId'  => $row['government_id'],
                    'email'         => $row['email'],
                    'debtAmount'    => $row['debt_amount'],
                    'debtDueDate'   => $row['debt_due_date'],
                    'debtId'        => $row['debt_id']
                ];

                // Gera o boleto
                (new BoletoService())->generateBoleto($boletoInfo);
                // Envia o e-mail
                (new Notification())->notificationBoleto( $boletoInfo);
            }
        }

        Boleto::whereIn('debt_id', $id_boleto)->update([
            'processed' => 'Y', // atualiza o estado para 'Y'
        ]);
    }
}
