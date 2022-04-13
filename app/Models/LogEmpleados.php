<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEmpleados extends Model
{
    protected $fillable=['carga_id','mensaje'];
    use HasFactory;
}
