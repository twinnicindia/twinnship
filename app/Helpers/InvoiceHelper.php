<?php
    namespace App\Helpers;

    use App\Http\Controllers\Utilities;
    use App\Libraries\Logger;
    use App\Models\Basic_informations;
    use App\Models\Employees;
    use App\Models\Invoice;
    use App\Models\Invoice_orders;
    use App\Models\Order;
    use App\Models\Partners;
    use App\Models\Product;
    use App\Models\Seller;
    use App\Models\ServiceablePincode;
    use App\Models\ServiceablePincodeFM;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;

    class InvoiceHelper{
        public static function GenerateInvoice($sellerId, $startDate, $endDate, $invoiceDate): bool
        {
            $sellers=Seller::where('id', $sellerId)->get();
            $all_sellers=[];
            $current_year = date("y");
            $next_year =  intval($current_year) + 1;
            $invoiceDate = date('Y-m-d',strtotime($invoiceDate));
            foreach ($sellers as $s){
                $all_sellers[]=$s->id;
                $orders=Order::select('id','total_charges','awb_assigned_date')->where('status','delivered')->where('invoice_status','n')->where('seller_id',$s->id)->where('delivered_date', '>=', $startDate)->where('delivered_date', '<=', $endDate)->get();
                //generate invoice here
                if(count($orders)!=0){
                    $invoiceNumber = Invoice::max('invoice_number') + 1;
                    $invoiceData=[
                        'seller_id' => $s->id,
                        'inv_id' => "TW/$current_year"."$next_year/$invoiceNumber",
                        'invoice_date' => $invoiceDate,
                        'due_date' => date('Y-m-d',strtotime($invoiceDate." +7 days")),
                        'status' => 'Paid',
                        'type' => 'f',
                        'invoice_number' => $invoiceNumber
                    ];
                    $invoice_id=Invoice::create($invoiceData)->id;
                    $total_charges=0;
                    $all_orders=[];
                    foreach ($orders as $o){
                        //insert record in invoice_orders and update invoice_status of order
                        $invoiceOrders=[
                            'invoice_id' => $invoice_id ?? 0,
                            'order_id' => $o->id
                        ];
                        Invoice_orders::create($invoiceOrders);
                        $all_orders[]=$o->id;
                        $total_charges+=$o->total_charges;
                    }
                    Order::whereIn('id',$all_orders)->update(['invoice_status'=>'y']);
                    $invoice_amount=($total_charges * 100)/ 118;
                    $charge=$total_charges - $invoice_amount;
                    Invoice::where('id',$invoice_id)->update(['gst_amount' => $charge,'invoice_amount' => $invoice_amount,'total' => $total_charges]);
                }
            }
            return true;
        }
    }
