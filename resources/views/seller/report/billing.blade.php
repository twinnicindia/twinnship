<input type="hidden" id="total_page" name="totalpage" value="{{ $billing->total() }}">
@if(!empty($billing))
    @foreach($billing as $b)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$b->id}}"></td>
            <td>{{$b->customer_order_number}}</td>
            <td>{{$b->awb_number}}</td>
            <td>{{isset($PartnerName[$b->courier_partner])}}</td>
            <td>{{$b->status}}</td>
            <td>{{$b->awb_assigned_date}}</td>
            <td>{{$b->total_charges}}</td>
            <td>{{$b->excess_weight_charges !=''?$b->excess_weight_charges : '-'}}</td>
            <td> - </td>
            <td>{{$b->total_charges + $b->excess_weight_charges}}</td>
            <td>{{$b->weight != '' ? $b->weight / 1000 : ''}} kg <br>
                {{$b->length}} * {{$b->breadth}} * {{$b->height}} cm
            </td>
            @if(isset($b->c_weight))
                <td>{{$b->c_weight != '' ? $b->x_weight / 1000 : ''}} kg <br>
                    {{$b->c_length}} * {{$b->c_breadth}} * {{$b->c_height}} cm
                </td>
            @else
                <td> - </td>
            @endif
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="13">No Billing Details found</td>
    </tr>
@endif
