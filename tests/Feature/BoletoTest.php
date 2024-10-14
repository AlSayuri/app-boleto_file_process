<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BoletoTest extends TestCase
{
    public function test_import_csv_100000()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_100000.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_100000.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }

    public function test_import_csv_1()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_1.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_1.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }

    public function test_import_csv_error()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_with_error.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_with_error.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }

    public function test_import_csv_with_duplicate_rows()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_duplicate.csv');


        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_duplicate.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }

    public function test_import_csv_duplicate_files()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_1.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_1.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_1.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_1.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input_100000.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input_100000.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }

    public function test_import_csv_total()
    {
        // Simula o sistema de arquivos local em testes
        Storage::fake('local');

        // Caminho do arquivo de teste na pasta tests/files
        $pathToTestFile = base_path('tests/File/input.csv');

        // Cria uma simulacao do arquivo para o upload
        $file = UploadedFile::fake()->createWithContent('input.csv', file_get_contents($pathToTestFile));

        // Faz a requisicao POST enviando o arquivo simulado
        $response = $this->post('/api/process-boleto', [
            'file' => $file,
        ]);

        // Verifica se a requisicao deu sucesso
        $response->assertStatus(200);
    }
}
