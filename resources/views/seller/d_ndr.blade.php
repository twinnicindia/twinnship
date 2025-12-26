<?php error_reporting(0); ?>
<div class="card db-card-bg mb-3 mb-md-4">
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
</div>

<div class="row mt-2">
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
</div>
<div class="row mt-2">
    <div class="col-md-6 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">NDR Reason Split</h6>
                </div>
                <div id="NDRReasonSplit" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">NDR Status</h6>
                </div>
                <div id="NDRStatusChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:190px;"></th>
                                <th>NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Count of NDR Raised Shipment"></i><br>Shipment</th>
                                <th>1st NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 1st NDR Attempt is made"></i><br>Attempt</th>
                                <th>1st NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 1st NDR Attempt"></i><br>Delivered</th>
                                <th>2nd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 2nd NDR Attempt is made"></i><br>Attempt</th>
                                <th>2nd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 2nd NDR Attempt"></i><br>Delivered</th>
                                <th>3rd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment on which 3rd NDR Attempt is made"></i><br>Attempt</th>
                                <th>3rd NDR <i class="far fa-info-circle" data-placement="top" data-toggle="tooltip" data-original-title="Shipment delivered on which 3rd NDR Attempt"></i><br>Delivered</th>
                                <th>Total Delivered</th>
                                <th>Total RTO</th>
                                <th>Lost/Damaged</th>
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
                                <td>Seller Positive Response</td>
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
                                <td>Buyer Postive Response</td>
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

<div class="row mt-2">
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">NDR to Delivery Attempt</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Seller Response</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Buyer Response</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Success by Zone</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:190px;"></th>
                                <th>Total</th>
                                <th>Zone A</th>
                                <th>Zone B</th>
                                <th>Zone C</th>
                                <th>Zone D</th>
                                <th>Zone E</th>
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
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Success by Courier</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:190px;"></th>
                                @foreach($allPartners as $p)
                                    <th>{{$PartnerName[$p] ?? ""}}</th>
                                @endforeach
                                <th>Other</th>
                                <th>Total</th>
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
