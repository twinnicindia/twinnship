<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TicketAttachment extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='ticket_attachment',$timestamps=false;
    protected $fillable = [
        'ticket_id',
        'attachment'
    ];
}
