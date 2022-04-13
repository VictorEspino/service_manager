<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicoPuesto extends Model
{
    protected $fillable=['topico_id','puesto_id'];
    use HasFactory;
}
