<div class="row justify-content-center">
    <div class="col-xxl-12 mb-3">
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>NDR Details</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>NDR Raised</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$total_ndr}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>NDR Raised Percentage</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    @if($total_ndr != 0)
                                                        <p class="box">{{round(($total_ndr / $total_order) * 100,2)}} %</p>
                                                    @else
                                                        <p class="box">0 %</p>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>Action Required</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$action_required}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>Delivered</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$ndr_delivered}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>NDR Requested</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$action_requested}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>NDR Details</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="icon transition">
                                                    <p class="text ptext">{{$attempt1_total ?? 0}}</p>
                                                </div>
                                                <span class="ms-2">Total Shipments</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon transition">
                                                        <p class="text ptext">{{$attempt1_pending ?? 0}}</p>
                                                    </div>
                                                    <span class="ms-2">Pending Shipments</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon transition">
                                                        <p class="text ptext">{{$attempt1_delivered ?? 0}}</p>
                                                    </div>
                                                    <span class="ms-2">Delivered Shipments</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon transition">
                                                        <p class="text ptext">{{$attempt1_rto ?? 0}}</p>
                                                    </div>
                                                    <span class="ms-2">RTO</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon transition">
                                                        <p class="text ptext">{{$attempt1_lost ?? 0}}</p>
                                                    </div>
                                                    <span class="ms-2">Lost/Damaged</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon transition">
                                                        <p class="text ptext">0</p>
                                                    </div>
                                                    <span class="ms-2">Total Shipments</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

