@php($cnt=1)
@foreach($rates as $r)
    @if(!empty($r[0]))
        @php($r=$r[0])
        <?php
            $cod_charge=0;
            if($order_type=="COD"){
                $cod_charge= ( $invoice_amount * $r->cod_maintenance) / 100;
                if($cod_charge < $r->cod_charge)
                    $cod_charge=$r->cod_charge;
            }
        ?>
        <tr>
            <td>{{$cnt++}}</td>
            <td>{{$partners[$r->partner_id]}}{{$partners[$r->partner_id] == "Gati" ? " **" : ""}}</td>
            <td>{{$r->price}}</td>
            <td>{{$cod_charge}}</td>
            <td>{{$cod_charge + $r->price}}</td>
        </tr>
    @endif
@endforeach
