@if(!empty($billing_data))
    <input type="hidden" value="{{ $billing_data->total() }}" id ="total_ajax_billing_data">
    @foreach($billing_data as $b)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$b->id}}"></td>
            <td><a href="https://www.Twinnship.in/view-order/{{$b->id}}" target="_blank">{{$b->customer_order_number}}</a></td>
            <td><a href="https://www.Twinnship.in/track-order/{{$b->awb_number}}" target="_blank">{{$b->awb_number}}</a></td>
            <td>{{$PartnerName[$b->courier_partner] ?? "Not Found"}}</td>
            <td>{{$b->status}}</td>
            <td>{{$b->awb_assigned_date}}</td>
            <td>{{$b->total_charges}}</td>
            <td>{{$b->excess_weight_charges}}</td>
            <td></td>
            <td>{{$b->total_charges + $b->excess_weight_charges}}</td>
            <td>{{$b->weight / 1000}} kg <br>
                {{$b->length}} * {{$b->breadth}} * {{$b->height}} cm
            </td>
            @if(isset($b->c_weight))
                <td>{{$b->c_weight / 1000}} kg <br>
                    {{$b->c_length}} * {{$b->c_breadth}} * {{$b->c_height}} cm
                </td>
            @else
                <td></td>
            @endif
            <td><button class="btn btn-primary view_transaction" data-id="{{$b->id}}">View</button></td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="13">No Billing Details found</td>
    </tr>
@endif
