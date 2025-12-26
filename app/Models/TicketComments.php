<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TicketComments extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ticket_comments',$timestamps=false;
    protected $fillable = [
        'ticket_id',
        'replied_by',
        'remark',
        'replied'
    ];
}
