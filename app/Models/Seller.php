<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='sellers',$timestamps=false;
    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'company_name',
        'password',
        'balance',
        'onhold_balance',
        'cod_balance',
        'last_remitted',
        'basic_information',
        'account_information',
        'kyc_information',
        'agreement_information',
        'warehouse_status',
        'created_at',
        'modified_at',
        'registered_ip',
        'status',
        'verified',
        'google_id',
        'creted_by',
        'profile_image',
        'plan_id',
        'gst_certificate_status',
        'cheque_status',
        'document_status',
        'agreement_status',
        'rto_charge',
        'early_cod_charge',
        'reconciliation_days',
        'courier_priority_1',
        'courier_priority_2',
        'courier_priority_3',
        'courier_priority_4',
        'invoice_date',
        'api_key',
        'reverse_charge',
        'full_label_display',
        'sms_service',
        'pincode_editable',
        'essentials',
        'easyecom_token',
        'display_invoice',
        'remmitance_days',
        'zone_type',
        'seller_order_type',
        'seller_order_type_updated_at',
        'zone_type_updated_at',
        'employee_flag_enabled',
        'webhook_enabled',
        'webhook_url',
        'is_alpha',
        'is_alpha_delhivery',
        'is_international',
        'iec_code',
        'ad_code',
        'product_name_as_sku',
        'onboarded_by',
        'cheapest_enabled',
        'remittance_frequency',
        'remittanceWeekDay',
        'modified_by',
        'auto_reassign_enabled',
        'duplicate_order_number_flag',
        'brand_tracking_enabled',
        'whatsapp_service',
        'auto_cancellation_flag',
        'created_by',
        'whatsapp_charges',
        'sms_charges',
        'shopify_tag_flag_enabled',
        'is_cod_amount_visibility',
        'is_bulk_ship_running',
        'display_mis_zone',
        'migration_enabled',
        'is_migrated',
        'migrated_datetime',
        'order_fetch_datetime',
        'merge_order_number',
        'gst_percentage',
        'collectable_amount'
    ];

    /**
     * Get the seller details.
     */
    public function basic_info() {
        return $this->hasOne(Basic_informations::class);
    }

    static function getSeller($empId){
        $resp = self::where('onboarded_by',$empId)->get();
        $data = [];
        foreach ($resp as $r){
            $data[] = $r->id;
        }
        return $data;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }
}


