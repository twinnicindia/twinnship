<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Admin_rights extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='admin_rights',$timestamps=false;
    protected $fillable = [
        'admin_id',
        'master_id',
        'ins',
        'del',
        'modi'
    ];
    public static function get_menus($id){
        $response=array();
        if(Session()->get('MyAdmin')->type=="user")
            $ans=DB::select("select m.* from master m,admin_rights ar where ar.master_id=m.id and ar.admin_id=$id and m.parent_id=0 and m.status='y' order by m.position");
        else
            $ans=DB::select('select * from master where parent_id=0 and status="y" order by `position`');
        $response['menu']=$ans;
        foreach ($ans as $a){
            if(Session()->get('MyAdmin')->type=="user")
                $response['submenu'][$a->id]=DB::select("select m.* from master m,admin_rights ar where ar.master_id=m.id and ar.admin_id=$id and m.parent_id=".$a->id." and m.status='y' order by m.position");
            else
                $response['submenu'][$a->id]=DB::select("select * from master where parent_id=".$a->id." and status='y' order by `position`");
        }
        return $response;
    }
    public static function get_parent_array(){
        $info=array();
        $res=DB::select("select * from master where parent_id=0");
        foreach ($res as $r)
            $info[$r->id]=$r->title;
        return $info;
    }
    public static function check_permission($url,$id){
        $resp=DB::select("select ar.* from admin_rights ar,master m,admin a where ar.master_id=m.id and ar.admin_id=a.id and m.link like '%$url' and ar.admin_id=$id and m.status='y'");
        return $resp;
    }
}
