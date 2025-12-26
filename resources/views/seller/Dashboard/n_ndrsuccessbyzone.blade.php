<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">Success by Zone</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:190px;"></th>
                    <th>Total</th>
                    <th>Zone A</th>
                    <th>Zone B</th>
                    <th>Zone C</th>
                    <th>Zone D</th>
                    <th>Zone E</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>NDR Raised</td>
                    <td>{{$z_ndr_raised_A + $z_ndr_raised_B + $z_ndr_raised_C + $z_ndr_raised_D + $z_ndr_raised_E}}</td>
                    <td>{{$z_ndr_raised_A}}</td>
                    <td>{{$z_ndr_raised_B}}</td>
                    <td>{{$z_ndr_raised_C}}</td>
                    <td>{{$z_ndr_raised_D}}</td>
                    <td>{{$z_ndr_raised_E}}</td>
                </tr>
                <tr>
                    <td>NDR Delivered</td>
                    <td>{{$z_ndr_delivered_A + $z_ndr_delivered_B + $z_ndr_delivered_C + $z_ndr_delivered_D + $z_ndr_delivered_E}}</td>
                    <td>{{$z_ndr_delivered_A}}</td>
                    <td>{{$z_ndr_delivered_B}}</td>
                    <td>{{$z_ndr_delivered_C}}</td>
                    <td>{{$z_ndr_delivered_D}}</td>
                    <td>{{$z_ndr_delivered_E}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
