<div class="col-md-3 mb-3 mb-md-4">
    <div class="card db-card-bg order_details">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="media">
                    <span><i class="mdi mdi-cart-outline mdi-48px f-s-40 text-success"></i></span>
                </div>
                <div class="media-body text-left">
                    <div class="h5">Today's Order</div>
                    <div class="count">{{$today_order ?? 0}}</div>
                    <p class="mb-0 text-muted">Created {{$total_created ?? 0}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 mb-3 mb-md-4">
    <div class="card db-card-bg">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="media">
                    <span><i class="far fa-inr fa-3x text-primary ml-1"></i></span>
                </div>
                <div class="media-body text-left">
                    <div class="h5">Today's Revenue</div>
                    <div class="count">{{round($today_revenue,2)}}</div>
                    <p class="mb-0 text-muted">Yesterday {{round($yesterday_revenue,2)}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 mb-3 mb-md-4">
    <div class="card db-card-bg">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="media">
                    <span><i class="mdi mdi-archive-outline mdi-48px text-warning"></i></span>
                </div>
                <div class="media-body text-left">
                    <div class="h5">Average Selling Price </div>
                    <div class="count">{{round($total_revanue,2)}}</div>
                    <p class="mb-0 text-muted">Seller</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 mb-3 mb-md-4">
    <div class="card db-card-bg">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="media">
                    <span><i class="mdi mdi-emoticon-happy-outline mdi-48px text-danger"></i></span>
                </div>
                <div class="media-body text-left">
                    <div class="h5">Total Customer</div>
                    <div class="count">{{$total_customer ?? 0}}</div>
                    <p class="mb-0 text-muted">Seller</p>
                </div>
            </div>
        </div>
    </div>
</div>
