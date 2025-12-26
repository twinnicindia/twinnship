<div class="card-body">
    <div class="card-title mb-md-4">
        <h5 class="title">NDR Details</h5>
    </div><br>
    <div class="row">
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$total_ndr ?? 0}}</div>
                <p class="mb-0 text-muted">NDR</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$action_required ?? 0}}</div>
                <p class="mb-0 text-muted">Action Required</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$action_requested ?? 0}}</div>
                <p class="mb-0 text-muted">Action Requested</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$ndr_delivered ?? 0}}</div>
                <p class="mb-0 text-muted">Delivered</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$ndr_rto ?? 0}}</div>
                <p class="mb-0 text-muted">RTO</p>
            </div>
        </div>
    </div>
</div>
