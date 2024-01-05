<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiAuth extends Model
{
    use HasFactory;

    protected $table = "api_auth";
    protected $primaryKey = "id";
}
