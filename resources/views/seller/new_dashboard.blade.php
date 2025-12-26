<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/seller/')}}/css/bootstrap.min.css" type="text/css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/seller/')}}/css/style.css" type="text/css">
    <!-- materialdesignicons css -->
    <link href="{{asset('public/assets/seller/')}}/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <title>Hello, world!</title>
</head>
<body>
<div class="container-fluid user-dashboard">
    <header class="header bg-white d-flex align-items-center px-lg-3 px-2">
        <div class="mr-auto navbar-brand text-left mb-0 d-none d-lg-block">
            <a href="#" class="d-block"><img src="{{asset('public/assets/seller/')}}/images/logo.png" height="30" alt=""></a>
        </div>
        <div class="mr-auto text-right d-lg-none d-flex align-items-center">
            <nav class="navbar navbar-light py-0">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>
            <div class="navbar-brand mx-auto w-100 text-center m-0">
                <a href="#" class="d-block"><img src="{{asset('public/assets/seller/')}}/images/logo.png" height="30" alt=""></a>
            </div>
            <div class="collapse user-mobile-navbar" id="navbarToggleExternalContent">
                <div class="text-left px-3 side-menu">
                    <ul class="mb-0">
                        <li>
                            <a href="#">
                                <i class="mdi mdi-monitor-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-page-layout-header-footer"></i> <span>Layouts</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-calendar-check-outline"></i> <span>Calendar</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-chat-processing-outline"></i> <span>Chats</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-email-edit-outline"></i> <span>Emails</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="mdi mdi-phone"></i> <span>Contacts</span>
                            </a>
                        </li>
                    </ul>
                    <div class="w-100 text-right py-3 d-flex align-items-center justify-content-between">
                        <a href="#" class="btn btn-outline-success header-btn" data-toggle="modal" data-target="#exampleModal"><i class="mdi mdi-power-plug-outline"></i> Recharge</a>
                        <div class="dropdown user-dropdown">
                            <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="user-photo"><img src="{{asset('public/assets/seller/')}}/images/user-photo.svg" alt=""></span>
                                <span class="user-name">Lorem I.</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#">Profile</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                <a class="dropdown-item" href="#">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ml-auto text-right d-none d-lg-block">
            <a href="#" class="btn btn-outline-success d-inline header-btn" data-toggle="modal" data-target="#exampleModal"><i class="mdi mdi-cash-plus"></i> Recharge</a>
            <div class="dropdown user-dropdown d-inline">
                <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="user-photo"><img src="{{asset('public/assets/seller/')}}/images/user-photo.svg" alt=""></span>
                    <span class="user-name">₹ 1024</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Profile</a>
                    <a class="dropdown-item" href="#">Settings</a>
                    <a class="dropdown-item" href="#">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <aside class="sidebar border-right  d-none d-lg-block">
        <div class="side-menu">
            <ul>
                <li>
                    <a href="#" class="active">
                        <i class="mdi mdi-monitor-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-page-layout-header-footer"></i> <span>Layouts</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="#"><i class="mdi mdi-page-layout-header-footer"></i> <span>Order</span></a></li>
                        <li><a href="#"><i class="mdi mdi-page-layout-header-footer"></i> <span>Order</span></a></li>
                        <li><a href="#"><i class="mdi mdi-page-layout-header-footer"></i> <span>Order</span></a></li>
                        <li><a href="#"><i class="mdi mdi-page-layout-header-footer"></i> <span>Order</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-calendar-check-outline"></i> <span>Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-chat-processing-outline"></i> <span>Chats</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-email-edit-outline"></i> <span>Emails</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="mdi mdi-phone"></i> <span>Contacts</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
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
                                        <h3 class="count text-success">1178</h3>
                                        <p class="mb-0">Sales</p>
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
                                        <h3 class="count text-warning">25</h3>
                                        <p class="mb-0">Stores</p>
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
                                        <h3 class="count text-danger">847</h3>
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
                                                <th>Name
                                                    <div class="sort">
                                                        <a href="#"><i class="mdi mdi-chevron-up"></i></a>
                                                        <a href="#"><i class="mdi mdi-chevron-down"></i></a>
                                                    </div>
                                                </th>
                                                <th>Product
                                                    <div class="sort">
                                                        <a href="#"><i class="mdi mdi-chevron-up"></i></a>
                                                        <a href="#"><i class="mdi mdi-chevron-down"></i></a>
                                                    </div>
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#searchByOrder" role="button" aria-expanded="false" aria-controls="searchByOrder"><i class="mdi mdi-filter-variant" data-toggle="tooltip" data-placement="top" title="Search by Order ID"></i></a>
                                                        <div class="collapse filter-collapse" id="searchByOrder">
                                                            <form class="mt-0">
                                                                <div class="form-group">
                                                                    <label for="searchByOrderId">Search by Order ID</label>
                                                                    <input type="text" class="form-control" id="searchByOrderId" placeholder="Enter it Here">
                                                                </div>
                                                                <button type="submit" class="btn btn-primary">Apply</button>
                                                            </form>
                                                        </div>
                                                        <a data-toggle="collapse" href="#FilterbyStatusModal" role="button" aria-expanded="false" aria-controls="FilterbyStatusModal"><i class="mdi mdi-finance" data-toggle="tooltip" data-placement="top" title="Filter by Status"></i></a>
                                                        <div class="collapse filter-collapse" id="FilterbyStatusModal">
                                                            <label>Filter by Status</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" id="lorem">
                                                                <label class="custom-control-label pt-1" for="lorem">Lorem</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" id="Ipsum">
                                                                <label class="custom-control-label pt-1" for="Ipsum">Ipsum</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" id="Dolor">
                                                                <label class="custom-control-label pt-1" for="Dolor">Dolor sit</label>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary mt-2 ml-0">Apply</button>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>quantity</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <tr>
                                                <td>
                                                    <div class="round-img">
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/4.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/2.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/3.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/4.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/4.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/2.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/3.jpg" alt=""></a>
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
                                                        <a href="#"><img src="{{asset('public/assets/seller/')}}/images/avatar/4.jpg" alt=""></a>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upgrade Your Shipping Limit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-check text-center">Choose the amount to recharge your account.</div>
                <form class="mt-3">
                    <div class="form-group">
                        <label for="inputGroupSelect01">Amount</label>
                        <div class="input-group mb-3">
                            <select class="custom-select" id="inputGroupSelect01">
                                <option value="">₹ 200 </option>
                                <option value="">₹ 500 </option>
                                <option value="">₹ 800 </option>
                                <option value="">₹ 1000 </option>
                                <option value="">₹ 1200 </option>
                                <option value="">₹ 1400 </option>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="promoCode">Have A Promo Code?</label>
                        <input type="text" id="promoCode" class="form-control" placeholder="Enter it Here">
                        <small><a href="#">Apply</a></small>
                    </div> -->
                    <div class="form-group">
                        <label for="promoCode">Have A Promo Code?</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promoCode" placeholder="Enter it Here">
                            <div class="input-group-append">
                                <a href="#" class="btn btn-secondary">Apply</a>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Recharge</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('public/assets/seller/')}}/js/jquery.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/popper.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/bootstrap.min.js"></script>
<!-- <script src="{{asset('public/assets/seller/')}}/js/bootstrap.bundle.min.js"></script> -->
<script src="{{asset('public/assets/seller/')}}/js/morris-chart/raphael-min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/morris-chart/morris.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/morris-chart/dashboard1-init.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/custom.js"></script>
</body>
</html>
