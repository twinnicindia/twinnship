<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Orders | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style type="text/css">
        @media print {
            .border-right,
            .header,
            #printButton {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')

        <div class="content-wrapper" id="contentDiv">
            <div class="content-inner" id="data_div">
                <div class="card" style="margin-left: 60px;">
                    <div class="card-header" >
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="m-b-0">
                                    @if(file_exists($basic->company_logo))
                                        <img src="{{asset($basic->company_logo)}}" style="width: 50px;" height="50" alt="{{$basic->company_name}}">
                                    @else
                                        {{$basic->company_name}}
                                    @endif
                                </h4>
                            </div>
                            <!-- <div class="col-md-6 text-right">
                                <button class="btn btn-outline-danger btn-sm">Cancelled</button>
                                <a href="orders/create/2762376/clone" class="btn btn-outline-dark btn-sm"> <i class="mdi mdi-content-copy"></i> Clone</a>
                            </div> -->
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="font-primary">Order</h4>
                                <div class="">
                                    <strong>Order No:</strong>
                                    <span>{{$order_data->customer_order_number}}</span>
                                </div>
                                <div class="">
                                    <strong>Date:</strong>
                                    <span>{{date('M d, Y',strtotime($order_data->inserted))}}</span>
                                </div>
                                <div class="">
                                    <strong>Payment Type:</strong>
                                    <span>{{$order_data->order_type}}</span>
                                </div>
                                <div class="">
                                    <strong>Order Weight:</strong>
                                    <span>{{round((intval($order_data->weight) / 1000),2) ?? 0}}</span>
                                </div>
                                <div class="">
                                    <strong>Dimension:</strong>
                                    <span>
                                        {{$order_data->length}} x {{$order_data->breadth}} x {{$order_data->height}} </span>
                                </div>
                                -
                            </div>
                            <div class="col-md-3">
                                <h5>Warehouse Details</h5>
                                    {{$order_data->p_customer_name}} <br>
                                    {{$order_data->p_address_line1}}<br>
                                    {{$order_data->city}}, {{$order_data->p_state}} {{$order_data->p_pincode}}<br>
                                    {{$order_data->p_country}}<br>
                                    {{$order_data->p_contact_code}} {{$order_data->p_contact}}<br>
                            </div>
                            <div class="col-md-3">
                                <h5>RTO Details</h5>
                                   {{$rto_warehouse->contact_name ?? ""}} <br>
                                    {{$rto_warehouse->address_line1 ?? ""}}<br>
                                    {{$rto_warehouse->city ?? ""}}, {{$rto_warehouse->state ?? ""}} {{$rto_warehouse->pincode ?? ""}}<br>
                                    {{$rto_warehouse->country ?? ""}}<br>
                                    {{$rto_warehouse->code ?? ""}} {{$rto_warehouse->contact_number ?? ""}}<br>
                                    GST No : <b>{{$rto_warehouse->gst_number ?? ""}}</b>
                            </div>
                            <div class="col-md-3">
                                <h5>
                                    Customer Details
                                </h5>
                                <address class="m-t-10" id="oldinfo">
                                    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order_data->channel), ['amazon', 'amazon_direct']) && now()->parse($order_data->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                        PII Data Archived
                                    @else
                                        {{$order_data->s_customer_name}} <br>
                                        {{$order_data->s_address_line1}}<br>
                                        {{$order_data->s_city}}, {{$order_data->s_state}} {{$order_data->s_pincode}}<br>
                                        {{$order_data->s_country}}<br>
                                        {{$order_data->s_contact_code}} {{$order_data->s_contact}}<br>
                                    @endif
                                </address>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3">
                                <thead>
                                    <tr>
                                        <th class="text-center">Product SKU</th>
                                        <th class="text-center">Product</th>
                                        <th class="text-center">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order_data->channel), ['amazon', 'amazon_direct']) && now()->parse($order_data->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                        <tr>
                                            <td class="text-center">PII Data Archived</td>
                                            <td class="text-center" id="oldproductname">
                                                <p class="text-black m-0">PII Data Archived</p>
                                            </td>
                                            <td class="text-center" id="oldproductqty">PII Data Archived</td>
                                        </tr>
                                    @else
                                        @foreach($product_data as $single)
                                        <tr>
                                            <td class="text-center">{{$single->product_sku}}</td>
                                            <td class="text-center" id="oldproductname">
                                                <p class="text-black m-0">{{$single->product_name}}</p>
                                            </td>
                                            <td class="text-center" id="oldproductqty">{{$single->product_qty}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    @if(empty($order_data->igst))
                                    <tr class="bg-light">
                                        <td></td>
                                        <td class="text-right">Price</td>
                                        <td class="text-center"><i class="fa fa-inr"></i> {{ intval($order_data->invoice_amount) - (intval($order_data->sgst) + intval($order_data->cgst))}}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td class="text-right">SGST (9%)</td>
                                        <td class="text-center"><i class="fa fa-inr"></i> {{$order_data->sgst}}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td class="text-right">CGST (9%)</td>
                                        <td class="text-center"><i class="fa fa-inr"></i> {{$order_data->cgst}}</td>
                                    </tr>
                                    @else
                                    <tr class="bg-light">
                                        <td></td>
                                        <td class="text-right">Price</td>
                                        <td class="text-center"><i class="fa fa-inr"></i> {{$order_data->invoice_amount - $order_data->igst}}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td class="text-right">IGST (18%)</td>
                                        <td class="text-center"><i class="fa fa-inr"></i> {{$order_data->igst}}</td>
                                    </tr>
                                    @endif
                                    <tr class="bg-light">
                                        <td colspan="1"></td>
                                        <td class="text-right">Total (Invoice Value)</td>
                                        <td class="text-center"><i class="fa fa-inr"></i>
                                            @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order_data->channel), ['amazon', 'amazon_direct']) && now()->parse($order_data->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                                PII Data Archived
                                            @else
                                                {{$order_data->invoice_amount}}
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="text-md-right">
                            <input id="printButton" type="button" class="btn btn-success" onclick="printData();" value="Print" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@include('seller.pages.scripts')
<script type="text/javascript">
    function printData() {
        //$('#contentDiv').removeClass('col-md-10').addClass('col-md-12');
        print();
    }
</script>

</html>
