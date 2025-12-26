<input type="hidden" id="total_page" name="totalpage" value="{{ $remittance->total() }}">
@if(!empty($remittance))
    @foreach($remittance as $r)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$r->id}}"></td>
            <td>{{$r->datetime}}</td>
            <td>{{$r->crf_id}}</td>
            <td>{{$r->utr_number}}</td>
            <td> &#x20b9; 0.00</td>
            <td> &#x20b9; 0.00</td>
            <td> &#x20b9; 0.00</td>
            <td>{{$r->amount}}</td>
            <td class="text-capitlize">{{$r->pay_type}}</td>
            <td>{{$r->description}}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9">No Recharge Data Found</td>
    </tr>
@endif
