<div class="row justify-content-center">
    <div class="col-xxl-12 mb-3">
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box  card border-0 rounded-10 mb-4 p-3"
                             style="background-color: #cacafa;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-0">
                                    <div class="icon transition">
                                        <i class="flaticon-donut-chart"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <span class="mt-3 mb-2">Today's Order</span>
                                    <h4 class="body-font fw-bold fs-3 mb-1">{{$today_order ?? 0}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6 mb-4">
                        <div class="stats-box-new  card border-0 rounded-10 mb-4 p-3"
                             style="background-color: #cacafa;">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold">Shipments Details</b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.all_order', ['type' => 'all'])}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$total_all_orders ?? 0}}</h4>
                                                <span class="mt-3 mb-2">Total Shipments</span>
                                            </div>
                                        </a>
                                    </div>


                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.all_order', ['type' => 'manifest'])}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$pending_order ?? 0}}</h4>
                                                <span class="mt-3 mb-2">Pickup Pending</span>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.all_order', ['type' => 'live_orders'])}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$intransit_order ?? 0}}</h4>
                                                <span class="mt-3 mb-2">In-Transit</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.all_order', ['type' => 'delivered'])}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$delivered_order ?? 0}}</h4>
                                                <span class="mt-3 mb-2">Delivered</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.ndr_orders')}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$ndr_pending ?? 0}}</h4>
                                                <span class="mt-3 mb-2">NDR Pending</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6 col-4">
                                        <a target="_blank" href="{{route('seller.all_order', ['type' => 'returns'])}}">
                                            <div class="d-flex flex-column align-items-center">
                                                <h4 class="body-font fw-bold fs-3 mb-1">{{$rto_order ?? 0}}</h4>
                                                <span class="mt-3 mb-2">RTO</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box  card border-0 rounded-10 mb-4 p-3"
                             style="background-color: #c2e7c7;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-0">
                                    <div class="icon transition">
                                        <i class="flaticon-donut-chart"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <span class="mt-3 mb-2">Today's Revenue</span>
                                    <h4 class="body-font fw-bold fs-3 mb-1">₹ {{round($today_revenue,2)}}</h4>
                                    <span>Yesterday ₹ {{round($yesterday_revenue,2)}}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6 mb-4">
                        <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold">NDR Details</b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{$total_ndr ?? 0}}</h4>
                                            <span class="mt-3 mb-2">Total NDR</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{$action_required ?? 0}}</h4>
                                            <span class="mt-3 mb-2">Your Reattempt Request</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{$action_requested ?? 0}}</h4>
                                            <span class="mt-3 mb-2">Buyer Reattempt Request</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{$ndr_delivered ?? 0}}</h4>
                                            <span class="mt-3 mb-2">NDR Delivered</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box card border-0 rounded-10 mb-4 p-3"
                             style="background-color: #cacafa;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-0">
                                    <div class="icon transition">
                                        <i class="flaticon-donut-chart"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <span class="mt-3 mb-2">Average Shipping</span>
                                    <h4 class="body-font fw-bold fs-3 mb-1">₹ {{round($total_revanue,2)}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6 mb-4">
                        <div class="stats-box-new bg-white  card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">COD Status</b></p>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-lg-2 col-md-3 col-sm-6 col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">₹{{$cod_total ?? 0}}</h4>
                                            <span class="mt-3 mb-2">Total COD</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">₹ {{($cod_total - $remitted_cod) ?? 0}}</h4>
                                            <span class="mt-3 mb-2">COD Pending</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">₹ {{$remitted_cod ?? 0}}</h4>
                                            <span class="mt-3 mb-2">COD Remitted</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{isset($nextRemitDate) ? date("D, d M' y",strtotime($nextRemitDate)) : date("D, d M' y",strtotime("next Wednesday"))}}</h4>
                                            <span class="mt-3 mb-2">Next Remit Date</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <h4 class="body-font fw-bold fs-3 mb-1">{{$nextRemitCod ?? 0}}</h4>
                                            <span class="mt-3 mb-2">Next Remit Amount</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">Couriers Split</b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days</p>
                            </div>
                            <div id="CourierSplitChart" style="height: 320px;"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">Overall Shipment Status</b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <div id="ShipmentStatusChart" style="height: 320px;"></div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">Delivery Performance </b></p>
                                <p style="display: inline-block; margin-left: auto;" class="">Last
                                    30 days</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <div id="DeliveryPerformanceChart" style="height: 320px;"></div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p style="display: inline-block; margin-left: auto;" class="">State Wise Split</p>
                            </div>
                            <!-- <div class="d-flex justify-content-center"> -->
                            <div id="indiaMap" style="height: 350px;"></div>
                            <!-- </div> -->
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">Shipments - Zone
                                        Distribution</b></p>
                            </div>
                            <div class="row">
                                <ul class="zone_wise pt-0">
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="sphere sphere-grey"></span>
                                            <span class="pl-lg">Zone A</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">{{$zone_a}}
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="sphere sphere-green"></span>
                                            <span class="pl-lg">Zone B</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">{{$zone_b}}
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="sphere sphere-red"></span>
                                            <span class="pl-lg">Zone C</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">{{$zone_c}}
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="sphere"></span>
                                            <span class="pl-lg">Zone D</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">{{$zone_d}}
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="sphere sphere-yellow"></span>
                                            <span class="pl-lg">Zone E</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">{{$zone_e}}
                                                        </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
                            <div style="display: flex; justify-content: space-between;">
                                <p><b class="text-dark fw-semibold ">Revenue</b></p>
                                <p style="display: inline-block; margin-left: auto;">Last 30
                                    days</p>
                            </div>
                            <div class="row">
                                <ul class="zone_wise pt-0">
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="pl-lg">Last 90 Days</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">₹0
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="pl-lg">This Week</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">₹0
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="pl-lg">This Month</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">₹0
                                                        </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="col-sm-10 d-flex">
                                            <span class="pl-lg">This Quarter</span>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                                        <span class="ng-binding">₹0
                                                        </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                    <div class="col-xxl-12 mb-3 mb-4">
                        <div class="card bg-white border-0 rounded-10 mb-4">
                            <div class="card-body p-4">
                                <div
                                    class="d-flex justify-content-between align-items-center border-bottom pb-20 mb-20">
                                    <h4 class="fw-semibold fs-18 mb-0">Shipment Overview by Courier
                                    </h4>
                                    <div class="dropdown action-opt">
                                        <button class="btn bg-transparent p-0" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <!-- <i data-feather="chevron-down"></i> -->
                                            <span>Last 30 Days</span>
                                        </button>

                                    </div>
                                </div>
                                <div class="default-table-area project-list">
                                    <div class="table-responsive  scroll-bar active">
                                        <table class="table align-middle">
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
        var data = [
            {
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
        var data = [
            {
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
