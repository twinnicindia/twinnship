<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Label</title>
    <!-- Latest compiled and minified CSS -->

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            /* border: 1px solid black; */
            /* padding: 1%; */
        }
        h3{
            font-weight: 700;
        }

        table,
        td,
        th {
            text-align: left;
            border: 1px solid black;
            border-collapse: collapse;
        }
        .tableInner{
            border: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-break {
            display: block;
            page-break-after: always;
        }
        @page{
            margin : 10px;
        }
        .no_row{
            border: 0;
        }
        @if($label->tabular_form_enabled == 'n')
        #productTable td{
            font-size: 10px;
            border:0;
        }
        @else
        #productTable td{
            font-size: 10px;
            border: solid 1px black;
        }
        @endif
        .noPadding{
            padding: 0;
        }
        th,td{
            padding: 3px;
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
    $cntl = 1;
@endphp
@foreach($orders as $order)
    @if(str_contains($order->courier_partner,'amazon'))
        <div class="">
            @php
                $fileData = base64_encode(file_get_contents(App\Libraries\BucketHelper::GetDownloadLink("labels/swa/".basename($order->amazon_label))));
            @endphp
            <img src="data:image/png;base64,{{$fileData}}" style="width:355px;">
        </div>
    @else
        @if($order->awb_number != "")
            <div style="display: flex; flex-direction: column; min-height:95vh;">
            <table>
                <tbody>
                <tr style="{{ !empty($label->header_visibility) && $label->header_visibility == 'n' ? 'display:none;' : '' }}">
                    <td class="noPadding">
                        <table class="tableInner">
                            <tr>
                                <td style="width: 70%;border:0; {{ !empty($label->shipping_address_visibility) && $label->shipping_address_visibility == 'n' ? 'display:none;' : '' }}">
                                    <b style="padding:0;margin:0;margin-bottom:5px;">Ship To - </b>
                                    @if($order->o_type == 'forward')
                                        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                            <p style="padding:0;margin:0;">PII Information Archived</p>
                                        @else
                                            <p style="padding:0;margin:0;">{{$order->s_customer_name}}<br>{{str_replace("."," ",$order->s_address_line1)}}<br>{{str_replace("."," ",$order->s_address_line2)}}<?= $order->s_address_line2 == "" ? "" : "<br>"?> {{$order->s_city}}, {{$order->s_state}}, {{$order->s_country}}<bR>{{$order->s_pincode}}<bR>Contact : {{$label->contact_mask != 'y'?$order->s_contact:"**********"}}</p>
                                        @endif
                                    @else
                                        <p style="padding:0;margin:0;">{{$order->p_warehouse_name}}<br>{{$order->p_address_line1}}<br>{{$order->p_address_line2}}<?= $order->p_address_line2 == "" ? "" : "<br>"?> {{$order->p_city}}, {{$order->p_state}}, {{$order->p_country}}<bR>{{$order->p_pincode}}<bR>Contact : {{$label->contact_mask != 'y'?$order->s_contact:"**********"}}</p>
                                    @endif
                                </td>
                                <td style="width: 30%;align-items: center;align-content: center;text-align: center;border:0; {{ !empty($label->header_logo_visibility) && $label->header_logo_visibility == 'n' ? 'display:none;' : '' }}">
                                    @if($basic_info->company_logo != "")
                                        <img src="{{ asset($basic_info->company_logo)}}" style="width: 80%;height:60px;">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="noPadding">
                        <table class="tableInner">
                            @if(strtolower($order->order_type) == "cod")
                                @if($seller->is_cod_amount_visibility == 1)
                                    <tr>
                                        <td style="width: 50%;border:0; {{ !empty($label->shipment_detail_visibility) && $label->shipment_detail_visibility == 'n' ? 'display:none;' : '' }}">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 28px;">{{$name[strtolower($order->order_type) ?? ""]}}</span><br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 18px;"><img src="{{asset('public/assets/seller/RupeeIcon.png')}}" style="width: 16px;height: 15px;" ><?= $order->invoice_amount == 0 ? $order->collectable_amount:$order->invoice_amount; ?></span><br/>
                                            Dimension(cm) : {{$order->height}} x {{$order->length}} x {{$order->breadth}}<br/>
                                            Weight(kg) : {{round($order->weight / 1000,2)}}
                                            {!! $order->route_code ? "<bR> Route Code : <b>$order->route_code</b>" : '' !!}
                                            {!! $order->courier_partner == 'bluedart' ? "<bR> Reference Number : <b>$order->order_number</b>" : '' !!}
                                            {!! $order->gati_ou_code ? "<bR> From: <b>" . (explode('/', $order->gati_ou_code)[0] ?? '') ."</b> To: <b>" . (explode('/', $order->gati_ou_code)[1] ?? '') . "</b>" : '' !!}
                                            {!! $order->gati_package_no ? "<bR> Package No: <b>$order->gati_package_no</b>" : '' !!}
                                        </td>
                                        <td style="width: 50%;text-align: center;border:0; {{ !empty($label->awb_barcode_visibility) && $label->awb_barcode_visibility == 'n' ? 'display:none;' : '' }}">
                                            {{$PartnerName[$order->courier_partner] ?? "Not Found"}}<br>
                                            <?php
                                            $generated=false;
                                            while($generated != true){
                                                $barcodeData = @file_get_contents("https://twinnship.com/barcode/generate-barcode?code={$order->awb_number}");
                                                if(!empty($barcodeData))
                                                    $generated = true;
                                            }
                                            ?>
                                            <img src="data:image/png;base64,{{base64_encode($barcodeData)}}" style="height:60px;margin:8px;max-width:160px;"><br>
                                            <b>{{$order->awb_number}} {{ $order->shipment_type == 'mps' && $order->courier_partner != 'gati' ? ($order->is_parent == 'y' ? '(Master)' : '') : '' }}</b>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="width: 50%;border:0; {{ !empty($label->shipment_detail_visibility) && $label->shipment_detail_visibility == 'n' ? 'display:none;' : '' }}">
                                            Dimension(cm) : {{$order->height}} x {{$order->length}} x {{$order->breadth}}<br/>
                                            Payment : <b style="font-size: 11px;">{{$name[strtolower($order->order_type) ?? ""]}}</b><br/>
                                            Weight(kg) : {{round($order->weight / 1000,2)}}<br/>
                                            AWB No. : <b>{{$order->awb_number}} {{ $order->shipment_type == 'mps' && $order->courier_partner != 'gati' ? ($order->is_parent == 'y' ? '(Master)' : '') : '' }}</b>
                                            @if($order->shipment_type == 'mps')
                                                @if($order->courier_partner == 'gati')
                                                    {!! "<br><b>MPS</b> | Packets : <b>$order->number_of_packets</b>" !!}
                                                @else
                                                    {!! "<br><b>MPS</b> | Packets : <b>$order->number_of_packets</b>" . ($order->is_parent == 'y' ? '' : "<br>Master : $order->parent_awb") !!}
                                                @endif
                                            @endif
                                            {!! $order->route_code ? "<bR> Route Code : <b>$order->route_code</b>" : '' !!}
                                            {!! $order->courier_partner == 'bluedart' ? "<bR> Reference Number : <b>$order->order_number</b>" : '' !!}
                                            {!! $order->gati_ou_code ? "<bR> From: <b>" . (explode('/', $order->gati_ou_code)[0] ?? '') ."</b> To: <b>" . (explode('/', $order->gati_ou_code)[1] ?? '') . "</b>" : '' !!}
                                            {!! $order->gati_package_no ? "<bR> Package No: <b>$order->gati_package_no</b>" : '' !!}
                                        </td>
                                        <td style="width: 50%;text-align: center;border:0; {{ !empty($label->awb_barcode_visibility) && $label->awb_barcode_visibility == 'n' ? 'display:none;' : '' }}">
                                            {{$PartnerName[$order->courier_partner] ?? "Not Found"}}<br>
                                            <?php
                                            $generated=false;
                                            while($generated != true){
                                                $barcodeData = @file_get_contents("https://twinnship.com/barcode/generate-barcode?code={$order->awb_number}");
                                                if(!empty($barcodeData))
                                                    $generated = true;
                                            }
                                            ?>
                                            <img src="data:image/png;base64,{{base64_encode($barcodeData)}}" style="height:60px;margin:10px;max-width:160px;">
                                        </td>
                                    </tr>
                                @endif
                            @else
                                <tr>
                                    <td style="width: 50%;border:0; {{ !empty($label->shipment_detail_visibility) && $label->shipment_detail_visibility == 'n' ? 'display:none;' : '' }}">
                                        Dimension(cm) : {{$order->height}} x {{$order->length}} x {{$order->breadth}}<br/>
                                        Payment : <b style="font-size: 11px;">{{$name[strtolower($order->order_type) ?? ""]}}</b><br/>
                                        Weight(kg) : {{round($order->weight / 1000,2)}}<br/>
                                        AWB No. : <b>{{$order->awb_number}} {{ $order->shipment_type == 'mps' && $order->courier_partner != 'gati' ? ($order->is_parent == 'y' ? '(Master)' : '') : '' }}</b>
                                        @if($order->shipment_type == 'mps')
                                            @if($order->courier_partner == 'gati')
                                                {!! "<br><b>MPS</b> | Packets : <b>$order->number_of_packets</b>" !!}
                                            @else
                                                {!! "<br><b>MPS</b> | Packets : <b>$order->number_of_packets</b>" . ($order->is_parent == 'y' ? '' : "<br>Master : $order->parent_awb") !!}
                                            @endif
                                        @endif
                                        {!! $order->route_code ? "<bR> Route Code : <b>$order->route_code</b>" : '' !!}
                                        {!! $order->courier_partner == 'bluedart' ? "<bR> Reference Number : <b>$order->order_number</b>" : '' !!}
                                        {!! $order->gati_ou_code ? "<bR> From: <b>" . (explode('/', $order->gati_ou_code)[0] ?? '') ."</b> To: <b>" . (explode('/', $order->gati_ou_code)[1] ?? '') . "</b>" : '' !!}
                                        {!! $order->gati_package_no ? "<bR> Package No: <b>$order->gati_package_no</b>" : '' !!}
                                    </td>
                                    <td style="width: 50%;text-align: center;border:0; {{ !empty($label->awb_barcode_visibility) && $label->awb_barcode_visibility == 'n' ? 'display:none;' : '' }}">
                                        {{$PartnerName[$order->courier_partner] ?? "Not Found"}}<br>
                                        <?php
                                        $generated=false;
                                        while($generated != true){
                                            $barcodeData = @file_get_contents("https://twinnship.com/barcode/generate-barcode?code={$order->awb_number}");
                                            if(!empty($barcodeData))
                                                $generated = true;
                                        }
                                        ?>
                                        <img src="data:image/png;base64,{{base64_encode($barcodeData)}}" style="height:60px;margin:10px;max-width:160px;">
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="noPadding">
                        <table class="tableInner">
                            <tr>
                                <?php
                                $order_date = date('d/m/Y', strtotime($order->inserted));
                                $manifest_date = date('d/m/Y h:i a', strtotime($order->awb_assigned_date));
                                ?>
                                <td style="width: 50%;border:0;">
                                    <div style="{{ !empty($label->order_detail_visibility) && $label->order_detail_visibility == 'n' ? 'display:none;' : '' }}">
                                        <b>Shipped By</b> (if undelivered, return to)<br/>
                                        @if($order->reseller_name == "")
                                            <p style="padding:0;margin:0;">{{$order->p_address_line1}} {!! !empty($order->p_address_line2) ? '<br>' : '' !!} {{ $order->p_address_line2 }} Contact : {{$label->s_contact_mask == 'y'? "****": $order->p_contact}} <br>{{$order->p_city}}, {{$order->p_state}}, {{$order->p_country}} {{$order->p_pincode}}</p>
                                            @if(!empty($basic_info->gst_number)) GSTIN: {{$label->s_gst_mask == 'y'? "****": $basic_info->gst_number}}<br/> @endif
                                            Invoice No. : TS-{{$order->customer_order_number}}<br/>
                                        @else
                                            {{$order->reseller_name}}
                                            @if($order->seller_id == 16)
                                                @if(!empty($basic_info->gst_number)) <br />GSTIN: {{$label->s_gst_mask == 'y'? "****": $basic_info->gst_number}}<br/> @endif
                                                <br />Invoice No. : TS-{{$order->customer_order_number}}<br/>
                                                Manifest Date. : {{$manifest_date}}
                                            @endif
                                        @endif
                                    </div>
                                    <div style="{{ !empty($label->manifest_date_visibility) && $label->manifest_date_visibility == 'n' ? 'display:none;' : '' }}">
                                        Manifest Date. : {{$manifest_date}}
                                    </div>

                                </td>
                                <td style="width: 50%;text-align: center;border:0; {{ !empty($label->order_barcode_visibility) && $label->order_barcode_visibility == 'n' ? 'display:none;' : '' }}">
                                    @if($order->seller->essentials == 'y')
                                        <b>Essentials</b>
                                        <br>
                                    @endif
                                    @php
                                        $customerOrderNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $order->customer_order_number);
                                    @endphp
                                    @if($label->barcode_visibility == 'y')
                                        <?php
                                            $generated=false;
                                            while($generated != true){
                                                $barcodeData = @file_get_contents("https://twinnship.com/barcode/generate-barcode?code={$customerOrderNumber}");
                                                if(!empty($barcodeData))
                                                    $generated = true;
                                            }
                                        ?>
                                        <img src="data:image/png;base64,{{base64_encode($barcodeData)}}" style="height:60px;margin:10px;max-width:160px;">
                                        <br/>
                                    @endif
                                    @if($label->ordernumber_visibility == 'y')
                                        Order #: {{$order->customer_order_number}}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                @if($label->tabular_form_enabled == 'n')
                <tr style="{{ !empty($label->product_detail_visibility) && $label->product_detail_visibility == 'n' ? 'display:none;' : '' }}">
                    <td class="noPadding">
                        <table class="tableInner" id="productTable" style="width: 100%;">
                            <thead>
                            <tr style="border: 1px solid black;">
                                <th style="width: 90%;">Name & SKU</th>
                                <th style="width: 10%;">QTY</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                <tr>
                                    <td>PII Information Archived</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                            @else
                                @php
                                    $order_id = $order->id;
                                    $product= DB::select("select * from products where order_id = $order_id");
                                    $cnt=0;
                                    $totalProduct =  count($product);
                                    $displayProductData = $label->all_product_display == 'y' ? $totalProduct : 5;
                                    $displayProduct = $label->all_product_display == 'y' ? count($product) : 5;
                                @endphp
                                @foreach($product as $key=> $p)
                                    @if($cnt < $displayProductData)
                                        <tr>
                                            <td>
                                                <b>Name :</b>
                                                @if($label->display_full_product_name == 'n')
                                                    {{strlen($p->product_name) > 120 ? substr("$p->product_name",0,120)."..." : $p->product_name}}
                                                @else
                                                    {{$p->product_name}}
                                                @endif
                                                <b>SKU :</b>
                                                @if($label->display_full_product_name == 'n')
                                                    {{strlen($p->product_sku) > 60 ? substr("$p->product_sku",0,60)."..." : $p->product_sku}}
                                                @else
                                                    {{$p->product_sku}}
                                                @endif
                                            </td>
                                            <td>{{$p->product_qty}}</td>
                                        </tr>
                                    @endif
                                    @php($cnt++)
                                @endforeach
                                <?php
                                $productCount = $totalProduct > $displayProduct ? $displayProduct : $totalProduct;
                                $ttl = ($displayProduct - $productCount) - 1;
                                for($i=0; $i<=$ttl; $i++) {
                                ?>
                                <tr>
                                    <td><br></td>
                                    <td></td>
                                </tr>
                                <?php } ?>
                                @if($totalProduct > $displayProduct)
                                    <tr>
                                        <td colspan="2" style="text-align: right;"><b><i>and {{$totalProduct - $displayProduct}} more items.</i></b></td>
                                    </tr>
                                @endif
                                <tr style="{{ !empty($label->invoice_value_visibility) && $label->invoice_value_visibility == 'n' ? 'display:none;' : '' }}">
                                    @if($order->shipment_type == 'mps')
                                        @if($order->is_parent == 'y')
                                            <td colspan="2" style="text-align: right;">TOTAL Amount : @if($label->gift_visibility == 'y') AS A GIFT @else Rs. {{$order->seller->display_invoice=='y' && !in_array($order->seller->id, [16, 329]) ? $order->collectable_amount == 0 ? $order->invoice_amount : $order->collectable_amount : 0}} @endif</td>
                                        @else
                                            <td colspan="2" style="text-align: right;"></td>
                                        @endif
                                    @else
                                        <td colspan="2" style="text-align: right;">TOTAL Amount : @if($label->gift_visibility == 'y') AS A GIFT @else Rs. {{$order->seller->display_invoice=='y' && !in_array($order->seller->id, [16, 329]) ? $order->collectable_amount == 0 ? $order->invoice_amount : $order->collectable_amount : 0}} @endif</td>
                                    @endif
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif

                @if($label->tabular_form_enabled == 'y')
                    <!-- Product Details Tabular -->
                    <tr style="{{ !empty($label->product_detail_visibility) && $label->product_detail_visibility == 'n' ? 'display:none;' : '' }}">
                        <td class="noPadding">
                            <table class="tableInner" id="productTable" style="width: 100%;">
                                <thead>
                                <tr style="border: 1px solid black;">
                                    <th style="width: 38%;">SKU</th>
                                    <th style="width: 55%;">Item Name</th>
                                    <th style="width: 2%;">Qty</th>
                                    <th style="width: 5%;">Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($order->channel), ['amazon', 'amazon_direct']) && now()->parse($order->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                                    <tr>
                                        <td>PII Information Archived</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                        <td><br></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @else
                                    <?php
                                    $order_id = $order->id;
                                    $product= DB::select("select * from products where order_id = $order_id");
                                    $cnt=0;
                                    $totalProduct =  count($product);
                                    $displayProductData = $label->all_product_display == 'y' ? $totalProduct : 5;
                                    $displayProduct = $label->all_product_display == 'y' ? count($product) : 5;
                                    ?>
                                    @foreach($product as $key=> $p)
                                        @if($cnt < $displayProductData)
                                            <tr>
                                                <td>
                                                    @if($label->display_full_product_name == 'n')
                                                        {{strlen($p->product_sku) > 12 ? substr("$p->product_sku",0,12)."." : $p->product_sku}}
                                                    @else
                                                        {{$p->product_sku}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($label->display_full_product_name == 'n')
                                                        {{strlen($p->product_name) > 120 ? substr("$p->product_name",0,120)."..." : $p->product_name}}
                                                    @else
                                                        {{$p->product_name}}
                                                    @endif
                                                </td>
                                                <td>{{$p->product_qty}}</td>
                                                <td>{{$p->total_amount}}</td>
                                            </tr>
                                        @endif
                                        @php($cnt++)
                                    @endforeach
                                    @if($totalProduct > $displayProduct)
                                        <tr>
                                            <td></td>
                                            <td style="text-align: left;"><b><i>+ {{$totalProduct - $displayProduct}} More Products</i></b></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endif
                                    <tr style="{{ !empty($label->invoice_value_visibility) && $label->invoice_value_visibility == 'n' ? 'display:none;' : '' }}">
                                        <td colspan="4" style="text-align: right;">TOTAL Amount : @if($label->gift_visibility == 'y') AS A GIFT @else Rs. {{$order->seller->display_invoice=='y' && !in_array($order->seller->id, [16, 329]) ? $order->collectable_amount == 0 ? $order->invoice_amount : $order->collectable_amount : 0}} @endif</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif

                <tr style="{{ !empty($label->other_charges) && $label->other_charges == 'n' ? 'display:none;' : '' }}">
                    <td class="noPadding">
                        <table class="tableInner" id="productTable" style="width: 100%;border-collapse:collapse;border:0;">
                            <tbody>
                            <tr>
                                <th>COD Charges</th>
                                <th>{{$order->cod_charges ?? 0}}</th>
                            </tr>
                            <tr>
                                <th>Shipping Charges</th>
                                <th>{{$order->shipping_charges ?? 0}}</th>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <th>{{$order->discount ?? 0}}</th>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr style="{{ !empty($label->disclaimer_text) && $label->disclaimer_text == 'n' ? 'display:none;' : '' }}">
                    <td>
                        <p style="margin: 0;">All disputes are subject to {{$basic_info->state}} jurisdiction. Goods once sold will only be taken back or exchange as per the store's exchange/return policy.</p>
                    </td>
                </tr>
                @if($order->courier_partner != 'bluedart_surface')
                    <tr style="{{ (($order->shipping_partner == 'prefexo' || strtoupper($order->seller_order_type) == 'NSE' || in_array($order->courier_partner, ['dtdc_surface', 'dtdc_express', 'dtdc_10kg', 'dtdc_2kg', 'dtdc_3kg', 'dtdc_5kg'])) || (!empty($label->footer_visibility) || $label->footer_visibility == 'y' ? 'display:block' : 'display:none')) }}">
                        <td class="noPadding">
                            <table class="tableInner">
                                <tr>
                                    @if($label->custom_footer_enable == 'y')
                                        <td style="width:70%;">{{$label->footer_customize_value}}</td>
                                        <td style="width:30%;align-content: center;align-items: center;text-align: center;">
                                            <img src="{{asset($config->logo)}}" style="height:25px;width: 70px;">
                                        </td>
                                    @else
                                        @if($label->footer_visibility == 'y')
                                            <td style="width:70%;">THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE</td>
                                            <td style="width:30%;align-content: center;align-items: center;text-align: center;">
                                                <img src="{{asset($config->logo)}}" style="height:25px;width: 70px;">
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            <br>
        </div>
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
      @endif
    @endif
@endforeach
</body>

</html>
