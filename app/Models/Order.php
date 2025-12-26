<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='orders',$timestamps=false;
    protected $casts = [
        'invoice_amount' => 'integer',
    ];
    protected $fillable = [
        'seller_id',
        'warehouse_id',
        'product_id',
        'order_number',
        'customer_order_number',
        'order_type',
        'o_type',
        'b_customer_name',
        'b_customer_email',
        'b_address_line1',
        'b_address_line2',
        'b_city',
        'b_state',
        'b_country',
        'b_pincode',
        'b_contact_code',
        'b_contact',
        'delivery_address',
        'imported',
        'p_warehouse_name',
        'p_customer_name',
        'p_address_line1',
        'p_address_line2',
        'p_city',
        'p_state',
        'p_country',
        'p_pincode',
        'p_contact_code',
        'p_contact',
        'pickup_address',

        's_customer_name',
        's_address_line1',
        's_address_line2',
        's_city',
        's_state',
        's_country',
        's_pincode',
        's_contact_code',
        's_contact',

        'channel',
        'product_name',
        'product_sku',
        'weight',
        'length',
        'breadth',
        'height',
        'vol_weight',

        'c_weight',
        'c_length',
        'c_breadth',
        'c_height',
        's_charge',
        'c_charge',
        'shipping_charges',
        'cod_charges',
        'rto_charges',
        'total_charges',
        'early_cod_charges',
        'gst_charges',
        'excess_weight_charges',
        'discount',
        'invoice_amount',
        'zone',
        'cgst',
        'sgst',
        'igst',
        'courier_partner',
        'awb_number',
        'alternate_awb_number',
        'xb_token_number',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'status',
        'delivery_stutus',
        'escalation_status',
        'ndr_action',
        'ndr_status',
        'manifest_status',
        'rto_status',
        'pickup_time',
        'pickup_done',
        'pickup_schedule',
        'weight_disputed',
        'settled_weight_disputed',

        'time',
        'awb_assigned_date',
        'reason',
        'reason_for_cancel',
        'reason_for_ndr',
        'reason_for_delay',
        'awb_barcode',
        'orderno_barcode',
        'zone',
        'invoice_status',
        'expected_delivery_date',
        'delivered_date',
        'last_sync',
        'ndr_status_date',
        'cod_remmited',
        'reseller_name',
        'channel_id',
        'last_verified',
        'fulfillment_id',
        'location_id',
        'seller_channel_id',
        'seller_channel_name',
        'suggested_awb',
        'reference_code',
        'marketplace',
        'marketplace_id',
        'amazon_order_id',
        'product_qty',
        'manifest_sent',
        'amazon_label',
        'bluedart_label',
        'route_code',
        'shipment_type',
        'number_of_packets',
        'is_master',
        'parent_id',
        'master_id',
        'ewaybill_number',
        'last_executed',
        'is_tagged',
        'global_type',
        'same_as_rto',
        'rto_warehouse_id',
        'is_qc',
        'collectable_amount'
    ];

    public static function getAWBNumber($seller = 0){
        $response=[];
        if($seller == 0)
            $resp=DB::table('orders')->get();
        else
            $resp=DB::table('orders')->where('seller_id',$seller)->get();
        foreach($resp as $r)
            $response[$r->id]=$r->awb_number;
        return $response;
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product','order_id');
    }

    public function ndrattempts()
    {
        return $this->hasMany(Ndrattemps::class,'order_id')->where('action_by','!=','Twinnship')->where('action_status', '=', 'pending')->where('ndr_data_type','auto');
    }

    public function sellerNdrAction()
    {
        return $this->hasOne(Ndrattemps::class,'order_id')->where('action_by','Seller')->where('ndr_data_type','manual');
    }
    /**
     * Get the bluedart details.
     */
    public function bluedart_details() {
        return $this->hasOne(Bluedart_details::class);
    }

    /**
     * Get the warehouse details.
     */
    public function warehouse() {
        return $this->belongsTo(Warehouses::class);
    }

    /**
     * Get the seller details.
     */
    public function seller() {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the courier details.
     */
    public function courier() {
        return $this->belongsTo(Partners::class, 'courier_partner', 'keyword');
    }

    /**
     * Get the channel details.
     */
    public function seller_channel() {
        return $this->belongsTo(Channels::class, 'seller_channel_id', 'id');
    }

    /**
     * Get the tracking details.
     */
    public function tracking() {
        return $this->hasMany(OrderTracking::class, 'awb_number');
    }

    public function InternationalDetails()
    {
        return $this->hasOne(InternationalOrders::class,'order_id');
    }

    public function ofdDate()
    {
        return $this->hasOne(InternationalOrders::class,'order_id');
    }

    public function Intransittable()
    {
        return $this->hasOne(MoveToIntransit::class,'order_id');
    }

    public function onBoarded() {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }
}
