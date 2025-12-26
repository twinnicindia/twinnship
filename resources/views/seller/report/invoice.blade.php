<input type="hidden" id="total_page" name="totalpage" value="{{ $invoice->total() }}">
@if(!empty($invoice))
    @foreach($invoice as $i)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$i->id}}"></td>
            <td>{{$i->inv_id}}</td>
            <td>{{$i->invoice_date}}</td>
            <td>{{$i->due_date}}</td>
            <td>{{$i->total}}</td>
            <td><button class="btn btn-sm btn-success">{{$i->status}}</button></td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6">No Recharge Data Found</td>
    </tr>
@endif
