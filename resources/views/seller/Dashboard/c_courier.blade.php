<div class="container">
    <div class="row mt-2">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <div class="client-logo-box">
                <a>
                    <img src="{{!empty(Session()->get('MySeller')->courier_priority_1) ? asset($PartnerImage[Session()->get('MySeller')->courier_priority_1]) : ''}}" class="img-fluid" alt="">
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="client-logo-box">
                <a>
                    <img src="{{!empty(Session()->get('MySeller')->courier_priority_2) ? asset($PartnerImage[Session()->get('MySeller')->courier_priority_2]) : ''}}" class="img-fluid" alt="">

                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="client-logo-box">
                <a>
                    <img src="{{!empty(Session()->get('MySeller')->courier_priority_3) ? asset($PartnerImage[Session()->get('MySeller')->courier_priority_3]) : ''}}" class="img-fluid" alt="">
                </a>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light">
                    <div class="card-title mb-0">
                        <h4 class="title text-center mb-0">Courier</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light">
                    <div class="card-title mb-0">
                        <h4 class="title text-center mb-0">{{!empty(Session()->get('MySeller')->courier_priority_1) ? $PartnerName[Session()->get('MySeller')->courier_priority_1] : 'Select Partner First'}}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light">
                    <div class="card-title mb-0">
                        <h4 class="title text-center mb-0">{{!empty(Session()->get('MySeller')->courier_priority_2) ? $PartnerName[Session()->get('MySeller')->courier_priority_2] : 'Select Partner First'}}</h4>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light">
                    <div class="card-title mb-0">
                        <h4 class="title text-center mb-0">{{!empty(Session()->get('MySeller')->courier_priority_3) ? $PartnerName[Session()->get('MySeller')->courier_priority_3] : 'Select Partner First'}}</h4>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">Mode</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">Surface</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">Surface</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">Surface</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">Shipment Count</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_shipment ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_shipment ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_shipment ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">COD Order</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_cod ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_cod ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_cod ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">Prepaid Order</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_prepaid ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_prepaid ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_prepaid ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">Delivered</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">1st Attempt Delivered</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_1st_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_1st_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_1st_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">NDR Delivered</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_ndr_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_ndr_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_ndr_delivered ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">NDR Raised</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_ndr_raised ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_ndr_raised ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_ndr_raised ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">RTO</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_rto ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_rto ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_rto ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-light p-3">
                    <h6 class="title mb-0 pl-2">Lost / Damaged</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner1_lost ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner2_lost ?? '-'}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-shadow">
                <div class="card-body bg-white p-3">
                    <h6 class="title mb-0 pl-2">{{$partner3_lost ?? '-'}}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
