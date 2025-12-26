<div class="card db-card-bg mb-3 mb-md-4 shipment_details cursor-pointer">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Order Details</h5>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$total_all_orders ?? 0}}</div>
            <p class="mb-0 text-muted">Total Orders</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$shipped_orders ?? 0}}</div>
            <p class="mb-0 text-muted">Shipped Orders</p>
        </div>
    </div>
</div>
<div class="card db-card-bg mb-3 mb-md-4 shipment_details cursor-pointer">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Shipment Details</h5>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$pending_order ?? 0}}</div>
            <p class="mb-0 text-muted">Yet to Pick</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$picked_up ?? 0}}</div>
            <p class="mb-0 text-muted">Picked</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$intransit_order ?? 0}}</div>
            <p class="mb-0 text-muted">In-Transit</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$out_for_delivery ?? 0}}</div>
            <p class="mb-0 text-muted">Out for Delivery</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$delivered_order ?? 0}}</div>
            <p class="mb-0 text-muted">Delivered</p>
        </div>
        <div class="mb-3 mb-md-4 text-center">
            <div class="count">{{$ndr_pending ?? 0}}</div>
            <p class="mb-0 text-muted">NDR</p>
        </div>
        <div class="mb-0 text-center">
            <div class="count">{{$rto_order ?? 0}}</div>
            <p class="mb-0 text-muted">RTO</p>
        </div>
    </div>
</div>
