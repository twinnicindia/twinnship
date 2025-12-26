<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | {{$config->title}}</title>
    @include('seller.pages.styles')
</head>
<body>
<div class="container-fluid user-dashboard">

    @include('seller.pages.header')

    @include('seller.pages.sidebar')

    <div class="content-wrapper">
        <div class="content-inner">
            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3 title">Dashboard</h3>
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-4">
                            <div class="card p-3">
                                <div class="media align-items-center">
                                    <div class="media-left meida media-middle">
                                        <span><i class="mdi mdi-currency-usd mdi-48px text-primary"></i></span>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="count text-primary">568120</h3>
                                        <p class="mb-0">Total Revenue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-4">
                            <div class="card p-3">
                                <div class="media align-items-center">
                                    <div class="media-left meida media-middle">
                                        <span><i class="mdi mdi-cart-outline mdi-48px f-s-40 text-success"></i></span>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="count text-success">{{$total_order}}</h3>
                                        <p class="mb-0">Total Order</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-4">
                            <div class="card p-3">
                                <div class="media align-items-center">
                                    <div class="media-left meida media-middle">
                                        <span><i class="mdi mdi-archive-outline mdi-48px text-warning"></i></span>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="count text-warning">{{$total_warehouse}}</h3>
                                        <p class="mb-0">Total Warehouse</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-4">
                            <div class="card p-3">
                                <div class="media align-items-center">
                                    <div class="media-left meida media-middle">
                                        <span><i class="mdi mdi-emoticon-happy-outline mdi-48px text-danger"></i></span>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="count text-danger">84</h3>
                                        <p class="mb-0">Customer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- column -->
                        <div class="col-lg-8 mb-3 mb-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title title">Extra Area Chart</h3>
                                    <div id="extra-area-chart"></div>
                                </div>
                            </div>
                        </div>
                        <!-- column -->

                        <!-- column -->
                        <div class="col-lg-4 mb-3 mb-md-4">
                            <div class="card">
                                <div class="card-body browser">
                                    <h3 class="card-title title">Process Bar</h3>
                                    <p class="progress-title">iMacs <span class="pull-right">85%</span></p>
                                    <div class="progress ">
                                        <div role="progressbar" style="width: 85%; height:5px;" class="progress-bar bg-danger wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
                                    </div>

                                    <p class="progress-title">iBooks<span class="pull-right">90%</span></p>
                                    <div class="progress">
                                        <div role="progressbar" style="width: 90%; height:5px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
                                    </div>

                                    <p class="progress-title">iPhone<span class="pull-right">65%</span></p>
                                    <div class="progress">
                                        <div role="progressbar" style="width: 65%; height:5px;" class="progress-bar bg-success wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
                                    </div>

                                    <p class="progress-title">Samsung<span class="pull-right">65%</span></p>
                                    <div class="progress">
                                        <div role="progressbar" style="width: 65%; height:5px;" class="progress-bar bg-warning wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
                                    </div>

                                    <p class="progress-title">android<span class="pull-right">65%</span></p>
                                    <div class="progress mb-3">
                                        <div role="progressbar" style="width: 65%; height:5px;" class="progress-bar bg-success wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- column -->
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3 mb-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h3 class="title">Recent Orders </h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Product</th>
                                                <th>quantity</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <tr>
                                                <td>
                                                    <div class="round-img">
                                                        <a href="#"><img src="images/avatar/4.jpg" alt=""></a>
                                                    </div>
                                                </td>
                                                <td>John Abraham</td>
                                                <td><span>iBook</span></td>
                                                <td><span>456 pcs</span></td>
                                                <td><span class="badge badge-success">Done</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="round-img">
                                                        <a href="#"><img src="images/avatar/2.jpg" alt=""></a>
                                                    </div>
                                                </td>
                                                <td>John Abraham</td>
                                                <td><span>iPhone</span></td>
                                                <td><span>456 pcs</span></td>
                                                <td><span class="badge badge-success">Done</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="round-img">
                                                        <a href="#"><img src="images/avatar/3.jpg" alt=""></a>
                                                    </div>
                                                </td>
                                                <td>John Abraham</td>
                                                <td><span>iMac</span></td>
                                                <td><span>456 pcs</span></td>
                                                <td><span class="badge badge-warning">Pending</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="round-img">
                                                        <a href="#"><img src="images/avatar/4.jpg" alt=""></a>
                                                    </div>
                                                </td>
                                                <td>John Abraham</td>
                                                <td><span>iBook</span></td>
                                                <td><span>456 pcs</span></td>
                                                <td><span class="badge badge-success">Done</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 mb-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h3 class="title">Recent Orders </h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">First</th>
                                                <th scope="col">Last</th>
                                                <th scope="col">Handle</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>Jacob</td>
                                                <td>Thornton</td>
                                                <td>@fat</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td colspan="2">Larry the Bird</td>
                                                <td>@twitter</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 mb-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h3 class="title">Recent Orders </h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">First</th>
                                                <th scope="col">Last</th>
                                                <th scope="col">Handle</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>Jacob</td>
                                                <td>Thornton</td>
                                                <td>@fat</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td>Larry</td>
                                                <td>the Bird</td>
                                                <td>@twitter</td>
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

@include('seller.pages.scripts')

<script src="{{asset('public/assets/seller/')}}/js/morris-chart/raphael-min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/morris-chart/morris.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/morris-chart/dashboard1-init.js"></script>
</body>
</html>
