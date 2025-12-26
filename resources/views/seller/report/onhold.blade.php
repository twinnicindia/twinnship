<input type="hidden" id="total_page" name="totalpage" value="{{ $onhold->total() }}">
@if(!empty($onhold))
    @foreach($onhold as $o)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$o->id}}"></td>
            <td>{{$o->awb_assigned_date}}</td>
            <td>{{$o->order_number}}</td>
            <td>{{$o->awb_number}}</td>
            <td>{{$PartnerName[$o->courier_partner]}}</td>
            <td>-</td>
            <td>
                FWD : {{$o->total_charges}}<br>
                RTO : {{$o->rto_charges}}
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6">No Onhold Data Found</td>
    </tr>
@endif
