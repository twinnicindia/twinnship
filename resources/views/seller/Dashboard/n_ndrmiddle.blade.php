<div class="col-md-6">
    <div class="card db-card-bg mb-3 mb-md-4">
        <div class="card-body">
            <h6 class="mb-4">NDR Response</h6>
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Seller Response</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Seller Positive Response</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Seller Positive Response Delivered</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Buyer Response</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Buyer Positive Response</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="text-center">
                        <div class="count">0</div>
                        <p class="mb-0 text-muted">Buyer Positive Response Delivered</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="card db-card-bg mb-3 mb-md-4" style="height:242px; overflow:auto">
        <div class="card-body">
            <h6 class="mb-4">NDR Funnel</h6>
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <p>1st NDR</p>
                    <div class="text-center">
                        <div class="count">{{$attempt1_total ?? 0}}</div>
                        <p class="mb-0 text-muted">Total Shipment</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt1_pending ?? 0}}</div>
                        <p class="mb-0 text-muted">Pending Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt1_delivered ?? 0}}</div>
                        <p class="mb-0 text-muted">Delivered Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt1_rto ?? 0}}</div>
                        <p class="mb-0 text-muted">RTO</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt1_lost ?? 0}}</div>
                        <p class="mb-0 text-muted">Lost/Damaged</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <p>2nd NDR</p>
                    <div class="text-center">
                        <div class="count">{{$attempt2_total ?? 0}}</div>
                        <p class="mb-0 text-muted">Total Shipment</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt2_pending ?? 0}}</div>
                        <p class="mb-0 text-muted">Pending Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt2_delivered ?? 0}}</div>
                        <p class="mb-0 text-muted">Delivered Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt2_rto ?? 0}}</div>
                        <p class="mb-0 text-muted">RTO</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt2_lost ?? 0}}</div>
                        <p class="mb-0 text-muted">Lost/Damaged</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <p>3rd NDR</p>
                    <div class="text-center">
                        <div class="count">{{$attempt3_total ?? 0}}</div>
                        <p class="mb-0 text-muted">Total Shipment</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt3_pending ?? 0}}</div>
                        <p class="mb-0 text-muted">Pending Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt3_delivered ?? 0}}</div>
                        <p class="mb-0 text-muted">Delivered Shipments</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt3_rto ?? 0}}</div>
                        <p class="mb-0 text-muted">RTO</p>
                    </div>
                    <br>
                    <div class="text-center">
                        <div class="count">{{$attempt3_lost ?? 0}}</div>
                        <p class="mb-0 text-muted">Lost/Damaged</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
