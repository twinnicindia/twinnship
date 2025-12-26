<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Partners extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='partners',$timestamps=false;
    protected $fillable = [
        'title',
        'keyword',
        'position',
        'image',
        'api_key',
        'other_key',
        'ship_url',
        'track_url',
        'status',
        'weight_initial',
        'extra_limit',
        'weight_category',
        'serviceability_check',
        'qc_enabled',
        'reverse_enabled'
    ];
    public static function getPartnerKeywordList(){
        $response=[];
        $resp=DB::table('partners')->get();
        foreach($resp as $r)
            $response[$r->keyword]=$r->title;
        return $response;
    }

    public static function getPartnerImage(){
        $response=[];
        $resp=DB::table('partners')->get();
        foreach($resp as $r)
            $response[$r->keyword]=$r->image;
        return $response;
    }

    public static function getPartnerIdList(){
        $response=[];
        $resp=DB::table('partners')->get();
        foreach($resp as $r)
            $response[$r->id]=$r->title;
        return $response;
    }
}
