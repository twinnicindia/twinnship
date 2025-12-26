<?php

namespace App\Http\Controllers;

use App\Helpers\TrackingHelper;
use App\Libraries\Logger;
use App\Models\Order;
use App\Models\ZZExceptionLogs;
use Illuminate\Support\Facades\DB;
use Exception;

class ServiceController extends Controller
{
    function __construct()
    {
    }

    function trackOrderJob()
    {
        try {
            $startedAt = now();
            $cronName = 'track-order';
            $totalExecuted = 0;
            $totalSucceeded = 0;
            $totalSkipped = 0;
            $startedAt = now();
            $seven_days = \Carbon\Carbon::now()->subDays(70)->format("Y-m-d H:i:s");
            //$toBeSkipped = 'ekart','ekart_1kg','ekart_2kg','ekart_3kg','ekart_5kg','amazon_swa','amazon_swa_1kg','amazon_swa_3kg','amazon_swa_5kg','amazon_swa_10kg','dtdc_express','dtdc_surface','dtdc_1kg','dtdc_2kg','dtdc_3kg','dtdc_5kg','dtdc_6kg','dtdc_10kg';
            $query = "select id from `orders` where manifest_sent = 'y' and `status` not in('pending','cancelled','delivered','shipped','lost','damaged') and `awb_assigned_date` >= '$seven_days' order by last_sync limit 1200";
            $orders=DB::select($query);
            // $orders=DB::select("select * from `orders` where `manifest_status`='y' and `status` not in('pending','cancelled','delivered','shipped') and awb_number = '2804227350'");
            foreach ($orders as $singleOrder) {
                $o = Order::find($singleOrder->id);
                $totalExecuted++;
                if($startedAt->diffInSeconds(now()) >= 1140) {
                    throw new Exception('Time limit exceeded');
                }
                try{
                    if(empty($o))
                        continue;
                    $isSucceeded = TrackingHelper::PerformTracking($o);
                    if($isSucceeded){
                        $totalSucceeded++;
                    }
                    else
                        $totalSkipped++;
                }catch(Exception $e){
                    ZZExceptionLogs::create(['order_id' => $o->id,'awb_number' => $o->awb_number,'seller_id' =>$o->seller_id,'courier_partner' => $o->courier_partner,'exception_message' => $e->getMessage()." - ".$e->getLine(),'inserted' => date('Y-m-d H:i:s')]);
                    continue;
                }
            }
            Logger::cronLog($cronName, 'success', 'Cron job executed', 'Cron executed successfully', null, $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => true,
                'message' => 'Cron executed successfully'
            ]);
        } catch(Exception $e) {
            Logger::cronLog($cronName, 'failed', 'Cron job failed', null, $e->getMessage(), $totalExecuted, $totalSucceeded, $totalSkipped, $startedAt, now());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
