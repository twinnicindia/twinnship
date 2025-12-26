<div class="row mt-2">
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="card-title mb-md-4">
                        <h5 class="title">Courier Split</h5>
                    </div>
                    <div id="CourierSplitChart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="card-title mb-md-4">
                        <h5 class="title">Overall Shipment Status</h5>
                    </div>
                    <div id="ShipmentStatusChart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="card-title mb-md-4">
                        <h5 class="title">Delivery Performance</h5>
                    </div>
                    <div id="DeliveryPerformanceChart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
            <div class="card card-shadow">
                <div class="card-body bg-white">
                    <div class="card-title mb-md-4">
                        <h5 class="title">State Wise Split</h5>
                    </div>
                    <!-- change this to indiaMap to display india map again -->
                    <div id="indiaMap" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
                <div class="card-body">
                    <div class="card-title mb-md-4">
                        <h5 class="title">Shipment - Zone Distribution</h5>
                    </div>
                    <div id="ZoneDistributionChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
            <div class="card card-shadow h-100">
                <div class="card-body bg-light">
                    <div class="card-title mb-md-4">
                        <h5 class="title">Revenue</h5>
                    </div>
                    <ul class="list-group list-group-flush db-list">
                        <li>
                            <span>Lifetime</span>
                            <span>₹{{$revenue_lifetime}}</span>
                        </li>
                        <li>
                            <span>This Week</span>
                            <span>₹{{$revenue_week}}</span>
                        </li>
                        <li>
                            <span>This Month</span>
                            <span>₹{{$revenue_month}}</span>
                        </li>
                        <li>
                            <span>This Quarter</span>
                            <span>₹{{$revenue_quarter}}</span>
                        </li>
                        <li>
                            <span>This Year</span>
                            <span>₹{{$revenue_year}}</span>
                        </li>
                        <!-- <li>
                <span>Zone F</span>
                <span>₹200</span>
            </li> -->
                    </ul>
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
                                <td>{{$other_partner_ndr_delivered ?? 0}}</td>
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

    <script>
        //Courier Split Chart
        anychart.onDocumentReady(function() {
            // create data
            var data = [
                @foreach($courier_split as $c)
                {
                    x: "{{$c->courier_partner!=''?$PartnerName[$c->courier_partner] :'-'}}",
                    value: {{$c->total_order}}
                },
                @endforeach
            ];
            // create a pie chart and set the data
            var chart = anychart.pie(data);
            /* set the inner radius
            (to turn the pie chart into a doughnut chart)*/
            chart.innerRadius("70%");
            // set the chart title
            // chart.title("Doughnut Chart: Basic Sample");
            // set the container id
            chart.container("CourierSplitChart");
            // initiate drawing the chart
            chart.draw();
        });

        //ShipmentStatusChart
        anychart.onDocumentReady(function() {
            var data = [
                {
                    x: "Delivered",
                    value: {{$delivered}}
                },
                {
                    x: "Undelivered",
                    value: {{$undelivered}}
                },
                {
                    x: "Intransit",
                    value: {{$intransit}}
                },
                {
                    x: "RTO",
                    value: {{$rto}}
                },
                {
                    x: "Lost/Damaged",
                    value: {{$damaged}}
                }
            ];
            var chart = anychart.pie(data);
            chart.innerRadius("70%");
            chart.container("ShipmentStatusChart");
            chart.draw();
        });


        //DeliveryPerformanceChart
        anychart.onDocumentReady(function() {
            var data = [{
                    x: "On Time Deliveries",
                    value: {{$ontime_delivery}}
                },
                {
                    x: "Late Deliveries",
                    value: {{$late_delivery}}
                }
            ];
            var chart = anychart.pie(data);
            chart.innerRadius("70%");
            chart.container("DeliveryPerformanceChart");
            chart.draw();
        });

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
            chart.container("ZoneDistributionChart");
            chart.draw();
        });


        //most Popular Location Chart
        anychart.onDocumentReady(function() {
            var data = [{
                    x: "Delhi",
                    value: 23
                },
                {
                    x: "Maharastra",
                    value: 12
                },
                {
                    x: "Gujrat",
                    value: 6
                }
            ];
            var chart = anychart.pie(data);
            chart.innerRadius("70%");
            chart.container("PopularLocationChart");
            chart.draw();
        });

        anychart.onDocumentReady(function() {
            // create map
            var map = anychart.map();

            // create data set
            var dataSet = anychart.data.set(
                <?= json_encode($mapData) ?>
            );

            // create choropleth series
            series = map.choropleth(dataSet);
            series.geoIdField('id');
            series.colorScale(anychart.scales.linearColor('#deebf7', '#3182bd'));
            series.hovered().fill('#073D59');
            map.geoData(anychart.maps['india']);
            map.interactivity().zoomOnMouseWheel(true);
            // Disables zoom on double click
            map.interactivity().keyboardZoomAndMove(true);
            // Disables zoom on double click
            map.interactivity().zoomOnDoubleClick(true);
            map.container('indiaMap');

            var zoomController = anychart.ui.zoom();
            zoomController.target(map);
            map.listen('pointClick', function(e) {
                map.zoomToFeature(e.point.get('id'));
            })
            zoomController.render();
            map.draw();
            });

    </script>
