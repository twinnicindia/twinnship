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
            border: 1px solid grey;
            padding: 2%;
        }

        table,
        td,
        th {
            padding: 7px;
            text-align: left;
            border: 1px solid #a3a8ad;
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
    @php
    $name=array(
    'cod' => "COD",
    'prepaid' => "PREPAID",
    'reverse' => "REVERSE",
    );
    @endphp
    @foreach($manifest_order as $order)
    <div class="page-break">
    <div style="display: flex; justify-content: space-between; flex-direction: column; min-height:95vh;">
        <div>
            <div style="display: flex;  justify-content: space-between;">
                <table style="border: none !important;">
                    <tr>
                        <td style="width:50%;border: none !important;">
                            <strong>DELIVER TO : </strong><br>
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
                        <td style="text-align:left;width:50%;border: none !important;">
                            <strong>SHIPPED BY:</strong><br>
                            {{$order->p_warehouse_name}}<br>
                            {{$order->p_address_line1}}<br>
                            {{$order->p_address_line2}}<br>
                            {{$order->p_city}},{{$order->p_state}},{{$order->p_country}}<br>
                            {{$order->p_pincode}}<br>
                            {{$order->p_contact_code}}{{$order->p_contact}}<br>
                            Email : {{$seller->email}}<br>
                        </td>
                    </tr>
                </table>
            </div>
            <hr style="border: 1px solid black; margin-top:15px;">
            <div style="margin-left: 30px;">
                <span>ORDER # : {{$order->customer_order_number}}<br>
                    <!-- <img src="{{$order->orderno_barcode != "" ? url($order->orderno_barcode) : ''}}" alt=""> -->
                    </span>
            </div>
            <hr style="border: 1px solid black; margin-top:15px;">
            <div style="display: flex;  justify-content: space-between;margin-left:20px;">
                <table style="border: none !important;">
                    <tr>
                        <td style="width:50%;border: none !important;">
                            SHIPMENT WEIGHT : {{$order->weight}}<br>
                            DIMENSIONS : {{$order->weight}}<br>
                        </td>
                        <td style="text-align:left;width:50%;border: none !important">
                            ROUTING CODE : {{$order->customer_order_number}}<br>

                        </td>
                    </tr>
                </table>
            </div>
            <hr style="border: 1px solid black; margin-top:15px;">
            <div style="display: flex;  justify-content: space-between;margin-left:20px;">
                <table style="border: none !important;">
                    <tr>
                        <td style="width:50%;border: none !important;">
                            <h1>{{$name[$order->order_type]}}</h1>
                            @if($order->order_type == 'cod')
                                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                    <h1> COLLECT CASH PII Data Archived</h1>
                                @else
                                    <h1> COLLECT CASH {{$order->invoice_amount}}</h1>
                                @endif
                            @endif
                        </td>
                        <td style="text-align:left;width:50%;border: none !important">
                            COURIER : {{$order->courier_partner}}
                            <div style="margin-top:15px;">
                                <span>AWB # : <br>
                                    <img src='{{url($order->awb_barcode)}}'></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                <thead style="border-top: 2px solid #a3a8ad;border-bottom: 2px solid #a3a8ad;">
                    <tr style="border: 1px solid black;">
                        <th>S.NO</th>
                        <th>SKU</th>
                        <th>ITEM</th>
                        <th>QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                        <tr>
                            <td>1</td>
                            <td>PII Data Archived<td>
                            <td>PII Data Archived<td>
                            <td>PII Data Archived</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right;">TOTAL</td>
                            <td>RS. PII Data Archived</td>
                        </tr>
                    @else
                        @php
                        $order_id = $order->id;
                        $product= DB::select("select * from products where order_id = $order_id");
                        @endphp
                        @foreach($product as $key=> $p)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$p->product_sku}}<td>
                            <td>{{$p->product_name}}<td>
                            <td>{{$p->product_qty}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" style="text-align: right;">TOTAL</td>
                            <td>RS.{{$order->invoice_amount}}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
        </div>

        <div style="position: absolute; bottom: 0;">
            <div>
                Invoice No. : Retail176449 | Invoice Date : {{date('Y-m-d h:i s ')}}<br>
                Gstin No : 29AAHCB7092H1ZA
            </div>
            <hr>
            <div>
                <span style="font-size:13px;;">TERMS AND CONDITIONS:</span><br>
                <span style="font-size:10px;">
                    1. Visit official website of Blue Dart to view the Conditions of Carriage<br>
                    2. Shipping charges are inclusive of service tax and all figures are in INR</span>
            </div>
            <hr>
            <span style="font-size:10px;">All disputes are subject to Karnataka jurisdiction. Goods once sold will only be taken back or exchanged as per the store's exchange/return policy
                <hr>
                THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE
        </div>
    </div>
    </div>
    @endforeach
</body>

</html>
