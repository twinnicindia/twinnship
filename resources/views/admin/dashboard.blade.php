<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> Dashboard | {{env('appTitle')}} </title>
    <!-- Font Awesome Icons -->
    @include('admin.pages.styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        @include('admin.pages.header')
        @include('admin.pages.sidebar')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="clearfix hidden-md-up"></div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Seller</span>
                                    <span class="info-box-number">{{$total_seller}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Orders</span>
                                    <span class="info-box-number">{{$total_order}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Created Order (Today)</span>
                                    <span class="info-box-number">{{$today_created_order}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Shipped Order (Today)</span>
                                    <span class="info-box-number">{{$today_shipped_order}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Invoice Value (Today)</span>
                                    <span class="info-box-number">₹ {{$today_invoice_value}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Shipped Invoice Value (Today)</span>
                                    <span class="info-box-number">₹ {{$today_shipped_invoice_value}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Freight Charge (Today)</span>
                                    <span class="info-box-number">₹ {{$today_freight_charges}}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card" style="height: 500px; positon: relative;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" style="height: 448px;">
                                            <div class="card-title w-100 d-flex align-items-center justify-content-between">
                                                <h5 class="title">Seller Orders</h5>
                                                <a href="{{ route('administrator.orderReport.export', ['report' => 'seller-order']) }}" class="btn btn-sm btn-primary">Export</a>
                                            </div>
                                            <div class="pt-5" id="seller-order" style="width: 100%; height: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card" style="height: 500px; positon: relative;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" style="height: 448px;">
                                            <div class="card-title w-100 d-flex align-items-center justify-content-between">
                                                <h5 class="title">All Orders</h5>
                                                <a href="{{ route('administrator.orderReport.export', ['report' => 'all-order']) }}" class="btn btn-sm btn-primary">Export</a>
                                            </div>
                                            <div class="pt-5" id="all-order" style="width: 100%; height: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>

        @include('admin.pages.footer')
    </div>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    @include('admin.pages.scripts')
    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="{{url('/')}}/assets/admin/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="{{url('/')}}/assets/admin/plugins/raphael/raphael.min.js"></script>
    <script src="{{url('/')}}/assets/admin/plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="{{url('/')}}/assets/admin/plugins/jquery-mapael/maps/usa_states.min.js"></script>
    <!-- ChartJS -->
    <!-- <script src="{{url('/')}}/assets/admin/plugins/chart.js/Chart.min.js"></script> -->

    <!-- Chart JS -->
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-map.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/geodata/latest/countries/united_states_of_america/united_states_of_america.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.15/proj4.js"></script>


    <!-- PAGE SCRIPTS -->
    <script src="{{url('/')}}/assets/admin/dist/js/pages/dashboard2.js"></script>
    <script>
        //Orders Report
        anychart.onDocumentReady(async function() {
            var dataSet = [];
            await $.ajax({
                url: "{{ route('administrator.orderReport') }}",
                method: "get",
                success: function(res) {
                    dataSet = res;
                }
            });

            // create a data set
            var data = anychart.data.set(dataSet);

            // map the data
            var seriesData_1 = data.mapAs({x: 0, value: 1, fill: 3, stroke: 5, label: 6});
            var seriesData_2 = data.mapAs({x: 0, value: 2, fill: 4, stroke: 5, label: 6});

            // create a chart
            chart = anychart.column();

            // create series and set the data
            var series1 = chart.column(seriesData_1);
            series1.name("Todays Order");
            var series2 = chart.column(seriesData_2);
            series2.name("Yesterdays Order");

            // set the chart title
            // chart.title("Order Chart");

            // set the titles of the axes
            var xAxis = chart.xAxis();
            xAxis.title("Sellers");
            var yAxis = chart.yAxis();
            yAxis.title("Orders");

            // Set width bound
            chart.width('100%');

            // Set height bound
            chart.height('95%');

            chart.container('seller-order');
            chart.draw();
        });

        anychart.onDocumentReady(async function() {
            var dataSet = [];
            await $.ajax({
                url: "{{ route('administrator.orderReport') }}?report=all-order",
                method: "get",
                success: function(res) {
                    dataSet = res;
                }
            });

            // create a data set
            var data = anychart.data.set(dataSet);

            // map the data
            var seriesData = data.mapAs({x: 0, value: 1, fill: 3, stroke: 5, label: 6});

            // create a chart
            chart = anychart.column();

            // create series and set the data
            var series = chart.column(seriesData);
            series.name("All Orders");

            // set tooltip text template
            var tooltip = series.tooltip();
            tooltip.format(function() {
                return this.getData("tooltip").split("\\n").join("\n");
            });

            // set the chart title
            // chart.title("Order Chart");

            // set the titles of the axes
            var xAxis = chart.xAxis();

            // Set width bound
            chart.width('100%');

            // Set height bound
            chart.height('95%');

            chart.container('all-order');
            chart.draw();
        });

    </script>
</body>

</html>
