<div class="card-body">
    <div class="row">
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                <div class="count">{{$total_ndr}}</div>
                <p class="mb-0 text-muted">NDR</p>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                @if($total_ndr != 0)
                    <div class="count">{{round(($total_ndr / $total_order) * 100,2)}} %</div>
                @else
                    <div class="count">0 %</div>
                @endif
                <p class="mb-0 text-muted">Raised Percentage</p>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                <div class="count">{{$action_required}}</div>
                <p class="mb-0 text-muted">Action Required</p>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                <div class="count">{{$action_requested}}</div>
                <p class="mb-0 text-muted">Action Requested</p>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                <div class="count">{{$ndr_delivered}}</div>
                <p class="mb-0 text-muted">NDR Delivered</p>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="text-center">
                <div class="count">{{$ndr_rto}}</div>
                <p class="mb-0 text-muted">RTO</p>
            </div>
        </div>
    </div>
</div>