{{--                    <div class="col-lg-6 col-md-6 mb-4 col-sm-6">--}}
{{--                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">--}}
{{--                            <div style="display: flex; justify-content: space-between;">--}}
{{--                                <p><b>NDR Funnel</b></p>--}}
{{--                                <p style="display: inline-block; margin-left: auto;" class="">Last--}}
{{--                                    30 days--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="row justify-content-between">--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">--}}
{{--                                        <div class="d-flex flex-column align-items-center">--}}
{{--                                            <div class="d-flex flex-column align-items-center">--}}
{{--                                                <div class="d-flex align-items-center mb-2">--}}
{{--                                                    <div class="icon transition">--}}
{{--                                                        <p class="text ptext">0</p>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="ms-2">Total Shipments</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>NDR Reason Split</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="d-flex justify-content-center">
                                        <div id="NDRReasonSplit" style="height: 320px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>NDR Status</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="d-flex justify-content-center">
                                        <div id="NDRStatusChart" style="height: 320px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12 mb-4">
                        <div class="card bg-white border-0 rounded-10 mb-4">
                            <div class="card-body p-4">

                                <div class="default-table-area project-list">
                                    <div class="table-responsive  scroll-bar active">
                                        <table class="table align-middle ">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="text-dark"></th>
                                                <th scope="col" class="text-dark">NDR Shipments</th>
                                                <th scope="col" class="text-dark">1st NDR Attempt</th>
                                                <th scope="col" class="text-dark">1st NDR Delivered</th>
                                                <th scope="col" class="text-dark">2nd NDR Attempt</th>
                                                <th scope="col" class="text-dark">2nd NDR Delivered</th>
                                                <th scope="col" class="text-dark">3nd NDR Attempt</th>
                                                <th scope="col" class="text-dark">3nd NDR Delivered</th>
                                                <th scope="col" class="text-dark">Total Delivered</th>
                                                <th scope="col" class="text-dark">Total RTO</th>
                                                <th scope="col" class="text-dark">Lost/Damaged</th>
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
                                                <td> Seller Positive Response</td>
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
                                                <td>Buyer Positive Response </td>
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
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold fs-18 mb-0">NDR to Delivery
                                        Attempt</b></p>

                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{asset('assets/sellers')}}/images/notfound.png" alt=""
                                     style="height: 200px; width:auto">
                            </div>
                            <p class="text-center text-dark"> <b>Data not found for the
                                    selected filter.</b></p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold fs-18 mb-0">Seller Response</b>
                                </p>

                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{asset('assets/sellers')}}/images/notfound.png" alt=""
                                     style="height: 200px; width:auto">
                            </div>
                            <p class="text-center text-dark"> <b>No Shipment in last 30
                                    days.</b></p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold fs-18 mb-0">Buyer Response</b>
                                </p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{asset('assets/sellers')}}/images/notfound.png" alt="" style="height: 200px; width:auto">
                            </div>
                            <p class="text-center text-dark"> <b>No Shipment Delivered in last 30 days.</b></p>
                        </div>
                    </div>
                    <div class="col-xxl-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                                    <div class="default-table-area project-list">
                                        <div class="table-responsive  scroll-bar active">
                                            <table class="table align-middle ">
                                                <thead>
                                                <tr>
                                                    <th scope="col" class="text-dark">NDR Success By Zone</th>
                                                    <th scope="col" class="text-dark">Total</th>
                                                    <th scope="col" class="text-dark">Zone A</th>
                                                    <th scope="col" class="text-dark">Zone B</th>
                                                    <th scope="col" class="text-dark">Zone C</th>
                                                    <th scope="col" class="text-dark">Zone D</th>
                                                    <th scope="col" class="text-dark">Zone E</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>NDR Raised</td>
                                                    <td>{{$z_ndr_raised_A + $z_ndr_raised_B + $z_ndr_raised_C + $z_ndr_raised_D + $z_ndr_raised_E}}</td>
                                                    <td>{{$z_ndr_raised_A}}</td>
                                                    <td>{{$z_ndr_raised_B}}</td>
                                                    <td>{{$z_ndr_raised_C}}</td>
                                                    <td>{{$z_ndr_raised_D}}</td>
                                                    <td>{{$z_ndr_raised_E}}</td>
                                                </tr>
                                                <tr>
                                                    <td>NDR Delivered</td>
                                                    <td>{{$z_ndr_delivered_A + $z_ndr_delivered_B + $z_ndr_delivered_C + $z_ndr_delivered_D + $z_ndr_delivered_E}}</td>
                                                    <td>{{$z_ndr_delivered_A}}</td>
                                                    <td>{{$z_ndr_delivered_B}}</td>
                                                    <td>{{$z_ndr_delivered_C}}</td>
                                                    <td>{{$z_ndr_delivered_D}}</td>
                                                    <td>{{$z_ndr_delivered_E}}</td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                                    <div class="default-table-area project-list">
                                        <div class="table-responsive  scroll-bar active">
                                            <table class="table align-middle ">
                                                <thead>
                                                <tr>
                                                    <th scope="col" class="text-dark">Success by Courier</th>
                                                    @foreach($allPartners as $p)
                                                        <th scope="col" class="text-dark">{{$PartnerName[$p] ?? ""}}</th>
                                                    @endforeach
                                                    <th scope="col" class="text-dark">Other</th>
                                                    <th scope="col" class="text-dark">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>NDR Raised</td>
                                                    <?php $total=0; ?>
                                                    @foreach($allPartners as $p)
                                                        <td>{{$p_ndr_raised[$p] ?? 0}}</td>
                                                            <?php $total+=($p_ndr_raised[$p] ?? 0); ?>
                                                    @endforeach
                                                    <td>{{$p_ndr_raised['other'] ?? 0}}</td>
                                                    <?php $total+=($p_ndr_raised['other'] ?? 0); ?>
                                                    <td>{{$total}}</td>
                                                </tr>
                                                <tr>
                                                    <td>NDR Delivered</td>
                                                    <?php $total=0; ?>
                                                    @foreach($allPartners as $p)
                                                        <td>{{$p_ndr_delivered[$p] ?? 0}}</td>
                                                            <?php $total+=($p_ndr_delivered[$p] ?? 0); ?>
                                                    @endforeach
                                                    <td>{{$p_ndr_delivered['other'] ?? 0}}</td>
                                                    <?php $total+=($p_ndr_delivered['other'] ?? 0); ?>
                                                    <td>{{$total}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-grow-1"></div>
</div>

<script>
    //NDR Reason Split Chart
    anychart.onDocumentReady(function() {
        var data = [
                @foreach($reason_split as $r)
            {
                x: "{{$r->reason}}",
                value: {{$r->total_reason}}
            },
            @endforeach
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("NDRReasonSplit");
        chart.draw();
    });


    //
    @php
        $index=[
            'pending' => 0,
            'rto' => 1,
            'delivered' => 2,
            'reattempt' => 3,
            'lost' => 4
        ]
    @endphp
    var this_week=[0,0,0,0,0];
    var two_week=[0,0,0,0,0];
    var three_week=[0,0,0,0,0];
    var four_week=[0,0,0,0,0];
    var five_week=[0,0,0,0,0];
    @php($cnt=0)
        @foreach($this_week as $w)
        this_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($two_week as $w)
        two_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($three_week as $w)
        three_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($four_week as $w)
        four_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($five_week as $w)
        five_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
    anychart.onDocumentReady(function () {
        var dataSet = anychart.data.set([
            ["{{$five_week_date}}", five_week[0], five_week[1], five_week[2], five_week[3],five_week[4]],
            ["{{$four_week_date}}", four_week[0], four_week[1], four_week[2], four_week[3],four_week[4]],
            ["{{$three_week_date}}", three_week[0], three_week[1], three_week[2], three_week[3],three_week[4]],
            ["{{$two_week_date}}", two_week[0], two_week[1], two_week[2], two_week[3],two_week[4]],
            ["{{$this_week_date}}", this_week[0], this_week[1], this_week[2], this_week[3],this_week[4]]
        ]);
        var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });
        var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });
        var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });
        var fourthSeriesData = dataSet.mapAs({ x: 0, value: 4 });
        var fifthSeriesData = dataSet.mapAs({ x: 0, value: 5 });
        var chart = anychart.column();
        chart.animation(true);
        chart.yScale().stackMode('value');
        chart.title().padding([0, 0, 5, 0]);
        var setupSeriesLabels = function (series, name) {
            series.name(name).stroke('3 #fff 1');
            series.hovered().stroke('3 #fff 1');
        };
        var series;
        series = chart.column(firstSeriesData);
        setupSeriesLabels(series, 'Delivered');
        series = chart.column(secondSeriesData);
        setupSeriesLabels(series, 'RTO');
        series = chart.column(thirdSeriesData);
        setupSeriesLabels(series, 'Pending');
        series = chart.column(fourthSeriesData);
        setupSeriesLabels(series, 'Reattempt');
        series = chart.column(fifthSeriesData);
        setupSeriesLabels(series, 'lost');
        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
        chart.yAxis().labels().format('{%Value}{groupsSeparator: }');
        chart.yAxis().title('NDR Orders');
        chart.interactivity().hoverMode('by-x');
        chart.tooltip().displayMode('union');
        chart.container('NDRStatusChart');
        chart.draw();
    });
</script>

