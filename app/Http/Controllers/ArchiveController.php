<?php

namespace App\Http\Controllers;

use App\Libraries\MyUtility;
use App\Models\ArchivedJobLogs;
use App\Models\Channel_orders_log;
use App\Models\COD_transactions;
use App\Models\CronLogs;
use App\Models\DelhiveryAWBNumbers;
use App\Models\DownloadReport;
use App\Models\DtdcAwbNumbers;
use App\Models\DTDCPushLogData;
use App\Models\DtdcSEAwbNumbers;
use App\Models\EcomExpressAwbs;
use App\Models\EkartAwbNumbers;
use App\Models\EmployeeWorkLogs;
use App\Models\FileUploadJobLogModel;
use App\Models\Manifest;
use App\Models\MarutiEcomAwbs;
use App\Models\Ndrattemps;
use App\Models\Notifications;
use App\Models\Order;
use App\Models\OrderSMSLogs;
use App\Models\OrderTracking;
use App\Models\PickedUpOrders;
use App\Models\Product;
use App\Models\Transactions;
use App\Models\WeightReconciliation;
use App\Models\XbeesAwbnumber;
use App\Models\XbeesAwbnumberUnique;
use Illuminate\Http\Request;
use Exception;

class ArchiveController extends Controller
{
    protected $password;
    function __construct()
    {
        $this->password = MyUtility::GenerateArchivePassword();
    }
    function index(Request $request){
        if($request->password == $this->password){
            try{
                $orders = Order::select('id','awb_number')->where('inserted','<',$request->date)->get();
                $totalOrder = 0;
                $totalTracking = 0;
                $totalProduct = 0;
                $allOrderIds = [];
                $allAwbs = [];
                foreach ($orders as $o){
                    $allOrderIds[]=$o->id;
                    $allAwbs[]=$o->awb_number;
                    if(count($allOrderIds) == 500){
                        Order::whereIn('id',$allOrderIds)->delete();
                        $totalTracking+=$this->_ArchiveOrderTracking($allAwbs);
                        $totalProduct+=$this->_ArchiveOrderProducts($allOrderIds);
                        $totalOrder+=count($allOrderIds);
                        $allOrderIds=[];
                        $allAwbs = [];
                    }
                }
                Order::whereIn('id',$allOrderIds)->delete();
                $totalTracking+=$this->_ArchiveOrderTracking($allAwbs);
                $totalProduct+=$this->_ArchiveOrderProducts($allOrderIds);
                $totalOrder+=count($allOrderIds);
                $data = [
                    [
                        'table_name' => 'orders',
                        'deleted_before' => $request->date,
                        'executed' => date('Y-m-d H:i:s'),
                        'no_of_records' => $totalOrder,
                        'executed_by' => $request->executedBy ?? 'admin',
                        'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                    ],
                    [
                        'table_name' => 'order_tracking',
                        'deleted_before' => $request->date,
                        'executed' => date('Y-m-d H:i:s'),
                        'no_of_records' => $totalTracking,
                        'executed_by' => $request->executedBy ?? 'admin',
                        'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                    ],
                    [
                        'table_name' => 'products',
                        'deleted_before' => $request->date,
                        'executed' => date('Y-m-d H:i:s'),
                        'no_of_records' => $totalProduct,
                        'executed_by' => $request->executedBy ?? 'admin',
                        'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                    ],
                ];
                ArchivedJobLogs::insert($data);
                return response()->json(['status' => true, 'message' => 'Archive Job Executed Successfully','orders' => $totalOrder,'tracking'=> $totalTracking,'products' => $totalProduct]);
            }
            catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]);
            }

        }else{
            return response()->json(['status' => false, 'message' => 'Invalid Password Please Use the Correct Password']);
        }
    }
    function archivePendingOrders(Request $request){
        if($request->password == $this->password){
            try{
                $orders = Order::select('id','awb_number')->where('status','pending')->where('inserted','<',$request->date)->get();
                $totalOrder = 0;
                $totalProduct = 0;
                $allOrderIds = [];
                foreach ($orders as $o){
                    $allOrderIds[]=$o->id;
                    if(count($allOrderIds) == 500){
                        Order::whereIn('id',$allOrderIds)->delete();
                        $totalProduct+=$this->_ArchiveOrderProducts($allOrderIds);
                        $totalOrder+=count($allOrderIds);
                        $allOrderIds=[];
                    }
                }
                Order::whereIn('id',$allOrderIds)->delete();
                $totalProduct+=$this->_ArchiveOrderProducts($allOrderIds);
                $totalOrder+=count($allOrderIds);
                $data = [
                    [
                        'table_name' => 'orders',
                        'deleted_before' => $request->date,
                        'executed' => date('Y-m-d H:i:s'),
                        'no_of_records' => $totalOrder,
                        'executed_by' => 'job',
                        'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                    ],
                    [
                        'table_name' => 'products',
                        'deleted_before' => $request->date,
                        'executed' => date('Y-m-d H:i:s'),
                        'no_of_records' => $totalProduct,
                        'executed_by' => 'job',
                        'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
                    ],
                ];
                ArchivedJobLogs::insert($data);
                return response()->json(['status' => true, 'message' => 'Archive Job Executed Successfully','orders' => $totalOrder,'products' => $totalProduct]);
            }
            catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e->getMessage()."-".$e->getFile()."-".$e->getLine()]);
            }

        }else{
            return response()->json(['status' => false, 'message' => 'Invalid Password Please Use the Correct Password']);
        }
    }
    function _ArchiveOrderTracking($orderIds){
        $trackingIds = OrderTracking::select('id')->whereIn('awb_number',$orderIds)->get();
        $deleteIds = [];
        $deletedTotal = 0;
        foreach ($trackingIds as $t){
            $deleteIds[]=$t->id;
            if(count($deleteIds) == 1000){
                OrderTracking::whereIn('id',$deleteIds)->delete();
                $deleteIds = [];
                $deletedTotal+=count($deleteIds);
            }
        }
        OrderTracking::whereIn('id',$deleteIds)->delete();
        $deletedTotal+=count($deleteIds);
        return $deletedTotal;
    }
    function _ArchiveOrderProducts($orderIds){
        $productIds = Product::select('id')->whereIn('order_id',$orderIds)->get();
        $deleteIds = [];
        $deletedTotal = 0;
        foreach ($productIds as $t){
            $deleteIds[]=$t->id;
            if(count($deleteIds) == 1000){
                Product::whereIn('id',$deleteIds)->delete();
                $deleteIds = [];
                $deletedTotal+=count($deleteIds);
            }
        }
        Product::whereIn('id',$deleteIds)->delete();
        $deletedTotal+=count($deleteIds);
        return $deletedTotal;
    }
    function archiveOtherTables(Request $request){
        $date = $request->date;
        if($this->password != $request->password){
            return response()->json(['status' => false, 'message' => 'Invalid Password Please Use the Correct Password']);
        }
        $allData = [
            [
                'table_name' => 'channel_orders_log',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveChannelOrdersLog(),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'cod_transactions',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveCODTransactions($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'cron_logs',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveCronLogs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'delhivery_awb_numbers',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveDelhiveryAWBNumbers($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'download_report',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveDownloadReport($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'dtdc_awb_numbers',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveDTDCAWBNumbers($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'dtdc_se_awb_numbers',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveDTDCSEAWBNumbers($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'dtdc_push_data_log',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveDTDCPushDataLog($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'ecom_express_awbs',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveEcomExpressAWBs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'ekart_awb_numbers',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveEkartAWBs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'employee_work_logs',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveEmployeeWorkLogs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'file_upload_job_logs',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveFileUploadJobLogs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'manifest',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveManifest($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'maruti_ecom_awbs',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveMarutiEcomAWBs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'ndr_attempts',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveNDRAttempts($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'notifications',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveNotification($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'order_sms_los',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveOrderSMSLogs($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'picked_order_list',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchivePickedOrderList($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'transactions',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveTransactions($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'weight_reconciliation',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveWeightReconciliation($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'xpressbees_awb_numbers',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveXpressBeesAWBNumbers($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ],
            [
                'table_name' => 'xpressbees_awb_numbers_unique',
                'deleted_before' => $request->date,
                'executed' => date('Y-m-d H:i:s'),
                'no_of_records' => $this->_ArchiveXpressBeeUnique($date),
                'executed_by' => 'job',
                'ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
            ]
        ];
        ArchivedJobLogs::insert($allData);
        $responseArray = [];
        foreach ($allData as $dm) {
            $responseArray[]=[
                'table' => $dm['table_name'],
                'records' => $dm['no_of_records']
            ];
        }
        return response()->json(['status' => true,'message' => 'Data Archived Successfully','result' => $responseArray]);
    }
    function _ArchiveChannelOrdersLog(){
        $count = Channel_orders_log::count();
        Channel_orders_log::truncate();
        return $count;
    }
    function _ArchiveCODTransactions($date){
        $allCOD = COD_transactions::select('id')->where('datetime','<',$date)->get();
        $allData = [];
        foreach ($allCOD as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                COD_transactions::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        COD_transactions::whereIn('id',$allData)->delete();
        return count($allCOD);
    }
    function _ArchiveCronLogs($date){
        $allRecords = CronLogs::select('id')->where('finished_at','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                CronLogs::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        CronLogs::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveDelhiveryAWBNumbers($date){
        $allCOD = DelhiveryAWBNumbers::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allCOD as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                DelhiveryAWBNumbers::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        DelhiveryAWBNumbers::whereIn('id',$allData)->delete();
        return count($allCOD);
    }
    function _ArchiveDownloadReport($date){
        $allRecords = DownloadReport::select('id')->where('updated_at','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                DownloadReport::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        DownloadReport::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveDTDCAWBNumbers($date){
        $allRecords = DtdcAwbNumbers::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                DtdcAwbNumbers::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        DtdcAwbNumbers::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveDTDCSEAWBNumbers($date){
        $allRecords = DtdcSEAwbNumbers::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                DtdcSEAwbNumbers::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        DtdcSEAwbNumbers::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveDTDCPushDataLog($date){
        $allRecords = DTDCPushLogData::select('id')->where('inserted','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                DTDCPushLogData::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        DTDCPushLogData::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveEcomExpressAWBs($date){
        $allRecords = EcomExpressAwbs::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                EcomExpressAwbs::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        EcomExpressAwbs::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveEkartAWBs($date){
        $allRecords = EkartAwbNumbers::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                EkartAwbNumbers::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        EkartAwbNumbers::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveEmployeeWorkLogs($date){
        $allRecords = EmployeeWorkLogs::select('id')->where('inserted','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                EmployeeWorkLogs::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        EmployeeWorkLogs::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveFileUploadJobLogs($date){
        $allRecords = FileUploadJobLogModel::select('id')->where('updated_at','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                FileUploadJobLogModel::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        FileUploadJobLogModel::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveManifest($date){
        $allRecords = Manifest::select('id')->where('created','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                Manifest::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        Manifest::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveMarutiEcomAWBs($date){
        $allRecords = MarutiEcomAwbs::select('id')->where('used_time','<',$date)->where('used','n')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                MarutiEcomAwbs::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        MarutiEcomAwbs::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveNDRAttempts($date){
        $allRecords = Ndrattemps::select('id')->where('raised_date','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                Ndrattemps::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        Ndrattemps::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveNotification($date){
        $allRecords = Notifications::select('id')->where('updated_at','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                Notifications::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        Notifications::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveOrderSMSLogs($date){
        $allRecords = OrderSMSLogs::select('id')->where('sent_datetime','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                OrderSMSLogs::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        OrderSMSLogs::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchivePickedOrderList($date){
        $allRecords = PickedUpOrders::select('id')->where('datetime','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                PickedUpOrders::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        PickedUpOrders::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveTransactions($date){
        $allRecords = Transactions::select('id')->where('datetime','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                Transactions::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        Transactions::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveWeightReconciliation($date){
        $allRecords = WeightReconciliation::select('id')->where('created','<',$date)->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                WeightReconciliation::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        WeightReconciliation::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveXpressBeesAWBNumbers($date){
        $allRecords = XbeesAwbnumber::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                XbeesAwbnumber::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        XbeesAwbnumber::whereIn('id',$allData)->delete();
        return count($allRecords);
    }
    function _ArchiveXpressBeeUnique($date){
        $allRecords = XbeesAwbnumberUnique::select('id')->where('used_time','<',$date)->where('used','y')->get();
        $allData = [];
        foreach ($allRecords as $a){
            $allData[]=$a->id;
            if(count($allData) == 500){
                XbeesAwbnumberUnique::whereIn('id',$allData)->delete();
                $allData=[];
            }
        }
        XbeesAwbnumberUnique::whereIn('id',$allData)->delete();
        return count($allRecords);
    }

}
