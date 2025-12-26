@if(!empty($remittance))
@foreach($remittance as $r)
<tr>
    <td>{{$r->datetime}}</td>
    <td>{{$r->id}}</td>
    <td>{{$r->utr_number}}</td>
    <td> &#x20b9; 0.00</td>
    <td>{{$r->early_cod_charge}}</td>
    <td> &#x20b9; 0.00</td>
    <td>{{$r->amount}}</td>
    <td>{{$r->pay_type}}</td>
    <td>{{$r->description}}</td>
</tr>
@endforeach
@else
<tr>
    <td colspan="9">No Recharge Data Found</td>
</tr>
@endif