<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubArea extends Model
{
    protected $fillable=['area_id','nombre'];
    use HasFactory;
}
