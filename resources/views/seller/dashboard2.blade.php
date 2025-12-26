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
                        </div>
                        <div id="overview_tab_data2" style="width: 100%; min-height: 70vh;">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-orders" role="tabpanel" aria-labelledby="nav-orders-tab">
                    <div class="mt-2" style="width: 100%; min-height: 70vh;" id="orders_tab_data">
                    </div>
                </div>

                <div class="tab-pane fade show" id="nav-shipment" role="tabpanel" aria-labelledby="nav-shipment-tab">
                    <div style="width: 100%; min-height: 70vh;" id="shipment_tab_data"></div>
                </div>

                <div class="tab-pane fade show" id="nav-ndr" role="tabpanel" aria-labelledby="nav-ndr-tab">
                    <div style="width: 100%; min-height: 70vh;" id="ndr_tab_data"></div>
                </div>

                <div class="tab-pane fade show" id="nav-rto" role="tabpanel" aria-labelledby="nav-rto-tab">
                    <div style="width: 100%; min-height: 70vh;" id="rto_tab_data"></div>
                </div>

                <div class="tab-pane fade show" id="nav-courier" role="tabpanel" aria-labelledby="nav-courier-tab">
                    <div style="width: 100%; min-height: 70vh;" id="courier_tab_data"></div>
                </div>

                <div class="tab-pane fade show" id="nav-delays" role="tabpanel" aria-labelledby="nav-delays-tab">
                    <div style="width: 100%; min-height: 70vh;" id="delays_tab_data"></div>
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
            $.ajax({
                url: '{{route('seller.dashboardCounter')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#overview_tab_data1').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#overview_tab_data1').html(response);
                    $('#overview_tab_data1').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#overview_tab_data1').LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $.ajax({
                url: '{{route('seller.dashboardOverview')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#overview_tab_data2').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchHomeData = true;
                    $('#overview_tab_data2').html(response);
                    $('#overview_tab_data2').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#overview_tab_data2').LoadingOverlay('hide');
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
                url: '{{route('seller.dashboardOrder')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#orders_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchOrdersData = true;
                    $('#orders_tab_data').html(response);
                    $('#orders_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#orders_tab_data').LoadingOverlay('hide');
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
                url: '{{route('seller.dashboardShipment')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#shipment_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchShipmentData= true;
                    $('#shipment_tab_data').html(response);
                    $('#shipment_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#shipment_tab_data').LoadingOverlay('hide');
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
                url: '{{route('seller.dashboardNdr')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#ndr_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchNDRData = true;
                    $('#ndr_tab_data').html(response);
                    $('#ndr_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#ndr_tab_data').LoadingOverlay('hide');
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
                url: '{{route('seller.dashboardRTO')}}',
                beforeSend: function() {
                    // Get overview page data
                    $('#rto_tab_data').LoadingOverlay('show', {
                        image       : "{{asset('assets/1.png')}}",
                        imageAutoResize : true,
                        imageResizeFactor : 1,
                        zIndex: 1,
                    });
                },
                success: function(response) {
                    fetchRTOData = true;
                    $('#rto_tab_data').html(response);
                    $('#rto_tab_data').LoadingOverlay('hide');
                },
                error: function(response) {
                    $('#rto_tab_data').LoadingOverlay('hide');
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
                url: '{{route('seller.dashboardCourier')}}',
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
                url: '{{route('seller.dashboardDelays')}}',
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
