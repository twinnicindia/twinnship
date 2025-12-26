<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupportTicket extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='support_ticket',$timestamps=false;
    protected $fillable = [
        'seller_id',
        'ticket_no',
        'type',
        'subject',
        'description',
        'issue',
        'awb_number',
        'raised',
        'last_replied',
        'status',
        'escalate_reason',
        'escalate_image',
        'sevierity'
    ];
    public function support_tickets()
    {
        return $this->hasMany('App\Models\TicketComments','ticket_id');
    }
}
