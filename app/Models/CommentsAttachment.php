<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CommentsAttachment extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='comments_attachment',$timestamps=false;
    protected $fillable = [
        'ticket_comment_id',
        'attachment'
    ];
}
