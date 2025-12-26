<h5 class="text-dark mb-4">RTO Order Details</h5>
<div class="card db-card-bg mb-3 mb-md-5 p-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <div class="text-center">
                    <div class="count">{{$rto_initiated + $rto_undelivered + $rto_undelivered}}</div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="text-center">
                    <div class="count">@if($total_order != '0'){{round((($rto_initiated + $rto_undelivered + $rto_undelivered) / $total_order) *100,2)}} @endif %</div>
                    <p class="mb-0 text-muted">RTO Percentage</p>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
<div class="card db-card-bg mb-3 mb-md-4 p-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-sm-4">
                <div class="text-center">
                    <div class="count">{{$rto_initiated}}</div>
                    <p class="mb-0 text-muted">RTO Initiated</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="text-center">
                    <div class="count">{{$rto_undelivered}}</div>
                    <p class="mb-0 text-muted">RTO In Transit</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="text-center">
                    <div class="count">{{$rto_delivered}}</div>
                    <p class="mb-0 text-muted">RTO Delivered</p>
                </div>
            </div>
        </div>
    </div>
</div>
