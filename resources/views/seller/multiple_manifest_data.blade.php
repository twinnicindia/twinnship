<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Manifest</title>
    <style>
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

        body {
            font-family: sans-serif;
            border: 1px solid grey;
            padding: 2%;
            font-size: 11px;
        }
    </style>
</head>

<body>
<div class="container">
    <div>
        <div style="display: inline-block; width:10%; margin-left:5px;">
            <img style="height:20px;" src="data:image/png;base64,{{base64_encode(@file_get_contents(asset($config->logo)))}}">
        </div>
        <div style="display: inline-block; margin-left: 30%; text-align:center;">
            <span style="font-size:16px;">{{$config->title}}</span><br>
            <span style="font-size:9px;">Generated on : {{date('F d Y h:i A')}}</span>
        </div>
    </div>
    <div class="row" style="display: flex;  justify-content: space-between;">
        <table style="border: none !important;">
            <tr>
                <td style="width:50%;border: none !important;">
                    Seller : <strong>{{Session('MySeller')->company_name}}</strong><br>
                    Courier : <strong>{{"Custom"}}</strong>
                </td>

                <td style="text-align:right;width:50%;border: none !important;">
                    Manifest Id : {{$manifest_id ?? "Custom"}}<br>
                    Total shipments to dispatch : {{count($manifest_data)}}
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class=" row">
        <table>
            <thead style="border-top: 2px solid #a3a8ad;border-bottom: 2px solid #a3a8ad;">
            <tr>
                <th>S.NO</th>
                <th>Order no</th>
                <th>AWB no</th>
                <th>Contents</th>
                <th>Barcode</th>
            </tr>
            </thead>
            <tbody>
            @foreach($manifest_data as $key=> $m)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$m->customer_order_number}}</td>
                    <td>{{$m->awb_number}}</td>
                    <td>
                        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($m->channel), ['amazon', 'amazon_direct']) && now()->parse($m->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                            PII Data Archived
                        @else
                            {{substr($m->product_name,0,20)}}{{strlen($m->product_name)>20 ? ".." : ""}}({{substr($m->product_sku,0,20)}}{{strlen($m->product_sku)>20 ? ".." : ""}})
                        @endif
                    </td>
                    <td style="padding:1%;"> <img src="data:image/png;base64,{{base64_encode(@file_get_contents(url('/barcode/test.php?code='.$m->awb_number)))}}" style="max-width:150px"><br><br></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div><br>
    <h4 style="text-align:center;border-bottom: 1px dashed #a3a8ad; border-top: 1px dashed #a3a8ad; padding:7px;">To Be Filled By Custom Executives</h4>
    <br>
    <div class="row" style="display: flex;  justify-content: space-between; margin:2% 4% 5% 4%;">
        <table style="border: none !important;">
            <tr>
                <td style="width:50%;border: none !important;">
                    Pickup Time : <strong>_______</strong><br>
                    FE Name : <strong>_______</strong><br>
                    FE Signature : <strong>_______</strong><br>
                    FE Phone : <strong>_______</strong>
                </td>

                <td style="text-align:right;width:50%;border: none !important;">
                    Total Item Picked : ______<br>
                    Seller Signature : _______
                </td>
            </tr>
        </table>
    </div>
    <div style="text-align: center;">
        <strong>Contact : </strong>{{$manifest->warehouse_contact??""}}
        <br><br>
        This is a system generated document
    </div>
</div>
</body>

</html>
