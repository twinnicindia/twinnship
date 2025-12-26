<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderArchive extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='zz_archive_orders',$timestamps=false;
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
        'seller_order_type',
        'last_executed',
        'is_tagged',
        'global_type',
        'same_as_rto',
        'rto_warehouse_id',
        'ondc_seller_id',
        'is_alpha',
        'channel_name',
        'channel_code',
        'is_qc'
    ];


    public function Intransittable()
    {
        return $this->hasOne(MoveToIntransit::class,'order_id');
    }

    public function seller() {
        return $this->belongsTo(Seller::class);
    }
}
