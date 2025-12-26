<div class="table-container" style="overflow-x: auto;">
<table id="example1" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Courier Partner</th>
        <th>Within City</th>
        <th>Within State</th>
        <th>Metro to Metro</th>
        <th>Rest of India</th>
        <th>North East & J.K</th>
        <th>COD Charges</th>
        <th>COD Maintenance(%)</th>
        <th>Extra Charge Zone - A</th>
        <th>Extra Charge Zone - B</th>
        <th>Extra Charge Zone - C</th>
        <th>Extra Charge Zone - D</th>
        <th>Extra Charge Zone - E</th>
    </tr>
    </thead>
    <tbody>
    @foreach($ratesData as $a)
        <tr id="row{{$a->id}}">
            <td>{{$a->title}}</td>
            <td>{{$a->within_city}}</td>
            <td>{{$a->within_state}}</td>
            <td>{{$a->metro_to_metro}}</td>
            <td>{{$a->rest_india}}</td>
            <td>{{$a->north_j_k}}</td>
            <td>{{$a->cod_charge}}</td>
            <td>{{$a->cod_maintenance}}</td>
            <td>{{$a->extra_charge_a}}</td>
            <td>{{$a->extra_charge_b}}</td>
            <td>{{$a->extra_charge_c}}</td>
            <td>{{$a->extra_charge_d}}</td>
            <td>{{$a->extra_charge_e}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
