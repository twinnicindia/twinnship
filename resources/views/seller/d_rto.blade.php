<div class="row mt-2">
    <div class="col-md-4 col-sm-6">
        <h5 class="text-dark mb-4">RTO Order Details</h5>
        <div class="card db-card-bg mb-3 mb-md-5 p-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="text-center">
                            <div class="count">{{$rto_initiated + $rto_undelivered + $rto_undelivered}}</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="text-center">
                            <div class="count">@if($total_order != '0'){{round((($rto_initiated + $rto_undelivered + $rto_undelivered) / $total_order) *100,2)}} @endif %</div>
                            <p class="mb-0 text-muted">RTO Percentage</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="card db-card-bg mb-3 mb-md-4 p-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <div class="text-center">
                            <div class="count">{{$rto_initiated}}</div>
                            <p class="mb-0 text-muted">RTO Initiated</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="text-center">
                            <div class="count">{{$rto_undelivered}}</div>
                            <p class="mb-0 text-muted">RTO In Transit</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="text-center">
                            <div class="count">{{$rto_delivered}}</div>
                            <p class="mb-0 text-muted">RTO Delivered</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">RTO Count</h6>
                </div>
                <div id="RTOCount" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-8 col-sm-8">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">RTO Status</h6>
                </div>
                <div id="RTOStatus" style="height: 320px;"></div>
            </div>
        </div>

    </div>
    <div class="col-md-4 col-sm-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">RTO Reasons</h6>
                </div>
                <div id="RTOResons" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top RTO - Pincodes</h6>
                </div>
                <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pincode</th>
                                <th>RTO Count</th>
                                <th>Percentage</th>
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
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top RTO - City</h6>
                </div>
                <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>RTO Count</th>
                                <th>Percentage</th>
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
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top RTO - Courier</h6>
                </div>
                <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>RTO Count</th>
                                <th>Percentage</th>
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
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top RTO - Customer</h6>
                </div>
                <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>RTO Count</th>
                                <th>Percentage</th>
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
