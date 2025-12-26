<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin_employee extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='admin_employee',$timestamps=true;
    protected $fillable = [
        'admin_id',
        'name',
        'email',
        'mobile',
        'password',
        'image',
        'seller_ids',
        'status',
        'department',
        'created_at',
        'modified_at',
    ];

    public static function get_menus() {
        return [
            "menu" => [
                (object) [
                    "id" => 1,
                    "title" => "Sellers",
                    "text" => null,
                    "link" => "employee/seller",
                    "icon" => "fa fa-users",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 1,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 2,
                    "title" => "Escallation",
                    "text" => null,
                    "link" => "employee/escallation",
                    "icon" => "fa fa-th",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 2,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 3,
                    "title" => "Orders",
                    "text" => null,
                    "link" => "employee/order",
                    "icon" => "fa fa-th",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 3,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 4,
                    "title" => "COD Remmitance",
                    "text" => null,
                    "link" => "employee/cod-remmitance",
                    "icon" => "fa fa-th",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 4,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 5,
                    "title" => "Weight Reconciliation",
                    "text" => null,
                    "link" => "employee/weight-reconciliation",
                    "icon" => "fa fa-th",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 5,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 6,
                    "title" => "Pickup Addresses",
                    "text" => null,
                    "link" => "employee/pickup-address",
                    "icon" => "fa fa-th",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 6,
                    "parent_id" => 0,
                ],
                (object) [
                    "id" => 7,
                    "title" => "Profile",
                    "text" => null,
                    "link" => "employee-profile",
                    "icon" => "fa fa-user",
                    "status" => "y",
                    "inserted_by" => null,
                    "inserted" => "2020-11-23 19:42:37",
                    "modified" => "2020-11-23 19:44:03",
                    "modified_by" => null,
                    "position" => 7,
                    "parent_id" => 0,
                ]
            ],
            "submenu" => [
                1 => [
                    // (object) [
                    //     "id" => 2,
                    //     "title" => "All Admins",
                    //     "text" => null,
                    //     "link" => "admin",
                    //     "icon" => null,
                    //     "status" => "y",
                    //     "inserted_by" => null,
                    //     "inserted" => "2020-11-23 21:32:02",
                    //     "modified" => null,
                    //     "modified_by" => null,
                    //     "position" => 1,
                    //     "parent_id" => 1,
                    // ]
                ],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => [],
                7 => [],
            ],
        ];
    }
}
