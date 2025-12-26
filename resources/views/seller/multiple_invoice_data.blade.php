<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice</title>
    <!-- Latest compiled and minified CSS -->

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            ;
        }

        table,
        td,
        th {
            padding: 7px;
            text-align: left;
            border-bottom: 1px solid #a3a8ad;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .page-break {
            display: block;
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        @foreach($orders as $order)

        <div class="row">

            <!-- <img src="{{$seller->logo_image==""?asset('public/assets/seller/images/logo.png'):asset($seller->profile_image)}}" alt="" style="height:30px; text-align:center;"> -->
                @if(file_exists($basic->company_logo))
                    <img style="height:25px;" src="data:image/png;base64,{{base64_encode(@file_get_contents(asset($basic->company_logo)))}}">
                @endif
            <h3 style="text-align: center;">{{$seller->company_name}}</h3>
            <h3 style="text-align: center; padding:10px;border-top:1px solid black;border-bottom:1px solid black">TAX INVOICE</h3>

            <div class="row" style="display: flex;  justify-content: space-between;">
                <table style="border: none !important;">
                    <tr>
                        <td style="width:30%;border: none !important; border-right:1px dashed black;"> <strong>SHIPPING ADDRESS</strong><br>
                            @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                PII Data Archived
                            @else
                                {{$order->s_customer_name}}<br>
                                {{$order->s_address_line1}}<br>
                                {{$order->s_address_line2}}<br>
                                {{$order->s_city}},{{$order->s_state}},{{$order->s_country}}<br>
                                {{$order->s_pincode}}<br>
                                {{$order->s_contact_code}}{{$order->s_contact}}<br>
                            @endif
                        </td>
                        <td style="text-align:right;width:30%;border: none !important;border-right:1px dashed black;">
                            <strong>SOLD BY:</strong><br>
                            {{$order->p_warehouse_name}}<br>
                            {{$order->p_address_line1}}<br>
                            {{$order->p_address_line2}}<br>
                            {{$order->p_city}},{{$order->p_state}},{{$order->p_country}}<br>
                            {{$order->p_pincode}}<br>
                            {{$order->p_contact_code}}{{$order->p_contact}}<br>
                            Email : {{$seller->email}}<br>
                        </td>
                        <td style="text-align:left;width:40%;border: none !important;">
                            <strong>INVOICE DETAILS</strong><br>
                            <strong>INVOICE NO : </strong>{{$order->customer_order_number}}<br>
                            <strong>GST NO : </strong>{{$basic->gst_number}}<br>
                            <strong>INVOICE DATE : </strong>{{date('Y-m-d',strtotime($order->awb_assigned_date))}}<br>
                            <strong>CHANNEL : </strong>{{$order->channel}}<br>
                            <strong>SHIPPED BY : </strong>{{$PartnerName[$order->courier_partner] ?? "Not Shipped"}}<br>
                            <strong>AWB NO : </strong>{{$order->awb_number}}<br>
                            <strong>PAYMENT METHOD : </strong>{{$order->order_type}}<br>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br>
        <div class="row">
            <table>
                <thead style="border-top: 2px solid #a3a8ad;border-bottom: 2px solid #a3a8ad;">
                    <tr>
                        <th>S.NO</th>
                        <th>PRODUCT NAME</th>
                        <th>PRODUCT SKU</th>
                        <th>QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                        <tr>
                            <td>1</td>
                            <td>PII Data Archived<br>
                            <td>PII Data Archived<br>
                            <td>PII Data Archived</td>
                        </tr>
                    @else
                        @foreach($order->products as $key=> $p)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$p->product_name}}<br>
                            <td>{{$p->product_sku}}<br>
                            <td>{{$p->product_qty}}</td>
                        </tr>
                        @endforeach
                    @endif
                    @if(intval($order->igst) == 0)
                        <tr>
                            <td colspan="3" style="text-align: right;"><h5>SGST</h5></td>
                            <td><h5>{{intval($order->sgst)}}</h5></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right;"><h5>CGST</h5></td>
                            <td><h5>{{intval($order->cgst)}}</h5></td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3" style="text-align: right;"><h5>IGST</h5></td>
                            <td><h5>{{intval($order->igst)}}</h5></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" style="text-align: right;">
                            <h3>NET TOTAL(In Value)</h3>
                        </td>
                        <td>
                            @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                <h3>PII Data Archived</h3>
                            @else
                                <h3>{{$order->invoice_amount}}</h3>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="row">
            <div style="border: 1px solid black;height:70px; width:160px;"></div>
            <p style="font-weight: bold;">Authorized Signature for {{$seller->company_name}}</p>
        </div>
        @if(!$loop->last)
        <div class="page-break"></div>
        @endif
        @endforeach
    </div>
</body>

</html>
