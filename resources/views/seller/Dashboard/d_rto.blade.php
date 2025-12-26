<div class="card-body">
    <div class="card-title mb-md-4">
        <h5 class="title">RTO Details</h5>
    </div><br>
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$rto_order ?? 0}}</div>
                <p class="mb-0 text-muted">RTO</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$rto_initiated ?? 0}}</div>
                <p class="mb-0 text-muted">RTO Initiated</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$rto_undelivered ?? 0}}</div>
                <p class="mb-0 text-muted">RTO In Transit</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$rto_delivered ?? 0}}</div>
                <p class="mb-0 text-muted">RTO Delivered</p>
            </div>
        </div>
    </div>
</div>
