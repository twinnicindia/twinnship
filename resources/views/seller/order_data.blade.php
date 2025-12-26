@if(count($order)!=0)

@php($cnt=1)
<input type="hidden" id="total_order" value="{{count($order)}}">
@foreach($order as $o)
<tr id="row{{$o->id}}" data-sku="{{$o->product_sku}}">
    <td><input type="checkbox" class="selectedCheck" data-status="{{$o->status}}" value="{{$o->id}}"></td>
    <?php
    $date = date('d/m/Y', strtotime($o->inserted));
    $time = date('h:i A', strtotime($o->inserted));
    ?>
    <td>Date : {{$date}} <br> Time : {{$time}}</td>
    <td>{{$o->channel==''?"Custom" : "$o->channel"}}</td>
    <td><a href='{{url("/view-order/$o->id")}}'>{{$o->customer_order_number}}</a>@if($o->is_tagged == 'y' && $o->channel == 'shopify')&nbsp;&nbsp;<a class="shopify_tags" data-id="{{$o->id}}"><i class="fa fa-tag"></i></a>@endif<br>
        @if($o->status=='pending')
        <span class="text-danger font-weight-bold">{{$o->status}}</span>
        @elseif($o->status == 'shipped')
        <span class="text-success font-weight-bold">{{$o->status}}</span>
        @else
        <span class="text-primary font-weight-bold">{{$o->status}}</span>
        @endif
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived
        @else
            {{$o->invoice_amount}}<br>
        @endif
        <span class="badge badge-success">{{$o->order_type}}</span>
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived
        @else
            Name : @foreach(explode(',', $o->product_name) as $name)
            {{$name}}<br>
            @endforeach
            SKU : @foreach(explode(',', $o->product_sku) as $sku)
            {{$sku}}<br>
            @endforeach
        @endif
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($o->channel), ['amazon', 'amazon_direct']) && now()->parse($o->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            PII Data Archived
        @else
            {{$o->b_customer_name}}
        @endif
    </td>
    <td>{{$o->p_address_line1}} <br>
        {{$o->p_address_line2}} <br>
        {{$o->p_city}} <br>
        {{$o->p_pincode}}</td>
    <td>{{$o->b_address_line1}} <br>
        {{$o->b_address_line2}} <br>
        {{$o->b_city}} <br>
        {{$o->b_pincode}}</td>
    <td class="OD-{{$o->id}}">
        <div>
            <span class="{{$o->weight == '' ? 'text-danger font-weight-bold' : ''}}">Weight</span> : {{$o->weight}}<a class="dimensiondata ml-1" data-placement="top" data-toggle="tooltip" data-original-title="Edit Dimension" data-id="{{$o->id}}"> <i class="fas fa-edit fa-lg"></i></a><br>
            <span class="{{$o->height == '' ? 'text-danger font-weight-bold' : ''}}">Height</span> : {{$o->height}}<br>
            <span class="{{$o->length == '' ? 'text-danger font-weight-bold' : ''}}">Length</span> : {{$o->length}}<br>
            <span class="{{$o->breadth == '' ? 'text-danger font-weight-bold' : ''}}">Breadth</span> : {{$o->breadth}}<br>
        </div>
    </td>
    <td>
        <button type="button" class="btn btn-primary shipOrderButton btn-sm mx-0" title="Ship Order" data-id="{{$o->id}}" data-status="{{$o->status}}" data-placement="top" data-toggle="tooltip" data-original-title="Ship Order"><i class="fas fa-shipping-fast"></i></button>
        <button type="button" title="Cancel Order" class="btn btn-danger cancelOrderButton btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order"> <i class="fas fa-times"></i> </button>
        <button type="button" title="Edit Order" class="btn btn-primary modify_data btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Edit Order"> <i class="fas fa-pencil"></i> </button>
        <a href="javascript:;" title="Delete Order" data-id="{{$o->id}}" class="btn btn-danger btn-sm remove_data mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Delete Order"><i class="fa fa-trash"></i></a>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="11">No Order found</td>
</tr>
@endif
