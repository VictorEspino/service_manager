<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoTicket extends Model
{
    use HasFactory;

    protected $fillable=['ticket_id','user_id','actividad_id','area_id','subarea_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class,'area_id');
    }
    public function subarea()
    {
        return $this->belongsTo(SubArea::class,'subarea_id');
    }
}
