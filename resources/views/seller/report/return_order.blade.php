<input type="hidden" id="total_page" name="totalpage" value="{{ $order->total() }}">
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
        "rto_in_transit" => "RTO In Transit",
        "rto_delivered" => "RTO Delivered",
        "delivered" => "Delivered",
        "ndr" => "NDR",
        "lost" => "Lost",
        "damaged" => "Damaged",
        "hold" => "Hold"
    );
@endphp
@forelse($order as $key=>$o)
    <tr id="row{{$o->id}}">
        <td><input type="checkbox" class="selectedCheck" value="{{$o->id}}"></td>
        <td>{{$key+1}}</td>
        <?php
        $date = date('d/m/Y', strtotime($o->inserted));
        $time = date('h:i A', strtotime($o->inserted));
        ?>
        <td>Date : {{$date}} <br> Time : {{$time}}</td>
        <td>{{$o->channel==''?"Custom" : "$o->channel"}}</td>
        <td><a href='{{url("/view-order/$o->id")}}' target="_blank">{{$o->customer_order_number}}</a><br>
            <span class="{{$o->status=='pending' ? 'text-danger' : 'text-primary'}} font-weight-bold text-capitalize">{{$orderStatus[$o->status]}}</span>
            <br>
            <span class="badge {{$o->o_type =='forward'?'badge-success':'badge-danger'}}">{{$o->o_type}}</span><br>
        </td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            <td><span class="{{$o->invoice_amount == '' ? 'text-danger font-weight-bold' : ''}}">Amount </span> : PII Data Archived<br><span class="badge badge-success">{{$o->order_type}}</span>@if($o->shipment_type == 'mps')<span class="badge badge-primary pl-2">MPS</span>@endif</td>
        @else
            <td><span class="{{$o->invoice_amount == '' ? 'text-danger font-weight-bold' : ''}}">Amount </span> : {{$o->invoice_amount}}<br><span class="badge badge-success">{{$o->order_type}}</span>@if($o->shipment_type == 'mps')<span class="badge badge-primary pl-2">MPS</span>@endif</td>
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
                <span class="{{$o->b_customer_name == '' ? 'text-danger font-weight-bold' : ''}}">Name</span> :{{strlen($o->b_customer_name) > 15 ? substr("$o->b_customer_name",0,15)."..." : $o->b_customer_name}} @if(strlen($o->b_customer_name) > 15)<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="{{$o->b_customer_name}}"><i class="fas fa-eye text-primary"></i></a>@endif<br>
                <span class="{{$o->b_contact == '' ? 'text-danger font-weight-bold' : ''}}">Contact</span> :{{$o->b_contact}}
            </td>
        @endif
        <td>{{$o->p_warehouse_name}}
            <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->p_address_line1}} <br> {{$o->p_address_line2}} <br> {{$o->p_city}} {{$o->p_state}} {{$o->p_pincode}}"><i class="fas fa-eye text-primary"></i></a>
        </td>
        <td>
            @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                PII Data Archived
            @else
                {{$o->s_state}} <br>
                {{$o->s_city}} <br>
                {{$o->s_pincode}} <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->s_address_line1}} <br> {{$o->s_address_line2}} <br> {{$o->s_city}} {{$o->s_state}} {{$o->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
            @endif
        </td>
        <td>
            <div>
                <span class="{{$o->weight == '' ? 'text-danger font-weight-bold' : ''}}">Weight</span> : {{$o->weight != '' ? $o->weight / 1000 : ''}}<br>
                <span class="{{$o->height == '' ? 'text-danger font-weight-bold' : ''}}">Height</span> : {{$o->height}}<br>
                <span class="{{$o->length == '' ? 'text-danger font-weight-bold' : ''}}">Length</span> : {{$o->length}}<br>
                <span class="{{$o->breadth == '' ? 'text-danger font-weight-bold' : ''}}">Breadth</span> : {{$o->breadth}}<br>
            </div>
        </td>
        <td>
            -
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11">No Order found</td>
    </tr>
@endforelse
