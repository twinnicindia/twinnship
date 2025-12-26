<div class="row justify-content-center">
    <div class="col-xxl-12 mb-3">
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">

                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>Total RTO</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$rto_initiated + $rto_undelivered + $rto_undelivered}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>RTO Percentage</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">@if($total_order != '0'){{round((($rto_initiated + $rto_undelivered + $rto_undelivered) / $total_order) *100,2)}} @endif %</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>RTO Initiated</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$rto_initiated}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>RTO Undelivered</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$rto_undelivered}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="ndb_bx_1">
                                                <div class="ndb_bx">
                                                    <h6>RTO Delivered</h6>
                                                </div>
                                                <div class=" ndb_bx ndb_bx_2">
                                                    <p class="box">{{$rto_delivered}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-12 col-md-12">
                                <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                                    <div style="display: flex; justify-content: space-between;">
                                        <p><b>RTO Count</b></p>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div id="RTOCount" style="height: 320px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>RTO Status</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div id="RTOStatus" style="height: 320px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>RTO Reasons </b></p>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div id="RTOResons" style="height: 320px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>Top RTO - Pincodes</b></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive  scroll-bar active">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th scope="col">Pincode</th>
                                            <th scope="col">RTO Count</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top_pincodes as $p)
                                                <tr>
                                                    <td>{{$p->s_pincode}}</td>
                                                    <td>{{$p->total_order}}</td>
                                                    <td>{{$total_order > 0 ? (round(($p->total_order / $total_order) *100,2)) : 0}} %</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">No Data Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>Top RTO - City</b></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive  scroll-bar active">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th scope="col">City</th>
                                            <th scope="col">RTO Count</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top_cities as $c)
                                                <tr>
                                                    <td>{{$c->s_city}}</td>
                                                    <td>{{$c->total_order}}</td>
                                                    <td>{{$total_order > 0 ? (round(($c->total_order / $total_order) *100,2)) : 0}} %</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">No Data Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>Top RTO - Courier</b></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive  scroll-bar active">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">RTO Count</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top_courier as $c)
                                                <tr>
                                                    <td>{{$PartnerName[$c->courier_partner]}}</td>
                                                    <td>{{$c->total_order}}</td>
                                                    <td>{{$total_order > 0 ? (round(($c->total_order / $total_order) *100,2)) : 0}} %</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">No Data Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b>Top RTO - Customer</b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days
                                </p>
                            </div>
                            <div class="">
                                <div class="table-responsive  scroll-bar active">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Unit Sold</th>
                                            <th scope="col">Revenue</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top_customer as $c)
                                                <tr>
                                                    <td>{{$c->s_customer_name}}</td>
                                                    <td>{{$c->total_order}}</td>
                                                    <td>{{$total_order > 0 ? (round(($c->total_order / $total_order) *100,2)) : 0}} %</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">No Data Found</td>
                                                </tr>
                                            @endforelse
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

<script>
    //RTO Count Chart
    anychart.onDocumentReady(function() {
        // create data
        var data = [
            {
                x: "{{$five_week_date}}",
                value: {{$five_week}}
            },
            {
                x: "{{$four_week_date}}",
                value: {{$four_week}}
            },
            {
                x: "{{$three_week_date}}",
                value: {{$three_week}}
            },
            {
                x: "{{$two_week_date}}",
                value: {{$two_week}}
            },
            {
                x: "{{$this_week_date}}",
                value: {{$this_week}}
            }
        ];
        // create a chart
        chart = anychart.line();
        // create a spline series and set the data
        var series = chart.spline(data);
        // set the container id
        chart.container("RTOCount");

        // initiate drawing the chart
        chart.draw();
    });



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
        chart.container("RTOResons");
        chart.draw();
    });


    @php
        $index=[
            'pending' => 0,
            'rto' => 1,
            'delivered' => 2,
            'lost' => 3,
            'damaged' => 4
        ]
    @endphp
    var this_week=[0,0,0];
    var two_week=[0,0,0];
    var three_week=[0,0,0];
    var four_week=[0,0,0];
    var five_week=[0,0,0];
    @php($cnt=0)
        @foreach($this_week_status as $w)
        this_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($two_week_status as $w)
        two_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($three_week_status as $w)
        three_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($four_week_status as $w)
        four_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($five_week_status as $w)
        five_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach

    anychart.onDocumentReady(function () {
        // create data set on our data
        var dataSet = anychart.data.set([
            ["{{$five_week_date}}", five_week[0], five_week[1], five_week[2]],
            ["{{$four_week_date}}", four_week[0], four_week[1], four_week[2]],
            ["{{$three_week_date}}", three_week[0], three_week[1], three_week[2]],
            ["{{$two_week_date}}", two_week[0], two_week[1], two_week[2]],
            ["{{$this_week_date}}", this_week[0], this_week[1], this_week[2]]
        ]);

        var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });
        var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });
        var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });
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
        setupSeriesLabels(series, 'Initiated');
        series = chart.column(secondSeriesData);
        setupSeriesLabels(series, 'Delivered');
        series = chart.column(thirdSeriesData);
        setupSeriesLabels(series, 'In Transit');
        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
        chart.yAxis().labels().format('{%Value}{groupsSeparator: }');
        chart.yAxis().title('RTO Status');
        chart.interactivity().hoverMode('by-x');
        chart.tooltip().displayMode('union');
        chart.container('RTOStatus');
        chart.draw();
    });

</script>

