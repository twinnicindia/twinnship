<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | {{$config->title}}</title>
@include('seller.pages.styles')

<!-- Chart CSS -->
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-circular-gauge.min.js"></script>
    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
    <script src="https://cdn.anychart.com/geodata/2.1.0/countries/india/india.js"></script>

    <style>
        .anychart-credits-text,.anychart-credits-logo,.highcharts-credits{
            display: none;
        }
        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{
            border-top : 2px solid #073D59 !important;
        }
        .cursor-pointer{
            cursor: pointer;
        }
        .user-dashboard .btn {
            min-width: 40px;
        }
        .filter a{
            font-size: 18px;
            color: #303030;
            line-height: 10px;
            position: relative;
        }
        .filter-collapse {
            position: absolute;
            z-index: 1;
            right: 0;
            width: 300px;
            transform: translateX(-50%);
            background: #ffffff;
            padding: 15px;
            border: 1px solid #dee2e6;
            font-size: 12px;
            line-height: 18px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <h3 class="h5 mb-3 title">Dashboard
                <div class="float-right h6">
                    <button class="btn btn-primary d-inline header-btn mx-o welcomebtn">Last 30 Days</button>
                    <a style="display: none;" data-toggle="collapse" id="dateFilterButton" href="#DateFilterModal" role="button" aria-expanded="false" aria-controls="DateFilterModal"><i class="fa fa-calendar-alt"></i> Select Date</a>
                    <div class="collapse filter-collapse ml-5" id="DateFilterModal">
                        <form action="{{route('seller.set_date_dashboard')}}" method="post" id="DateFilterForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" value="{{session('d_start_date')}}" id="start_name" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" value="{{session('d_end_date')}}" id="end_name" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                            <button type="reset" class="reset_value btn btn-primary btm-sm mt-2 ml-0 resetFilterDate">
                                Reset
                            </button>
                            <button type="button" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                            </button>
                        </form>
                    </div>
                </div>
            </h3>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-overview-tab" data-toggle="tab" href="#nav-overview" role="tab" aria-controls="nav-overview" aria-selected="true">Overview</a>
                    <a class="nav-item nav-link" id="nav-orders-tab" data-toggle="tab" href="#nav-orders" role="tab" aria-controls="nav-orders" aria-selected="false">Orders</a>
                    <a class="nav-item nav-link" id="nav-shipment-tab" data-toggle="tab" href="#nav-shipment" role="tab" aria-controls="nav-shipment" aria-selected="false">Shipment</a>
                    <a class="nav-item nav-link" id="nav-ndr-tab" data-toggle="tab" href="#nav-ndr" role="tab" aria-controls="nav-ndr" aria-selected="false">NDR</a>
                    <a class="nav-item nav-link" id="nav-rto-tab" data-toggle="tab" href="#nav-rto" role="tab" aria-controls="nav-rto" aria-selected="false">RTO</a>
                    <a class="nav-item nav-link" id="nav-courier-tab" data-toggle="tab" href="#nav-courier" role="tab" aria-controls="nav-courier" aria-selected="false">Courier</a>
                    <a class="nav-item nav-link" id="nav-delays-tab" data-toggle="tab" href="#nav-delays" role="tab" aria-controls="nav-delays" aria-selected="false">Delays</a>
                </div>

            </nav>
            <br>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                    <div style="width: 100%; min-height: 70vh;" id="overview_tab_data">
                        <div id="overview_tab_data1" style="width: 100%; min-height: 70vh;">
                            <div class="row" id="dashboardTop">

                            </div>
                            <div class="row">
                                <div class="col-md-3" id="dashboardOrderShipment">

                                </div>
                                <div class="col-md-9">
                                    <div class="card db-card-bg mb-3 mb-md-4 ndr_details cursor-pointer" style="height: 250px" id="dashboardNdrData">

                                    </div>
                                    <div class="card db-card-bg mb-3 mb-md-4" style="height: 304px" id="dashboardCodData">

                                    </div>
                                    <div class="card db-card-bg mb-3 mb-md-4 rto_details cursor-pointer" style="height: 304px" id="dashboardRtoData">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="overview_tab_data2" style="width: 100%; min-height: 70vh;">
                            <div class="row mt-2" >
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardCourierSplitData">

                                </div>
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardOverallData">

                                </div>
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardDeliveredData">

                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardStateData">

                                </div>
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardShipmentData">

                                </div>
                                <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardOverviewRevenue">

                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12" id="dashboardShipmentByCourierData">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-orders" role="tabpanel" aria-labelledby="nav-orders-tab">
                    <div class="mt-2" style="width: 100%; min-height: 70vh;" id="orders_tab_data">
                        <div class="row mt-2">
                            <div class="col-md-12" id="dashboardOrderTop">

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardPrepaidOrder">

                            </div>
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardbuyerOrder">

                            </div>
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardLocationOrder">

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6" id="dashboardCustomerOrder">

                            </div>
                            <div class="col-md-6" id="dashboardProductOrder">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-shipment" role="tabpanel" aria-labelledby="nav-shipment-tab">
                    <div style="width: 100%; min-height: 70vh;" id="shipment_tab_data">
                        <div class="row mt-2">
                            <div class="col-md-12" id="dashboardShipmentZoneWiseData">

                            </div>
                        </div>


                        <div class="row mt-2">
                            <div class="col-md-4" id="dashboardShipmentChannelData">

                            </div>
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardShipmentWeightData">

                            </div>
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-4" id="dashboardShipmentZoneData">

                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12" id="dashboardShipmentByCourierDataShip">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-ndr" role="tabpanel" aria-labelledby="nav-ndr-tab">
                    <div style="width: 100%; min-height: 70vh;" id="ndr_tab_data">
                        <div class="card db-card-bg mb-3 mb-md-4" id="dashboardNdrTopData">

                        </div>
                        <div class="row mt-2" id="dashboardMiddleTabData">

                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-sm-12" id="dashboardNdrSplitData">

                            </div>
                            <div class="col-md-6 col-sm-12" id="dashboardNdrStatusTabData">

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12" id="dashboardNdrAttemptTabData">

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
                            <div class="col-md-6" id="dashboardNdrSuccessbyZoneTabData">

                            </div>
                            <div class="col-md-6" id="dashboardNdrSuccessbyCourierTabData">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-rto" role="tabpanel" aria-labelledby="nav-rto-tab">
                    <div style="width: 100%; min-height: 70vh;" id="rto_tab_data">
                        <div class="row mt-2">
                            <div class="col-md-4 col-sm-6" id="dashboardRtoDetailTabData">

                            </div>
                            <div class="col-md-8 col-sm-12" id="dashboardRtoCountTabData">

                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-8 col-sm-8" id="dashboardRtoStatusTabData">


                            </div>
                            <div class="col-md-4 col-sm-4" id="dashboardRtoReasonTabData">

                            </div>
                        </div>

                        <div class="row mt-2" id="dashboardRtoPincodeTabData">

                        </div>

                        <div class="row mt-2" id="dashboardRtoCourierTabData">

                        </div>

                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-courier" role="tabpanel" aria-labelledby="nav-courier-tab">
                    <div style="width: 100%; min-height: 70vh;" id="courier_tab_data">

                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-delays" role="tabpanel" aria-labelledby="nav-delays-tab">
                    <div style="width: 100%; min-height: 70vh;" id="delays_tab_data">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->


@include('seller.pages.scripts')

<!-- Chart JS -->
<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-map.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
<script src="https://cdn.anychart.com/geodata/latest/countries/united_states_of_america/united_states_of_america.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.15/proj4.js"></script>

<!-- India Map -->
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
<script src="https://code.highcharts.com/mapdata/countries/in/in-all.js"></script>

<script>
    $("body").tooltip({
        selector: '[data-toggle=tooltip]',
        delay: {
            show: 1000
        }
    });

    $(document).ready(function() {
        var currentTab='overview',fetchHomeData = false,fetchOrdersData=false,fetchShipmentData = false,fetchNDRData = false,fetchRTOData = false,fetchCourierData = false,fetchDelaysData = false;
        overviewTabData();

        $('#nav-orders').on('click','#get_report',function(){
            const date1 = new Date($('#custom_start_date').val());
            const date2 = new Date($('#custom_end_date').val());
            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (date1 > date2) {
                $.notify(" Error... Start Date Must Be Less Than End Date!", {
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
                return false;
            }
            $('#orders_tab_data').LoadingOverlay('show', {
                image       : "{{asset('assets/1.png')}}",
                imageAutoResize : true,
                imageResizeFactor : 1,
                zIndex: 1,
            });
            $.ajax({
                url: "{{route('seller.getCustomDateOrder')}}",
                data: {
                    start_date : $('#custom_start_date').val(),
                    end_date : $('#custom_end_date').val()
                },
                success: function(response){
                    $('#customOrderDate').html(response);
                    $('#orders_tab_data').LoadingOverlay('hide');
                },
                error: function(response){
                    $('#orders_tab_data').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $('#' + $(this).data('modal')).collapse('hide');
        });

        $('#nav-orders').on('click','.custom_date_reset',function(){
            $('#orders_tab_data').LoadingOverlay('show', {
                image       : "{{asset('assets/1.png')}}",
                imageAutoResize : true,
                imageResizeFactor : 1,
                zIndex: 1,
            });
            $.ajax({
                url: "{{route('seller.getCustomDateOrder')}}",
                data: {
                    start_date : '{{date('Y-m-d',strtotime("-4 days"))}}',
                    end_date : '{{date('Y-m-d',strtotime("-1 days"))}}'
                },
                success: function(response){
                    $('#customOrderDate').html(response);
                    $('#orders_tab_data').LoadingOverlay('hide');
                },
                error: function(response){
                    $('#orders_tab_data').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $('#' + $(this).data('modal')).collapse('hide');
        });

        $('#nav-tabContent').on('click', '.overview', function() {
            $('#nav-overview-tab').click();
        });

        $('#nav-tabContent').on('click', '.order_details', function() {
            $('#nav-orders-tab').click();
        });

        $('#nav-tabContent').on('click', '.shipment_details', function() {
            $('#nav-shipment-tab').click();
        });

        $('#nav-tabContent').on('click', '.ndr_details', function() {
            $('#nav-ndr-tab').click();
        });

        $('#nav-tabContent').on('click', '.rto_details', function() {
            $('#nav-rto-tab').click();
        });

        //get data of Overview Tab
        $('#nav-tab').on('click', '#nav-overview-tab', function() {
            currentTab = 'overview';
            if(!fetchHomeData)
                 overviewTabData();
        });

        function overviewTabData() {

            //new dashboard
            $.ajax({
                url: '{{route('seller.dashboardTop')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardTop').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardTop').html(response);
                    $('#dashboardTop').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardTop').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardOrderShipment')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardOrderShipment').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardOrderShipment').html(response);
                    $('#dashboardOrderShipment').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardOrderShipment').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardNdrData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardNdrData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardNdrData').html(response);
                    $('#dashboardNdrData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardCodData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardCodData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardCodData').html(response);
                    $('#dashboardCodData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardCodData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardRtoData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardRtoData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardRtoData').html(response);
                    $('#dashboardRtoData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardCourierSplitData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardCourierSplitData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardCourierSplitData').html(response);
                    $('#dashboardCourierSplitData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardCourierSplitData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardOverallData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardOverallData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardOverallData').html(response);
                    $('#dashboardOverallData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardOverallData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardDeliveredData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardDeliveredData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardDeliveredData').html(response);
                    $('#dashboardDeliveredData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardDeliveredData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardStateData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardStateData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardStateData').html(response);
                    $('#dashboardStateData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardStateData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardShipmentData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardShipmentData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardShipmentData').html(response);
                    $('#dashboardShipmentData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardOverviewRevenue')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardOverviewRevenue').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardOverviewRevenue').html(response);
                    $('#dashboardOverviewRevenue').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardOverviewRevenue').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardShipmentByCourierData')}}',
                async : true,
                beforeSend: function() {
                    $('#dashboardShipmentByCourierData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#dashboardShipmentByCourierData').html(response);
                    $('#dashboardShipmentByCourierDataShip').html(response);
                    $('#dashboardShipmentByCourierData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentByCourierData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

        }

        //get data of Order Tab
        $('#nav-tab').on('click', '#nav-orders-tab', function() {
            currentTab='orders';
            if(!fetchOrdersData)
                ordersTabData();
        });

        function ordersTabData() {
            $.ajax({
                url: '{{route('seller.dashboardOrderTop')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardOrderTop').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardOrderTop').html(response);
                    $('#dashboardOrderTop').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardOrderTop').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardPrepaidOrder')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardPrepaidOrder').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardPrepaidOrder').html(response);
                    $('#dashboardPrepaidOrder').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardPrepaidOrder').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardbuyerOrder')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardbuyerOrder').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardbuyerOrder').html(response);
                    $('#dashboardbuyerOrder').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardbuyerOrder').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardLocationOrder')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardLocationOrder').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardLocationOrder').html(response);
                    $('#dashboardLocationOrder').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardLocationOrder').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardCustomerOrder')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardCustomerOrder').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardCustomerOrder').html(response);
                    $('#dashboardCustomerOrder').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardCustomerOrder').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardProductOrder')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardProductOrder').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#dashboardProductOrder').html(response);
                    $('#dashboardProductOrder').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardProductOrder').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

        }

        //get data of Shipement Tab
        $('#nav-tab').on('click', '#nav-shipment-tab', function() {
            currentTab='shipment';
            if(!fetchShipmentData)
                ShipementTabData();
        });

        function ShipementTabData() {
            $.ajax({
                url: '{{route('seller.dashboardShipmentZoneWiseData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardShipmentZoneWiseData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchShipmentData= true;
                    $('#dashboardShipmentZoneWiseData').html(response);
                    $('#dashboardShipmentZoneWiseData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentZoneWiseData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardShipmentChannelData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardShipmentChannelData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchShipmentData= true;
                    $('#dashboardShipmentChannelData').html(response);
                    $('#dashboardShipmentChannelData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentChannelData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardShipmentWeightData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardShipmentWeightData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchShipmentData= true;
                    $('#dashboardShipmentWeightData').html(response);
                    $('#dashboardShipmentWeightData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentWeightData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardShipmentZoneData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardShipmentZoneData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchShipmentData= true;
                    $('#dashboardShipmentZoneData').html(response);
                    $('#dashboardShipmentZoneData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardShipmentZoneData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });


        }

        //get data of NDR Tab
        $('#nav-tab').on('click', '#nav-ndr-tab', function() {
            currentTab='ndr';
            if(!fetchNDRData)
                NDRTabData();
        });

        function NDRTabData() {
            $.ajax({
                url: '{{route('seller.dashboardNdrTopData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrTopData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrTopData').html(response);
                    $('#dashboardNdrTopData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrTopData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardMiddleTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardMiddleTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardMiddleTabData').html(response);
                    $('#dashboardMiddleTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardMiddleTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardNdrSplitData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrSplitData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrSplitData').html(response);
                    $('#dashboardNdrSplitData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrSplitData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardNdrStatusTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrStatusTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrStatusTabData').html(response);
                    $('#dashboardNdrStatusTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrStatusTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardNdrAttemptTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrAttemptTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrAttemptTabData').html(response);
                    $('#dashboardNdrAttemptTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrAttemptTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardNdrSuccessbyZoneTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrSuccessbyZoneTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrSuccessbyZoneTabData').html(response);
                    $('#dashboardNdrSuccessbyZoneTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrSuccessbyZoneTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardNdrSuccessbyCourierTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardNdrSuccessbyCourierTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#dashboardNdrSuccessbyCourierTabData').html(response);
                    $('#dashboardNdrSuccessbyCourierTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardNdrSuccessbyCourierTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        }

        //get data of RTO Tab
        $('#nav-tab').on('click', '#nav-rto-tab', function() {
            currentTab='rto';
            if(!fetchRTOData)
                RTOTabData();
        });

        function RTOTabData() {
            $.ajax({
                url: '{{route('seller.dashboardRtoDetailTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoDetailTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoDetailTabData').html(response);
                    $('#dashboardRtoDetailTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoDetailTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardRtoCountTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoCountTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoCountTabData').html(response);
                    $('#dashboardRtoCountTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoCountTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardRtoStatusTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoStatusTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoStatusTabData').html(response);
                    $('#dashboardRtoStatusTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoStatusTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardRtoReasonTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoReasonTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoReasonTabData').html(response);
                    $('#dashboardRtoReasonTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoReasonTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
            $.ajax({
                url: '{{route('seller.dashboardRtoPincodeTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoPincodeTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoPincodeTabData').html(response);
                    $('#dashboardRtoPincodeTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoPincodeTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardRtoCourierTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#dashboardRtoCourierTabData').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#dashboardRtoCourierTabData').html(response);
                    $('#dashboardRtoCourierTabData').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#dashboardRtoCourierTabData').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });





        }

        //get data of Courier Tab
        $('#nav-tab').on('click', '#nav-courier-tab', function() {
            currentTab='courier';
            if(!fetchCourierData)
                CourierTabData();
        });

        function CourierTabData() {
            $.ajax({
                url: '{{route('seller.dashboardCourierTabData')}}',
                async : true,
                beforeSend: function() {
                    // Get overview page data
                    $('#courier_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchCourierData = true;
                    $('#courier_tab_data').html(response);
                    $('#courier_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#courier_tab_data').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        }
        //get data of Courier Tab
        $('#nav-tab').on('click', '#nav-delays-tab', function() {
            currentTab='delays';
            if(!fetchDelaysData)
                DelaysTabData();
        });

        function DelaysTabData() {
            $.ajax({
                url: '{{route('seller.dashboardDelayTabData')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#delays_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchDelaysData = true;
                    $('#delays_tab_data').html(response);
                    $('#delays_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#delays_tab_data').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        }


        //Date Filter
        $('.user-dashboard').on('click', '.applyFilterDate', function () {
            var that=$(this);
            $('#DateFilterModal').collapse('hide');
            showOverlay();
            $('#DateFilterForm').ajaxSubmit({
                success: function (response) {
                    if(currentTab ==='overview'){
                        location.reload();
                    }else if(currentTab==='orders'){
                        $('#nav-orders-tab').click();
                    }else{
                        $('#nav-shipment-tab').click();
                    }
                    hideOverlay();
                }
            });
        });
        $('.user-dashboard').on('click', '.resetFilterDate', function () {
            showOverlay();
            $.ajax({
                url: '{{route('seller.reset_date_dashboard')}}',
                success: function(response) {
                    $('#start_name').val('');
                    $('#end_name').val('');
                    if(currentTab ==='overview'){
                        location.reload();
                    }else if(currentTab==='orders'){
                        $('#nav-orders-tab').click();
                    }else{
                        $('#nav-shipment-tab').click();
                    }
                    hideOverlay();
                },
                error: function(response) {
                    hideOverlay();
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        });
    });
</script>
</body>

</html>
