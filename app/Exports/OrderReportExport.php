<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class OrderReportExport implements FromCollection, WithHeadings {
    /**
     * Filter criteria
     * 
     * @var array
     */
    protected $filter;

    public function __construct(array $filter = []) {
        $this->filter = $filter;
    }

    /**
     * Set export header
     * 
     * @return array
     */
    public function headings(): array {
        return [
            'Pickup State',
            'Pickup City',
            'Pickup Pincode',
            'Seller',
            'Seller Code',
            'Pickup Date',
            'Shipping Date',
            'Delivered Date',
            'Order Id',
            'AWB Number',
            'Courier Partner',
            'Payment Type',
            'Ship Pincode',
            'Ship State',
            'Ship City',
            'Zone',
            'Invoice Amount',
            'Billing Weight (Kg)',
            'Dimension(L * H * H)(CM)',
            'Prouct Name',
            'Current Status',
            'Collectable Value',
            'Freight Charges',
            'RTO Charges',
            'COD Charges',
            'Total Charges'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $orders = DB::table('orders')
        ->join('sellers', 'orders.seller_id', '=', 'sellers.id')
        ->select(
            "orders.p_state",
            "orders.p_city",
            "orders.p_pincode",
            'sellers.company_name',
            'sellers.code',
            "orders.pickup_time",
            "orders.awb_assigned_date",
            "orders.delivered_date",
            "orders.customer_order_number",
            "orders.awb_number",
            "orders.courier_partner",
            "orders.order_type",
            "orders.s_pincode",
            "orders.s_state",
            "orders.s_city",
            "orders.zone",
            "orders.invoice_amount",
            "orders.weight",
            DB::raw("concat(orders.length, '*', orders.breadth, '*', orders.height)"),
            "orders.product_name",
            "orders.status",
            "orders.shipping_charges",
            "orders.rto_charges",
            "orders.cod_charges",
            "orders.total_charges"
        );

        if($this->filter['order_status'] == 'delivered') {
            $orders = $orders->whereDate('orders.delivered_date', '>=', $this->filter['from_date'])
                ->whereDate('orders.delivered_date', '<=', $this->filter['to_date'])
                ->orderBy('orders.delivered_date', 'desc');
        } else if($this->filter['order_status'] == 'rto') {
            $orders = $orders->whereDate('orders.delivered_date', '>=', $this->filter['from_date'])
                ->whereDate('orders.delivered_date', '<=', $this->filter['to_date'])
                ->orderBy('orders.delivered_date', 'desc');
        } else {
            $orders = $orders->whereDate('orders.awb_assigned_date', '>=', $this->filter['from_date'])
                ->whereDate('orders.awb_assigned_date', '<=', $this->filter['to_date'])
                ->orderBy('orders.awb_assigned_date', 'desc');
        }
        if($this->filter['seller_id'] != "all") {
            $orders = $orders->where('orders.seller_id', $this->filter['seller_id']);
        }
        if($this->filter['order_type'] != "all") {
            $orders = $orders->where('orders.order_type', $this->filter['order_type']);
        }
        if($this->filter['order_status'] != "all") {
            if ($this->filter['order_status'] == 'delivered') {
                $orders = $orders->where('orders.status', 'delivered')
                    ->where('orders.rto_status', 'n');
            } else if($this->filter['order_status'] == 'ndr') {
                $orders = $orders->where('orders.ndr_status', 'y');
            } else if($this->filter['order_status'] == 'rto') {
                $orders = $orders->where('orders.status', 'delivered')->where('orders.rto_status', 'y');
            } else if ($this->filter['order_status'] == 'shipped') {
                $orders = $orders->where('orders.status', '!=', 'pending')->where('orders.status', '!=', 'cancelled');
            } else {
                $orders = $orders->where('orders.status', $this->filter['order_status']);
            }
        }
        return $orders->get();
    }
}