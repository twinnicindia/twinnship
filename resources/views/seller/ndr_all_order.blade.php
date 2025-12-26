<input type="hidden" id="pagecountNdrAll" name="totalpage" value="{{$order->total()}}">
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
        "hold" => "Hold"
    );
@endphp
@forelse($order as $n)
<tr id="row{{$n->id}}">
    <td><input type="checkbox" class="selectedCheck" value="{{$n->id}}"></td>
    <td>
        {{date('d-m-Y',strtotime($n->ndr_raised_time))}}
    </td>
    <td>
        <a href='{{url("/view-order/$n->id")}}' target="_blank"><span class="text-primary">{{$n->customer_order_number}}</span></a><br>
        <span class="text-primary font-weight-bold text-capitalize">Action Required</span><br>
        <span class="badge {{$n->o_type =='forward'?'badge-success':'badge-danger'}}">{{$n->o_type}}</span>
        <span class="badge badge-success">{{$n->order_type}}</span>
        @if($n->shipment_type == 'mps')<span class="badge badge-primary pl-2">MPS</span>@endif
    </td>
    <td>{{count($n->ndrattempts) == 0 ? 1 : count($n->ndrattempts)}}  Attempt &nbsp;<i class="fas fa-eye text-primary ndrHistory" data-id="{{$n->id}}"></i><br>
        Status : <span class="text-capitalize">{{$n->ndr_action}}</span><br>
        Last NDR : {{strlen($n->reason_for_ndr) > 15 ? substr("$n->reason_for_ndr",0,15)."..." : $n->reason_for_ndr}}
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived
        @else
            Name : {{strlen($n->product_name) > 15 ? substr("$n->product_name",0,15)."..." : $n->product_name}}<br>
            SKU : {{strlen($n->product_sku) > 15 ? substr("$n->product_sku",0,15)."..." : $n->product_sku}} <br>
            Qty : {{$n->product_qty ?? 1}} &nbsp;<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="Name : @foreach(explode(',', $n->product_name) as $name) {{$name}} @endforeach <br> SKU : @foreach(explode(',', $n->product_sku) as $sku) {{$sku}} @endforeach <br> QTY : {{$n->product_qty ?? 1}}"><i class="fas fa-eye text-primary"></i></a>
        @endif
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived<bR>
        @else
            {{$n->s_customer_name}}<bR>
            {{$n->s_contact}}<bR>
        @endif
    </td>
    <td>
        {{$PartnerName[$n->courier_partner] ?? ""}}<bR>
        <span class="text-primary"><a href='{{url("track-order/$n->awb_number")}}' target="_blank">{{$n->awb_number}}</a></span><bR>
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived<bR>
        @else
            {{$n->s_state}} <br>
            {{$n->s_city}} <br>
            {{$n->s_pincode}} <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$n->s_address_line1}} <br> {{$n->s_address_line2}} <br> {{$n->s_city}} {{$n->s_state}} {{$n->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
        @endif
    </td>
    <td>
        @php
            $escalate = \App\Models\SupportTicket::where('awb_number',$n->awb_number)->first();
        @endphp
        @if(!empty($escalate))
            Issue : {{$escalate->issue ?? ''}} <br>
            Status : {{$escalate->status=='o' ? 'Open' : 'Close'}}
        @else
        N / A
        @endif
    </td>
    <!-- <td><span class="text-success">{{$n->action_by ?? ""}}</span><bR>
        Remarks : {{$n->remark ?? ""}}</td> -->
    <td>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Re-Attempt" data-id="{{$n->id}}" class="btn btn-success btn-sm reattempt_btn mx-0"><i class="far fa-undo-alt"></i></a>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="RTO" data-id="{{$n->id}}" class="btn btn-info btn-sm rto_btn mx-0"><i class="far fa-angle-double-left"></i></a>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Escalate" data-id="{{$n->id}}" class="btn btn-danger btn-sm escalate_btn mx-0"><i class="far fa-radiation"></i></a>
    </td>
</tr>
@empty
<tr>
    <td colspan="10">No Order found</td>
</tr>
@endforelse
