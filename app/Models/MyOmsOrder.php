<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyOmsOrder extends Authenticatable {
    use HasFactory, Notifiable;
    public $table='my_oms_orders',$timestamps=false;
    protected $fillable = [
        'order_number',
        'customer_order_number',
        'seller_id',
        'warehouse_id',
        'order_type',
        'o_type',
        'b_customer_name',
        'b_customer_email',
        'b_address_line1',
        'b_address_line2',
        'b_country',
        'b_state',
        'b_city',
        'b_pincode',
        'b_contact',
        'b_contact_code',
        'p_warehouse_name',
        'p_customer_name',
        'p_address_line1',
        'p_address_line2',
        'p_country',
        'p_state',
        'p_city',
        'p_pincode',
        'p_contact',
        'p_contact_code',
        's_customer_name',
        's_address_line1',
        's_address_line2',
        's_country',
        's_state',
        's_city',
        's_pincode',
        's_contact',
        's_contact_code',
        'weight',
        'length',
        'breadth',
        'height',
        'vol_weight',
        'invoice_amount',
        'courier_partner',
        'awb_number',
        'awb_assigned_date',
        'awb_barcode',
        'channel',
        'inserted',
        'inserted_by',
        'modified',
        'modified_by',
        'product_name',
        'product_sku',
        'pickup_address',
        'delivery_address',
        'status',
        'orderno_barcode',
        'reseller_name',
        'product_qty',
        'shipment_type',
        'number_of_packets',
        'is_master',
        'parent_id',
        'master_id',
        'route_code',
        'ewaybill_number',
    ];

    public function products() {
        return $this->hasMany(MyOmsProduct::class, 'order_id');
    }
}
