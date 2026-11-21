<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalAlert extends Model
{
    protected $fillable = ['code', 'context'];

    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}
