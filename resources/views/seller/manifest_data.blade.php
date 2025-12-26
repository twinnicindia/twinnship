<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>
    <!-- Latest compiled and minified CSS -->

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    <style>
        table,
        td,
        th {
            border: 1px solid black;
            padding: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="invoice-title">
                    <h2 style="text-align: center;">Invoice</h2>
                </div>
                <hr>
                <div class="row">
                        <strong>Credit Note No.: CN/CM/12033</strong><br>
                        Credit Note Date: 2020-11-20 14:34:22<br>
                </div>
                <br>
                <div class="row" style="display: flex;  justify-content: space-between;">
                    <table style="border: none !important;">
                        <tr>
                            <td style="width:50%;border: none !important;"> <strong>TO</strong><br>
                                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                    PII Data Archived
                                @else
                                    {{$order->s_customer_name}}<br>
                                    {{$order->b_address_line1}}<br>
                                    {{$order->b_address_line2}}<br>
                                    {{$order->b_city}},{{$order->b_state}},{{$order->b_country}}<br>
                                    {{$order->b_contact_code}}{{$order->b_contact}}<br>
                                @endif
                            </td>
                            <td style="text-align:right;width:50%;border: none !important;">
                                <strong>{{$order->p_warehouse_name}}</strong><br>
                                {{$order->p_customer_name}}<br>
                                {{$order->p_address_line1}}<br>
                                {{$order->p_address_line2}}<br>
                                {{$order->p_city}},{{$order->p_state}},{{$order->p_country}}<br>
                                {{$order->p_contact_code}}{{$order->p_contact}}<br>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product SKU</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Product Price</th>
                        <th>Product Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                        <tr>
                            <td>PII Data Archived</td>
                            <td>PII Data Archived</td>
                            <td>PII Data Archived</td>
                            <td>PII Data Archived</td>
                            <td>PII Data Archived</td>
                        </tr>
                        <tr>
                            <td colspan="4"><strong>Total Credit Note Value</strong></td>
                            <td><strong>PII Data Archived</strong></td>
                        </tr>
                    @else
                        @foreach($product as $p)
                        <tr>
                            <td>{{$p->product_sku}}</td>
                            <td>{{$p->product_name}}</td>
                            <td>{{$p->product_qty}}</td>
                            <td>{{$p->product_unitprice}}</td>
                            <td>{{$p->total_amount}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"><strong>Total Credit Note Value</strong></td>
                            <td><strong>{{$order->invoice_amount}}</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="row">
            <h4>CIN number: U72900DL2011PTC225614</h4>
            <p>This is a system generated credit note and does not require a signature</p>
        </div>

    </div>
</body>

</html>
