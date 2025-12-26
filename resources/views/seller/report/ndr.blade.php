<input type="hidden" id="total_page" name="totalpage" value="{{ $ndr_data->total() }}">
@if(count($ndr_data) != 0)
    @php($cnt=1)
    @foreach($ndr_data as $n)
        <tr id="row{{$n->id}}">
            <td><input type="checkbox" class="selectedCheck" value="{{$n->id}}"></td>
            <td>
                {{$n->raised_date}}
            </td>
            <td>{{count($n->ndrattempts) == 0 ? 1 : count($n->ndrattempts)}}  Attempt<br>
                Status : {{$n->ndr_action}}<br>
                Last NDR : {{$n->reason_for_ndr}}
            </td>
            <td>
                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                    Number : <span class="text-primary">{{$n->order_number}}</span><bR>
                    Name : PII Data Archived<bR>
                    SKU : PII Data Archived<bR>
                    Pincode : PII Data Archived<bR>
                    Mode : {{$n->order_type}}<bR>
                    Amount : PII Data Archived
                @else
                    Number : <span class="text-primary">{{$n->order_number}}</span><bR>
                    Name : {{$n->product_name}}<bR>
                    SKU : {{$n->product_sku}}<bR>
                    Pincode : {{$n->b_pincode}}<bR>
                    Mode : {{$n->order_type}}<bR>
                    Amount : {{$n->invoice_amount}}
                @endif
            </td>
            <td>
                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                    PII Data Archived<bR>
                    PII Data Archived<bR>
                @else
                    {{$n->b_customer_name}}<bR>
                    {{$n->b_contact}}<bR>
                @endif
            </td>
            <td>
                {{$PartnerName[$n->courier_partner]}}<bR>
                <span class="text-primary">{{$n->awb_number}}</span><bR>
            </td>
            <td>
                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($n->channel), ['amazon', 'amazon_direct']) && now()->parse($n->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                    PII Data Archived<bR>
                    PII Data Archived<bR>
                    PII Data Archived<bR>
                @else
                    {{$n->b_address_line1}}<bR>
                    {{$n->b_address_line2}}<bR>
                    {{$n->b_city}}{{$n->b_pincode}}<bR>
                @endif
            </td>
            <td>N / A</td>
            <td><span class="text-success">{{$n->ndrattempts[count($n->ndrattempts) - 1]->action_by ?? $PartnerName[$n->courier_partner] }}</span><bR>
                Remarks : {{$n->remark}}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="10">No NDR Data found</td>
    </tr>
@endif
