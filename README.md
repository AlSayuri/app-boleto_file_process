# Sistema de API que faz upload de arquivos CSV que contém boletos para serem gerados e enviar um e-mail de notificação.
- Fluxo:
    - A função recebe um arquivo com validação de tipo de arquivo, aceitando apenas CSV
    - Lê o arquivo e verifica se existe os cabeçalhos obrigatórios (name, governmentId, email, debtAmount, debtDueDate, debtId)
    - Para cada linha é verificado se existe as informações obrigatórias e se está no formato correto
        - Caso esteja no formato certo é processado
        - Caso não seja válido é ignorado e continua para a próxima linha
    - Se caso o boleto já tiver sido processado anteriormente ele é ignorado e não será feito um novo processamento
        - Para garantir a idempotencia dos boletos é utilizado o debtId, que está presente no arquivo. No banco esse campo está como unique e o na hora de inerir no banco é utilizado o "insertOrIgnore" que ignora quando tiver conflito de dados duplicados. 
  
## Requisitos
- PHP 8.3
- Laravel 11

## Como Rodar a aplicação
1. Clone o repositório.
2. Execute `composer install`.
3. Rode o docker compose com `docker-compose up -d --build`
4. Gere uma application key com `php artisan key:generate`
5. Execute as migrações com `php artisan migrate`
6. Inicie o servidor de desenvolvimento com `php artisan serve`

## Como usar
1. Para importar boletos, envie um arquivo CSV via POST para a rota `/api/process-bolet`

## Funções
- processBoletoCSV em Controller > Files > FileController
    Essa função é o principa, onde recebe o arquivo e processa
- generateBoleto em app > Services > BoletoService
    Essa função é uma classe abstrata que simula a parte de geração de boletos, ela cria uma mensagem de log quando é acessada
- notificationBoleto em app > Services > Notification
    Essa função é uma classe abstrata que simula a parte de enviar e-mais, ela cria uma mensagem de log quando é acessada
    
## Testes (estão no arquivo BoletoTest, em test > Features)
- test_import_csv_100000
    - O arquivo utilizado possui 100000 registros
    - `php artisan test --filter BoletoTest::test_import_csv_100000`
- test_import_csv_1
    - O arquivo utilizado possui 1 registro
    - `php artisan test --filter BoletoTest::test_import_csv_1`
- test_import_csv_error
    - O arquivo possui campos de data e valor não formatado, esses não serão gerado os boletos, mas outros outros irão
    - `php artisan test --filter BoletoTest::test_import_csv_error`
- test_import_csv_with_duplicate_rows
    - O arquivo possui IDs de boletos iguals, os boletos que já foram gerandos não irão ser processado novamente
    - `php artisan test --filter BoletoTest::test_import_csv_with_duplicate_rows`
- test_import_csv_duplicate_files
    - Esse teste fará duas requisições com o mesmo arquivo, mas apenas o primeiro irá ser processado os boletos
    - `php artisan test --filter BoletoTest::test_import_csv_with_duplicate_rows`
- test_import_csv_total
    - O arquivo original disponibilizado no teste, com 1100000 registros
    - `php artisan test --filter BoletoTest::test_import_csv_total`
