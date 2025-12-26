<input type="hidden" id="total_invoice" name="totalpage" value="{{$invoice->total()}}">
@forelse($invoice as $i)
<tr>
    <td>{{$i->inv_id}}</td>
    <td>{{$i->invoice_date}}</td>
    <td>{{$i->due_date}}</td>
    <td>{{$i->total}}</td>
    <td><button class="btn btn-success">{{$i->status}}</button></td>
    <td><a href='{{url("billing_other_invoice/view/$i->id")}}'>View Invoice</a></td>
</tr>
@empty
<tr>
    <td colspan="6">No Invoice Data Found</td>
</tr>
@endforelse