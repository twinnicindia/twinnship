<div class="row mt-2">
    <div class="col-md-12">
        <div class="card card-shadow">
            <div class="card-body bg-light">
                <div class="card-title mb-md-4">
                    <h5 class="title">Zone Wise Shipments</h5>
                </div>
                <div id="ZoneWiseShipments" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-2">
    <div class="col-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Shipment's Channel</h6>
                </div>
                <div class="table-responsive h-300" style="min-height: 327px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Channels</th>
                                <th>Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipment_channel as $c)
                            <tr>
                                <td class="text-capitalize">{{$c->channel}}</td>
                                <td>{{$c->total_order}}</td>
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
    <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h5 class="title">Weight Profile in Kgs</h5>
                </div>
                <div id="WeightProfile" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h5 class="title">Shipment Zone</h5>
                </div>
                <div id="ShipmentZone" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Shipment Overview by Courier</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Courier Name</th>
                                <th>Yet to Pick</th>
                                <th>Picked</th>
                                <th>In-Transit</th>
                                <th>Delivered</th>
                                <th>NDR Pending</th>
                                <th>NDR Delivered</th>
                                <th>RTO</th>
                                <th>Lost/Damaged</th>
                                <th>Total Shipment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allPartners as $p)
                                <tr>
                                    <td>{{$PartnerName[$p]?? ""}}</td>
                                    <td>{{$partner_unscheduled[$p] ?? 0}}</td>
                                    <td>{{$partner_scheduled[$p] ?? 0}}</td>
                                    <td>{{$partner_intransit[$p] ?? 0}}</td>
                                    <td>{{$partner_delivered[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_pending[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_delivered[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_rto[$p] ?? 0}}</td>
                                    <td>{{$partner_damaged[$p] ?? 0}}</td>
                                    <td>{{$partner_total[$p] ?? 0}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Other</td>
                                <td>{{$other_partner_unscheduled ?? 0}}</td>
                                <td>{{$other_partner_scheduled ?? 0}}</td>
                                <td>{{$other_partner_intransit ?? 0}}</td>
                                <td>{{$other_partner_delivered ?? 0}}</td>
                                <td>{{$other_partner_ndr_pending ?? 0}}</td>
                                <td>{{$other_partner_delivered ?? 0}}</td>
                                <td>{{$other_partner_ndr_rto ?? 0}}</td>
                                <td>{{$other_partner_damaged ?? 0}}</td>
                                <td>{{$other_partner_total ?? 0}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $partner1= Session()->get('MySeller')->courier_priority_1;
    $partner2= Session()->get('MySeller')->courier_priority_2;
    $partner3= Session()->get('MySeller')->courier_priority_3;
    $partner4= Session()->get('MySeller')->courier_priority_4;
@endphp
<script>
    @php
        $index=[
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4
        ]
    @endphp
    var partner1=[0,0,0,0,0];
    var partner2=[0,0,0,0,0];
    var partner3=[0,0,0,0,0];
    var partner4=[0,0,0,0,0];
    var other_partner=[0,0,0,0,0];
    @php($cnt=0)
    {{-- @if(count($courier_partner1_zone) > 0  && count($courier_partner2_zone) > 0 && count($courier_partner3_zone) > 0 && count($courier_partner4_zone) > 0)  --}}
     @if(!empty($partner1) && !empty($partner2) && !empty($partner3) && !empty($partner1))
        @foreach($courier_partner1_zone as $cp)
            partner1[{{$index[$cp->zone ?? 'A']}}]='{{$cp->total_order ?? 0}}';
        @endforeach
        @foreach($courier_partner2_zone as $cp)
            partner2[{{$index[$cp->zone] ?? 'A'}}]='{{$cp->total_order ?? 0}}';
        @endforeach
        @foreach($courier_partner3_zone as $cp)
            partner3[{{$index[$cp->zone] ?? 'A'}}]='{{$cp->total_order ?? 0}}';
        @endforeach
        @foreach($courier_partner4_zone as $cp)
            partner4[{{$index[$cp->zone] ?? 'A'}}]='{{$cp->total_order ?? 0}}';
        @endforeach
        @foreach($other_partner_zone as $cp)
            other_partner[{{$index[$cp->zone]  ?? 'A'}}]='{{$cp->total_order ?? 0}}';
        @endforeach
        //Zone Wise Shipments
                anychart.onDocumentReady(function () {
        // create data set on our data
        var dataSet = anychart.data.set([
            ['{{$partner1!=""? $PartnerName[$partner1] : ""}}', partner1[0], partner1[1], partner1[2], partner1[3],partner1[4]],
            ['{{$partner2!=""? $PartnerName[$partner2] : ""}}', partner2[0], partner2[1], partner2[2], partner2[3],partner2[4]],
            ['{{$partner3!=""? $PartnerName[$partner3] : ""}}', partner3[0], partner3[1], partner3[2], partner3[3],partner3[4]],
            ['{{$partner4!=""? $PartnerName[$partner4] : ""}}', partner4[0], partner4[1], partner4[2], partner4[3],partner4[4]],
            ['Other', other_partner[0], other_partner[1], other_partner[2], other_partner[3],other_partner[4]]
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
        setupSeriesLabels(series, 'Zone A');
        series = chart.column(secondSeriesData);
        setupSeriesLabels(series, 'Zone B');
        series = chart.column(thirdSeriesData);
        setupSeriesLabels(series, 'Zone C');
        series = chart.column(fourthSeriesData);
        setupSeriesLabels(series, 'Zone D');
        series = chart.column(fifthSeriesData);
        setupSeriesLabels(series, 'Zone E');
        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
        chart.yAxis().labels().format('{%Value}{groupsSeparator: }');
        chart.yAxis().title('Zone Details');
        chart.interactivity().hoverMode('by-x');
        chart.tooltip().displayMode('union');
        chart.container('ZoneWiseShipments');
        chart.draw();
        });

    @endif

    //ZoneDistributionChart
    anychart.onDocumentReady(function() {
        var data = [{
                    x: "Zone A",
                    value: {{$zone_a}}
                },
                {
                    x: "Zone B",
                    value: {{$zone_b}}
                },
                {
                    x: "Zone c",
                    value: {{$zone_c}}
                },
                {
                    x: "Zone D",
                    value: {{$zone_d}}
                },
                {
                    x: "Zone E",
                    value: {{$zone_e}}
                },
            ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("ShipmentZone");
        chart.draw();
    });
    //ZoneDistributionChart
    anychart.onDocumentReady(function() {

            var data = [{
                    x: "0.5 Kgs",
                    value: {{$half_kgs}}
                },
                {
                    x: "0.5 - 1 Kgs",
                    value: {{$one_kgs}}
                },
                {
                    x: "1 - 1.5 Kgs",
                    value: {{$one_half_kgs}}
                },
                {
                    x: "1.5 - 2 Kgs",
                    value: {{$two_kgs}}
                },
                {
                    x: "2 - 5 Kgs",
                    value: {{$five_kgs}}
                },
                {
                    x: "5 kgs+",
                    value: {{$five_kgs_plus}}
                },
            ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("WeightProfile");
        chart.draw();
    });

</script>
