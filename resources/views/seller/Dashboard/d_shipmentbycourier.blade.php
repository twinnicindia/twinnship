<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">Shipment Overview by Courier</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Courier Name</th>
                    <th>Yet to Pick</th>
                    <th>Picked</th>
                    <th>In-Transit</th>
                    <th>Delivered</th>
                    <th>NDR Pending</th>
                    <th>NDR Delivered</th>
                    <th>RTO</th>
                    <th>Lost/Damaged</th>
                    <th>Total Shipment</th>
                </tr>
                </thead>
                <tbody>
                @foreach($allPartners as $p)
                    <tr>
                        <td>{{$PartnerName[$p]?? ""}}</td>
                        <td>{{$partner_unscheduled[$p] ?? 0}}</td>
                        <td>{{$partner_scheduled[$p] ?? 0}}</td>
                        <td>{{$partner_intransit[$p] ?? 0}}</td>
                        <td>{{$partner_delivered[$p] ?? 0}}</td>
                        <td>{{$partner_ndr_pending[$p] ?? 0}}</td>
                        <td>{{$partner_ndr_delivered[$p] ?? 0}}</td>
                        <td>{{$partner_ndr_rto[$p] ?? 0}}</td>
                        <td>{{$partner_damaged[$p] ?? 0}}</td>
                        <td>{{$partner_total[$p] ?? 0}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>Other</td>
                    <td>{{$other_partner_unscheduled ?? 0}}</td>
                    <td>{{$other_partner_scheduled ?? 0}}</td>
                    <td>{{$other_partner_intransit ?? 0}}</td>
                    <td>{{$other_partner_delivered ?? 0}}</td>
                    <td>{{$other_partner_ndr_pending ?? 0}}</td>
                    <td>{{$other_partner_ndr_delivered ?? 0}}</td>
                    <td>{{$other_partner_ndr_rto ?? 0}}</td>
                    <td>{{$other_partner_damaged ?? 0}}</td>
                    <td>{{$other_partner_total ?? 0}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
