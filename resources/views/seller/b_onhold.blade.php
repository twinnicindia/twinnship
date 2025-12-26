@if($onhold->total() != 0)
    @foreach($onhold as $o)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$o->w_id}}"></td>
            <td>{{$o->created}}</td>
            <td><a href='{{url("/view-order/$o->id")}}' target="_blank">{{$o->customer_order_number}}</a></td>
            <td><a href='{{url("track-order/$o->awb_number")}}' target="_blank">{{$o->awb_number}}</a></td>
            <td>{{$PartnerName[$o->courier_partner]}}</td>
            <td>{{$o->charged_amount - $o->total_charges}}</td>
            <td>
                FWD : {{$o->total_charges}}<br>
                RTO : {{$o->o_type=='reverse' ? $o->rto_charges : '0'}}
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4">No Onhold Data Found</td>
    </tr>
@endif
<input type="hidden" id="total_onhold" name="totalpage" value="{{$onhold->total()}}">
