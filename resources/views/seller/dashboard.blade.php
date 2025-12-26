<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-bundle.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-map.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/geodata/latest/countries/india/india.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.15/proj4.js"></script>

    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">

    <style>
        .anychart-credits-text,.anychart-credits-logo,.highcharts-credits{
            display: none;
        }
        body{
            zoom: 80%;
        }
    </style>
    <title>Dashboard | {{$config->title}}</title>
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="nav-scroll scroll-bar active card mb-4 col-12">
            <div class="tablist mt-3" id="pills-tab" role="tablist">
                <div class="me-2" role="presentation">
                    <a class="nav-buttons active" data-tab="overview" href="javascript:" type="button">Overview</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="nav-buttons" type="button" data-tab="orders" href="javascript:">Orders</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="nav-buttons" href="javascript:" data-tab="rto" type="button">RTO</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="nav-buttons" type="button" href="javascript:" data-tab="ndr">NDR</a>
                </div>
                <!-- <div class="me-2" role="presentation">
                    <a class="nav-buttons" type="button" href="javascript:" data-tab="courier">Courier</a>
                </div> -->
            </div>
        </div>

        <div id="currentTab">

        </div>
    </div>
</div>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@include('seller.pages.scripts')
<script>
    let selectedTab = 'overview';
    $(document).ready(function(){
        loadPageData();
        $('.nav-buttons').click(function (){
            let that = $(this);
            selectedTab = that.data('tab');
            $('.nav-buttons').removeClass('active');
            that.addClass('active');
            loadPageData();
        });
    });

    function loadPageData(){
        switch(selectedTab){
            case 'overview':
                loadOverviewData()
                break;
            case 'orders':
                loadOrdersData()
                break;
            case 'rto':
                loadRTOData()
                break;
            case 'ndr':
                loadNDRData()
                break;
            case 'courier':
                loadCourierData()
                break;
        }
    }

    function loadOverviewData(){
        $.ajax({
            url : '{{route('seller.dashboardOverview')}}',
            success : function (response){
                $('#currentTab').html(response);
            }
        });
    }

    function loadOrdersData(){
        $.ajax({
            url : '{{route('seller.dashboardOrder')}}',
            success : function (response){
                $('#currentTab').html(response);
            }
        });
    }

    function loadRTOData(){
        $.ajax({
            url : '{{route('seller.dashboardRTO')}}',
            success : function (response){
                $('#currentTab').html(response);
            }
        });
    }

    function loadNDRData(){
        $.ajax({
            url : '{{route('seller.dashboardNdr')}}',
            success : function (response){
                $('#currentTab').html(response);
            }
        });
    }

    function loadCourierData(){
        $.ajax({
            url : '{{route('seller.dashboardCourier')}}',
            success : function (response){
                $('#currentTab').html(response);
            }
        });
    }
</script>
</body>

</html>
