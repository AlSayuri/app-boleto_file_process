<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Boleto;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BoletoTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_not_create_duplicate_boletos()
    {
      $boletoData = [
          'debt_id'       => (string) Str::uuid(),
          'name'          => 'John Doe',
          'government_id' => '123456789',
          'email'         => 'john.doe@example.com',
          'debt_amount'   => 100.00,
          'debt_due_date' => now()->addDays(30)->toDateString(),
      ];
      Boleto::create($boletoData);

      $this->expectException(QueryException::class);
      Boleto::create($boletoData);
    }
}
