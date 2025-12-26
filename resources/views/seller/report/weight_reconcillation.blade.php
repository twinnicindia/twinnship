<input type="hidden" id="total_page" name="totalpage" value="{{ $weight_reconciliation->total() }}">
@if(!empty($weight_reconciliation))
    @foreach($weight_reconciliation as $w)
        <tr>
            <td>
                <input type="checkbox" class="selectedCheck" data-status="{{$w->status}}" value="{{$w->id}}">
            </td>
            <td>{{$w->order_number}}<br>
                {{$w->created}}
            </td>
            <td>{{$w->channel}}</td>
            <td>
                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($w->channel), ['amazon', 'amazon_direct']) && now()->parse($w->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                    PII Data Archived
                @else
                    Name : @foreach(explode(',', $w->product_name) as $name)
                            {{$name}}<br>
                        @endforeach
                        SKU : @foreach(explode(',', $w->product_sku) as $sku)
                            {{$sku}}<br>
                        @endforeach
                @endif
            </td>
            <td>
                @if(env('AMAZON_PII_ARCHIVE') == true && in_array(strtolower($w->channel), ['amazon', 'amazon_direct']) && now()->parse($w->inserted)->lte(now()->subDays(env('AMAZON_PII_ARCHIVE_DAY'))))
                    PII Data Archived
                @else
                    {{$w->invoice_amount}}
                @endif
            </td>
            <td><span class="text-primary">AWB : {{$w->awb_number}} </span><br>
                Courier : {{$PartnerName[$w->courier_partner]}}</td>
            <td>{{$w->e_weight / 1000}} kg <br>
                {{$w->e_length}} * {{$w->e_breadth}} * {{$w->e_height}} cm <br>
                Applied Amount : {{$w->applied_amount}}
            </td>
            @if(isset($w->c_weight))
                <td>{{$w->c_weight / 1000}} kg <br>
                    {{$w->c_length}} * {{$w->c_breadth}} * {{$w->c_height}} cm<br>
                    Charged Amount : {{$w->charged_amount}}
                </td>
            @else
                <td></td>
            @endif
            <td>{{$w->w_status}}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="10">No Billing Details found</td>
    </tr>
@endif
