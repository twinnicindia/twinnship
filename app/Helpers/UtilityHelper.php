<?php
    namespace App\Helpers;

    use App\Http\Controllers\Utilities;
    use App\Models\Basic_informations;
    use App\Models\Employees;
    use App\Models\Partners;
    use App\Models\Product;
    use App\Models\Seller;
    use App\Models\ServiceablePincode;
    use App\Models\ServiceablePincodeFM;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;

    class UtilityHelper{
        public static function GetPaginationData($totalRecords, $pageSize, $currentRecordIndex = 1): array
        {
            // Calculate last page
            $lastPage = ceil($totalRecords / $pageSize);

            // Calculate current page if currentRecordIndex is given
            $currentPage = $currentRecordIndex;

            return [
                'lastPage' => $lastPage,
                'currentPage' => $currentPage,
                'totalRecord' => $totalRecords
            ];
        }
        public static function ApplyOrderTabFilter($orderQuery,$selectedTab)
        {
            if($selectedTab == 'all')
                $orderQuery = $orderQuery->orderBy('inserted', 'desc');
            else if($selectedTab == 'processing')
                $orderQuery = $orderQuery->where('status','pending')->orderBy('inserted', 'desc');
            else if($selectedTab == 'ready_to_ship')
                $orderQuery = $orderQuery->where('status','shipped')->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'delivered')
                $orderQuery = $orderQuery->where('status','delivered')->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'lost_damaged')
                $orderQuery = $orderQuery->whereIn('status',['lost','damaged'])->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'cancelled')
                $orderQuery = $orderQuery->where('status','cancelled')->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'live_orders')
                $orderQuery = $orderQuery->whereNotIn('status',['pending','pickup_scheduled','shipped','manifested', 'cancelled','delivered','lost', 'damaged'])->where('rto_status','n')->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'returns')
                $orderQuery = $orderQuery->where('rto_status','y')->orderBy('awb_assigned_date', 'desc');
            else if($selectedTab == 'manifest')
                $orderQuery = $orderQuery->whereIn('status',['manifested', 'pickup_scheduled'])->orderBy('awb_assigned_date', 'desc');
            return $orderQuery;
        }

        public static function ApplyOrderFilter($orderData, $filterObject){
            if(!empty($filterObject['filterStartDate']))
                $orderData = $orderData->where('inserted', '>=', $filterObject['filterStartDate']." 00:00:00");
            if(!empty($filterObject['filterEndDate']))
                $orderData = $orderData->where('inserted', '<=', $filterObject['filterEndDate']. " 23:59:59");
            if(!empty($filterObject['filterOrderStatus']))
                $orderData = $orderData->whereIn('status',$filterObject['filterOrderStatus']);
            if(!empty($filterObject['filterOrderSource']))
                $orderData = $orderData->whereIn('channel',$filterObject['filterOrderSource']);
            if(!empty($filterObject['filterCourierPartner']))
                $orderData = $orderData->whereIn('courier_partner',$filterObject['filterCourierPartner']);
            if(!empty($filterObject['filterPickupAddress']))
                $orderData = $orderData->whereIn('warehouse_id',$filterObject['filterPickupAddress']);
            if(!empty($filterObject['filterPaymentType']))
                $orderData = $orderData->whereIn('order_type',$filterObject['filterPaymentType']);
            if(!empty($filterObject['filterOrderNumber']))
                $orderData = $orderData->whereIn('customer_order_number',explode(",", $filterObject['filterOrderNumber']));
            if(!empty($filterObject['filterAWBList']))
                $orderData = $orderData->whereIn('awb_number',explode(",", $filterObject['filterAWBList']));
            return $orderData;
        }

        public static function ApplyBillingFilter($orderData, $filterObject, $tab = 'shipping'){
            if(!empty($filterObject['filterStartDate']) && $tab == 'shipping')
                $orderData = $orderData->where('awb_assigned_date', '>=', $filterObject['filterStartDate']." 00:00:00");

            else if(!empty($filterObject['filterStartDate']) && $tab == 'passbook')
                $orderData = $orderData->whereDate('datetime', '>=', $filterObject['filterStartDate']);

            if(!empty($filterObject['filterEndDate']) && $tab == 'shipping')
                $orderData = $orderData->where('awb_assigned_date', '<=', $filterObject['filterEndDate']. " 23:59:59");

            else if(!empty($filterObject['filterEndDate']) && $tab == 'passbook')
                $orderData = $orderData->whereDate('datetime', '<=', $filterObject['filterEndDate']);

            if(!empty($filterObject['filterCourierPartner']) && $tab == 'shipping')
                $orderData = $orderData->whereIn('courier_partner',$filterObject['filterCourierPartner']);

            if(!empty($filterObject['filterAWBList']) && $tab == 'shipping')
                $orderData = $orderData->whereIn('awb_number',explode(",", $filterObject['filterAWBList']));

            if(!empty($filterObject['filterAWBList']) && $tab == 'passbook')
                $orderData = $orderData->whereIn('orders.awb_number',explode(",", $filterObject['filterAWBList']));

            return $orderData;
        }

        public static function ExportSellerOrderData($filePath, $orderData)
        {
            $fp = fopen($filePath, 'w');

            // $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date', 'Status', 'AWB Number', 'Courier Partner', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Product Name 1', 'Product SKU 1', 'Product Qauntity 1', 'Product Name 2', 'Product SKU 2', 'Product Qauntity 2', 'Product Name 3', 'Product SKU 3', 'Product Qauntity 3', 'Product Name 4', 'Product SKU 4', 'Product Qauntity 4');
            $info = array('Sr.No', 'Order Number', 'Order Type', 'Payment Type', 'Order Date','Connection Date','Pickup Date', 'Status','Estimate Delivery Date', 'AWB Number', 'Courier Partner', 'Channel Name', 'Store Name', 'Delivered Date', 'Customer Name', 'Address 1', 'Address 2', 'City', 'State', 'Country', 'Pincode', 'Country Code', 'Contact No', 'Pickup Address1', 'Pickup Address2', 'Pickup City', 'Pickup State', 'Pickup Country', 'Pickup Pincode', 'Weight(KG)', 'Length(CM)', 'Height(CM)', 'Breadth(CM)', 'Shipping Charges', 'Cod Charges', 'Discount', 'Invoice Total', 'Collectable Amount', 'AWB Assigned Date', 'Last Sync','RTO Initiated Date','RTO Delivered Date','OFD Attempt', 'Product Name 1', 'Product SKU 1', 'Product Quantity 1', 'Product Name 2', 'Product SKU 2', 'Product Quantity 2', 'Product Name 3', 'Product SKU 3', 'Product Quantity 3', 'Product Name 4', 'Product SKU 4', 'Product Quantity 4');
            fputcsv($fp, $info);
            $cnt = 1;
            $PartnerName = Partners::getPartnerKeywordList();
            foreach ($orderData as $e) {
                $courierPartner = !empty($e->courier_partner) ? ($PartnerName[$e->courier_partner] ?? $e->courier_partner) : '';
                $weight = !empty($e->weight) ? $e->weight / 1000 : '';
                $pickup_time = $e->pickup_time ?? "";
                $info = array($cnt, $e->customer_order_number, $e->o_type, $e->order_type, $e->inserted,"",!empty($pickup_time) ? date('Y-m-d',strtotime($pickup_time)) : "", $e->status,$e->expected_delivery_date, ('`' . $e->awb_number . '`'), $courierPartner, $e->channel ?? '', $e->seller_channel_name ?? '', $e->delivered_date, $e->b_customer_name, $e->s_address_line1, $e->s_address_line2, $e->b_city, $e->s_state, $e->s_country, $e->s_pincode, $e->s_contact_code, $e->s_contact, $e->p_address_line1, $e->p_address_line2, $e->p_city, $e->p_state, $e->p_country, $e->p_pincode, $weight, $e->length, $e->height, $e->breadth, $e->shipping_charges, $e->cod_charges, $e->discount, $e->invoice_amount, $e->collectable_amount, $e->awb_assigned_date, $e->last_sync,"",($e->rto_status == 'y' && $e->delivered_date) ? date("Y-m-d",strtotime($e->delivered_date)) : "",$e->ofdDate->ofd_attempt ?? 0);
                $products = Product::where('order_id', $e->id)->get();
                foreach ($products as $p) {
                    $info[] = $p->product_name;
                    $info[] = $p->product_sku;
                    $info[] = $p->product_qty;
                }
                fputcsv($fp, $info);
                $cnt++;
            }
            fclose($fp);
        }

        public static function CheckPincodeServiceability($sourcePincode, $destinationPincode, $orderType, $courierPartner)
        {
            $serviceabilityLM = ServiceablePincode::where('pincode', $destinationPincode)->where('courier_partner', $courierPartner)->where('status', 'Y')->where('active','y');
            if(strtolower($orderType) == 'cod')
                $serviceabilityLM = $serviceabilityLM->where('is_cod','y');
            $serviceabilityLM = $serviceabilityLM->count();

            $serviceabilityFM = ServiceablePincodeFM::where('pincode', $sourcePincode)->where('courier_partner', $courierPartner)->where('status', 'Y');
            $serviceabilityFM = $serviceabilityFM->count();

            if($serviceabilityLM > 0 && $serviceabilityFM > 0)
                return true;
            else
                return false;
        }

        public static function CreateWarehouseDelhivery($data)
        {
            $payload = [
                "phone" => $data['contact_number'],
                "city" => $data['city'],
                "name" => $data['warehouse_code'],
                "pin" => $data['pincode'],
                "address" => preg_replace('/[^A-Za-z0-9\ \,\-]/', ' ', $data['address_line1']. " ". $data['address_line2']),
                "country" => $data['country'],
                "email" => $data['support_email'],
                "registered_name" => $data['warehouse_code'],
                "return_address" => preg_replace('/[^A-Za-z0-9\ \,\-]/', ' ', $data['address_line1']. " ". $data['address_line2']),
                "return_pin" => $data['pincode'],
                "return_city" => $data['city'],
                "return_state" => $data['state'],
                "return_country" => $data['country']
            ];

            UtilityHelper::CreateWarehouseDelhiveryAPICall($payload, "3139b9184109955719485ee59c4c2dd2dc19bf9b");
            UtilityHelper::CreateWarehouseDelhiveryAPICall($payload, "8dc254096903ba9defd9288f7a128ec21eaccfa9");
            UtilityHelper::CreateWarehouseDelhiveryAPICall($payload, "cff281f047354da104dde6852b2d5398b037f092");

        }

        public static function CreateWarehouseDelhiveryAPICall($payload, $delhiveryKey)
        {
            return Http::withHeaders([
                'Authorization' => "Token {$delhiveryKey}",
                'Content-Type' => 'application/json'
            ])->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', $payload)->body();
        }

        public static function RefreshSellerSession()
        {
            $utilities = new Utilities();
            $codRemit = $utilities->getNextCodRemitDate(Session()->get('MySeller')->id);
            if(Session()->get('MySeller')->type == 'emp'){
                $emp = Employees::find(Session()->get('MySeller')->emp_id);
                $seller = Seller::find(Session()->get('MySeller')->id);
                $gst_number = Basic_informations::where('seller_id',$seller->id)->first();
                $seller->gst_number = $gst_number->gst_number ?? "";
                $seller->type = 'emp';
                $seller->emp_id = $emp->id;
                $seller->permissions = $emp->permissions;
                Session(['MySeller' => $seller]);
            }else{
                $seller = Seller::find(Session()->get('MySeller')->id);
                $gst_number = Basic_informations::where('seller_id',$seller->id)->first();
                $seller->gst_number = $gst_number->gst_number ?? "";
                $seller->type = 'sel';
                $seller->permissions = 'all';
                $seller->cod_balance = $codRemit['nextRemitCod'];
                Session(['MySeller' => $seller]);
            }
        }

        public static function GetAllStatusList()
        {
            return [
                'pending' => 'Pending',
                'shipped' => 'Shipped',
                'manifested' => 'Manifested',
                'pickup_scheduled' => 'Pickup Scheduled',
                'picked_up' => 'Picked Up',
                'in_transit' => 'In Transit',
                'ndr' => 'NDR',
                'out_for_delivery' => 'Out For Delivery',
                'delivered' => 'Delivered',
                'rto_initiated' => 'RTO Initiated',
                'rto_in_transit' => 'ROT In Transit',
                'rto_delivered' => 'RTO Delivered',
                'lost' => 'Lost',
                'damaged' => 'Damaged',
                'cancelled' => 'Cancelled'
            ];
        }
    }
