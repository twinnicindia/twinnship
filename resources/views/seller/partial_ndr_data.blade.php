<input type="hidden" value="@if(isset($count_ajax_ndr_data)) {{$count_ajax_ndr_data}} @endif" id ="total_ajax_ndr_data">
@forelse($ndr_data as $n)
<tr id="row{{$n->id}}">
    @if($n->status != 'delivered')
    <td><input type="checkbox" class="selectedCheck" value="{{$n->id}}"></td>
    @endif
    <td>
        {{$n->ndr_status_date}}
    </td>
    <td>{{count($n->ndrattempts) > 0 ? count($n->ndrattempts) : 1}}  Attempt<br>
        Status : <span class="text-capitalize">{{$n->ndr_action}}</span><br>
        Last NDR : {{$n->reason_for_ndr ?? ""}}
    </td>
    <td>
        @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
            Number : <span class="text-primary">{{$n->customer_order_number}}</span><bR>
            Name : PII Data Archived<br>
            SKU : PII Data Archived<br>
            Type : <span class="badge {{$n->o_type =='forward'?'badge-success':'badge-danger'}}">{{$n->o_type}}</span>
        @else
            Number : <span class="text-primary">{{$n->customer_order_number}}</span><bR>
            Name : {{strlen($n->product_name) > 15 ? substr("$n->product_name",0,15)."..." : $n->product_name}}<br>
            SKU : {{strlen($n->product_sku) > 15 ? substr("$n->product_sku",0,15)."..." : $n->product_sku}} <br>
            Type : <span class="badge {{$n->o_type =='forward'?'badge-success':'badge-danger'}}">{{$n->o_type}}</span>
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
    <td>N / A</td>
    <!-- <td><span class="text-success">{{$n->action_by ?? ""}}</span><bR>
        Remarks : {{$n->remark ?? ""}}</td> -->
    @if($n->status != 'delivered')
    <td>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Reattempt" data-id="{{$n->id}}" class="btn btn-success btn-sm reattempt_btn mx-0"><i class="far fa-undo-alt"></i></a>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="RTO" data-id="{{$n->id}}" class="btn btn-info btn-sm rto_btn mx-0"><i class="far fa-angle-double-left"></i></a>
        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Escalate" data-id="{{$n->id}}" class="btn btn-danger btn-sm escalate_btn mx-0"><i class="far fa-radiation"></i></a>
    </td>
    @endif
</tr>
@empty
<tr>
    <td colspan="10">No Order found</td>
</tr>
@endforelse
