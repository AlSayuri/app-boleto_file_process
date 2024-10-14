<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Boleto extends Model
{
  protected $table = 'boletos';
  protected $primaryKey = 'id';

  protected $fillable = [
    'name',
    'government_id',
    'email',
    'debt_amount',
    'debt_due_date',
    'debt_id',
    'created_at',
    'updated_at',
  ];

}
