<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:190px;"></th>
                    <th>NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Count of NDR Raised Shipment"></i><br>Shipment</th>
                    <th>1st NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 1st NDR Attempt is made"></i><br>Attempt</th>
                    <th>1st NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 1st NDR Attempt"></i><br>Delivered</th>
                    <th>2nd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 2nd NDR Attempt is made"></i><br>Attempt</th>
                    <th>2nd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 2nd NDR Attempt"></i><br>Delivered</th>
                    <th>3rd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 3rd NDR Attempt is made"></i><br>Attempt</th>
                    <th>3rd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 3rd NDR Attempt"></i><br>Delivered</th>
                    <th>Total Delivered</th>
                    <th>Total RTO</th>
                    <th>Lost/Damaged</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Total NDR Raised</td>
                    <td>{{$total_ndr}}</td>
                    <td>{{($attempt1_pending ?? 0)}}</td>
                    <td>{{($attempt1_delivered ?? 0)}}</td>
                    <td>{{($attempt2_pending ?? 0)}}</td>
                    <td>{{($attempt2_delivered ?? 0)}}</td>
                    <td>{{($attempt3_pending ?? 0)}}</td>
                    <td>{{($attempt3_delivered ?? 0)}}</td>
                    <td>{{($attempt1_delivered ?? 0) + ($attempt2_delivered ?? 0) + ($attempt3_delivered ?? 0)}}</td>
                    <td>{{($attempt1_rto ?? 0) + ($attempt2_rto ?? 0) + ($attempt3_rto ?? 0)}}</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Seller Response</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Seller Positive Response</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Buyer Response</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Buyer Postive Response</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
