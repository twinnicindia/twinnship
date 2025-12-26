<input type="hidden" value="{{$total_order ?? 0}}" id="total_order_count">
@php
    $orderStatus = array(
    "pending" => "Pending",
    "shipped" => "Shipped",
    "pickup_requested" => "Pickup Requested",
    "manifested" => "Manifested",
    "pickup_scheduled" => "Pickup Scheduled",
    "picked_up" => "Picked Up",
    "cancelled" => "Cancelled",
    "in_transit" => "In Transit",
    "out_for_delivery" => "Out for Delivery",
    "rto_initated" => "RTO Initiated",
    "rto_initiated" => "RTO Initiated",
    "delivered" => "Delivered",
    "ndr" => "NDR",
    "lost" => "Lost",
    "damaged" => "Damaged",
    "hold" => "Hold",
    );
@endphp
@forelse($order as $o)
<tr id="row{{$o->id}}">
    <td>
        <input type="checkbox" class="selectedCheck" data-status="{{$o->status}}" value="{{$o->id}}">
    </td>
    <?php
    $date = date('d/m/Y', strtotime($o->inserted));
    $time = date('h:i A', strtotime($o->inserted));
    ?>
    <td>Date : {{$date}} <br> Time : {{$time}}</td>
    <td>{{$o->channel==''?"Custom" : "$o->channel"}}</td>
    <td><a href='{{url("/view-order/$o->id")}}' target="_blank">{{$o->customer_order_number}}</a><br>
        <span class="{{$o->status=='pending' ? 'text-danger' : 'text-primary'}} font-weight-bold text-capitalize">{{$o->rto_status=='y'?'RTO ':''}}{{$orderStatus[$o->status]}}</span>
        <br>
        <span class="badge {{$o->o_type =='forward'?'badge-success':'badge-danger'}}">{{$o->o_type}}</span><br>
    </td>
    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
        <td><span class="{{$o->invoice_amount == '' ? 'text-danger font-weight-bold' : ''}}">Amount </span> : PII Data Archived<br><span class="badge badge-success">{{$o->order_type}}</span>@if($o->shipment_type == 'mps')<span class="badge badge-primary ml-1">MPS</span>@endif</td>
    @else
        <td><span class="{{$o->invoice_amount == '' ? 'text-danger font-weight-bold' : ''}}">Amount </span> : {{$o->invoice_amount}}<br><span class="badge badge-success">{{$o->order_type}}</span>@if($o->shipment_type == 'mps')<span class="badge badge-primary ml-1">MPS</span>@endif</td>
    @endif
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived
        @else
            Name : {{strlen($o->product_name) > 15 ? substr("$o->product_name",0,15)."..." : $o->product_name}}<br>
            SKU : {{strlen($o->product_sku) > 15 ? substr("$o->product_sku",0,15)."..." : $o->product_sku}} <br>
            Qty : {{$o->product_qty ?? 1}} &nbsp;<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="Name : @foreach(explode(',', $o->product_name) as $name) {{$name}} @endforeach <br> SKU : @foreach(explode(',', $o->product_sku) as $sku) {{$sku}} @endforeach <br> QTY : {{$o->product_qty ?? 1}}"><i class="fas fa-eye text-primary"></i></a>
        @endif
    </td>
    @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
        <td>
            <span class="{{$o->b_customer_name == '' ? 'text-danger font-weight-bold' : ''}}">Name</span> : PII Data Archived</br>
            <span class="{{$o->b_contact == '' ? 'text-danger font-weight-bold' : ''}}">Contact</span> : PII Data Archived
        </td>
    @else
        <td>
            <span class="{{$o->s_customer_name == '' ? 'text-danger font-weight-bold' : ''}}">Name</span> :{{strlen($o->s_customer_name) > 15 ? substr("$o->s_customer_name",0,15)."..." : $o->s_customer_name}} @if(strlen($o->s_customer_name) > 15)<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="{{$o->s_customer_name}}"><i class="fas fa-eye text-primary"></i></a>@endif<br>
            <span class="{{$o->s_contact == '' ? 'text-danger font-weight-bold' : ''}}">Contact</span> :{{$o->s_contact}}
        </td>
    @endif
    @if($o->o_type == 'forward')
        <td>{{$o->p_warehouse_name}}
            <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->p_address_line1}} <br> {{$o->p_address_line2}} <br> {{$o->p_city}} {{$o->p_state}} {{$o->p_pincode}}"><i class="fas fa-eye text-primary"></i></a>
        </td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            <td>PII Data Archived</td>
        @else
            <td>{{$o->s_state}} <br>
                {{$o->s_city}} <br>
                {{$o->s_pincode}} <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->s_address_line1}} <br> {{$o->s_address_line2}} <br> {{$o->s_city}} {{$o->s_state}} {{$o->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
            </td>
        @endif
    @else
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            <td>PII Data Archived</td>
        @else
            <td>{{$o->s_state}} <br>
                {{$o->s_city}} <br>
                {{$o->s_pincode}} <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->s_address_line1}} <br> {{$o->s_address_line2}} <br> {{$o->s_city}} {{$o->s_state}} {{$o->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
            </td>
        @endif
        <td>{{$o->p_warehouse_name}}
            <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->p_address_line1}} <br> {{$o->p_address_line2}} <br> {{$o->p_city}} {{$o->p_state}} {{$o->p_pincode}}"><i class="fas fa-eye text-primary"></i></a>
        </td>
    @endif
    <td class="OD-{{ $o->id }}">
        <div>
            <span class="{{$o->weight == '' ? 'text-danger font-weight-bold' : ''}}">Wt</span> : {{$o->weight != '' ? round(($o->weight / 1000),2) : ''}}
            @if($o->status == 'pending')
            <!--<a class="dimensiondata ml-1" data-type="all_order" data-placement="top" data-toggle="tooltip" data-original-title="Edit Dimension" data-id="{{$o->id}}"> <i class="fas fa-edit fa-lg text-primary"></i></a>-->
            @endif
            <br>
            (<span class="{{$o->length == '' ? 'text-danger font-weight-bold' : ''}}">L</span> * <span class="{{$o->breadth == '' ? 'text-danger font-weight-bold' : ''}}">B</span> * <span class="{{$o->height == '' ? 'text-danger font-weight-bold' : ''}}">H</span>) :
            <span class="{{$o->length == '' ? 'text-danger font-weight-bold' : ''}}">{{$o->length !='' ? round($o->length) : '__'}}</span> * <span class="{{$o->breadth == '' ? 'text-danger font-weight-bold' : ''}}">{{$o->breadth !='' ? round($o->breadth) : '__'}}</span> * <span class="{{$o->height == '' ? 'text-danger font-weight-bold' : ''}}">{{$o->height !='' ? round($o->height) : '__'}}</span><br>
            @if($o->height != '' && $o->length != '' && $o->length != '')
            <span>Vol.Wt</span> : {{round($o->vol_weight / 1000,2) ?? 0}}<br>
            @endif
        </div>
    </td>
    <td>
        @if($o->awb_number!='')
        Courier : {{$PartnerName[$o->courier_partner] ?? ''}}<br>
        AWB : <a href='{{url("track-order/$o->awb_number")}}' target="_blank">{{$o->awb_number}}</a><br>
        @else
        <p>-</p>
        @endif
    </td>
    <td>
        @if (in_array($o->status, ['manifested', 'shipped', 'pickup_requested', 'pickup_scheduled']))
        <button type="button" class="btn btn-warning shipOrderButton btn-sm mx-0" title="Re-assign Order" data-id="{{$o->id}}" data-status="{{$o->status}}" data-placement="top" data-toggle="tooltip" data-original-title="Re-assign Order"><i class="fas fa-shipping-fast"></i></button>
        @endif
        <!--<button type="button" title="Edit Order" class="btn btn-primary modify_data btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Edit Order"> <i class="fas fa-pencil"></i> </button>-->
    </td>
</tr>
@empty
<tr>
    <td colspan="11">No Order found</td>
</tr>
@endforelse
