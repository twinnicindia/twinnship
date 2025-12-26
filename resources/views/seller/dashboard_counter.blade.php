
                            <div class="row">
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
                            </div>
                            <div class="row">
                                <div class="col-md-3">
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
                                </div>
                                <div class="col-md-9">
                                    <div class="card db-card-bg mb-3 mb-md-4 ndr_details cursor-pointer" style="height: 250px">
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
                                    </div>
                                    <div class="card db-card-bg mb-3 mb-md-4" style="height: 304px">
                                        <div class="card-body">
                                            <div class="card-title mb-md-4">
                                                <h5 class="title">COD Status</h5>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md col-sm-6">
                                                    <div class="mb-3 mb-md-4 text-center">
                                                        <div class="count">{{$cod_total ?? 0}}</div>
                                                        <p class="mb-0 text-muted">COD</p>
                                                    </div>
                                                </div>
{{--                                                <div class="col-md-3 col-sm-6">--}}
{{--                                                    <div class="mb-3 mb-md-4 text-center">--}}
{{--                                                        <div class="count">{{$cod_available ?? 0}}</div>--}}
{{--                                                        <p class="mb-0 text-muted">COD Available</p>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
                                                <div class="col-md col-sm-6">
                                                    <div class="mb-3 mb-md-4 text-center">
                                                        <div class="count">{{$remitted_cod ?? 0}}</div>
                                                        <p class="mb-0 text-muted">COD Remitted</p>
                                                    </div>
                                                </div>
                                                <div class="col-md col-sm-6">
                                                    <div class="mb-3 mb-md-4 text-center">
                                                        <div class="count">{{($cod_total - $remitted_cod) ?? 0}}</div>
                                                        <p class="mb-0 text-muted">COD Pending</p>
                                                    </div>
                                                </div>
                                                <div class="col-md col-sm-6">
                                                    <div class="mb-3 mb-md-4 text-center">
                                                        <div class="count">{{isset($nextRemitDate) ? date("D, d M' y",strtotime($nextRemitDate)) : date("D, d M' y",strtotime("next Wednesday"))}}</div>
                                                        <p class="mb-0 text-muted">Next Remit Date</p>
                                                    </div>
                                                </div>
                                                <div class="col-md col-sm-6">
                                                    <div class="mb-3 mb-md-4 text-center">
                                                        <div class="count">{{$nextRemitCod ?? 0}}</div>
                                                        <p class="mb-0 text-muted">Next Remit Amount</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card db-card-bg mb-3 mb-md-4 rto_details cursor-pointer" style="height: 304px">
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
                                    </div>
                                </div>
                            </div>
