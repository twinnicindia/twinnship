<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All OMS Orders | {{$config->title}} </title>
    @include('seller.pages.styles')
    <link href="{{asset('public/assets/seller/')}}/css/progress.css" rel="stylesheet">
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }
        .font-medium{
            color: #073D59 !important;
        }
        .badge-pill:hover {
            font-size: 12px;
        }
        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{
            border-top : 2px solid #073D59 !important;
        }
    </style>
</head>

<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="data_div">
            <div class="card">
                <div class="card-body" style="min-height:500px;">
                    <h3 class="h4 mb-4">All OMS Orders
                        <div class="float-right">
                            <form action="{{route('seller.export_csv_my_oms_order')}}" method="post" id="ExportOrderForm" class="d-inline">
                                @csrf
                                <input type="hidden" name="export_order_id" id="export_order_id">
                                <button type="button" class="btn btn-primary btn-sm mx-0 export_order_btn" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                            </form>
                            <span data-toggle="modal" data-target="#bulkupload"><button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Import CSV"><i class="fa fa-download"></i></button></span>
                            <button type="button" class="btn btn-danger btn-sm mx-0" id="removeAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Delete Order"><i class="fa fa-trash"></i></button>

                            <form action="{{route('seller.multipleLableDownload')}}" method="post" id="MultilabelForm">
                                @csrf
                                <input type="hidden" name="multilable_id" id="multilable_id">
                            </form>
                        </div>
                    </h3>
                    <div style="display: block;">
                        <div class="w-100 float-left" style="max-width: 60%; display: inline-block;">
                            <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="AllOrderSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="all_order">
                                <div class="row">
                                    <div class="col-6 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs/Contact Numbers)">
                                        </div>
                                    </div>
                                    <div class="col-4 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="AllOrderSearchingForm" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="filter_order" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="AllOrderSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <span class="float-right" style="display: inline-block;">
                                <p class="mb-0 h6 f-14">Showing <span class="order_display_limit"></span> of <span id="order_count"></span></p>
                                <p class="mb-0 h6 f-14">Selected <span class="total_order_selected">0</span> out of <span class="order_display_limit"></span></p>
                            </span>
                        <input type="hidden" class="limit_order" value="20">
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-all_orders" role="tabpanel" aria-labelledby="nav-all_orders-tab">
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-center w-100 mb-1">
                                                Order Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_DateFilterModal" role="button" aria-expanded="false" aria-controls="A_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="A_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" placeholder="Min Amount" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" name="end_date" placeholder="Max Amount" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}" required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button data-id="filter_order" data-key="start_date,end_date" data-modal="A_DateFilterModal" type="reset" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="A_DateFilterModal" data-form="A_DateFilterForm" data-id="filter_order" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-center w-100 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="A_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel</label>
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="A_custom">
                                                                <label class="custom-control-label pt-1" for="A_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="A_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="A_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="filter_order" data-key="channel" data-modal="A_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_ChannelFilterModal" data-form="A_channelForm" data-id="filter_order" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="14%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                <span>Order Id</span>
                                                <div class="filter justify-content-right" style="display:inline;">
                                                    <a data-toggle="collapse" href="#A_NumberFilterModal" role="button" aria-expanded="false" aria-controls="A_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_number">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="form-group">
                                                                <label for="A_searchByOrderId">Search by Order Id</label>
                                                                <input type="text" class="form-control" name="value" id="A_searchByOrderId" placeholder="Enter Order Id Here">
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="order_number" data-modal="A_NumberFilterModal"" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_NumberFilterModal" data-form="A_orderIdForm" data-id="filter_order" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                <span>Status</span>
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_StatusFilterModal" role="button" aria-expanded="false" aria-controls="A_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_StatusFilterModal" style="z-index:1px;">
                                                        <label>Filter by Status</label>
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_StatusForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="order_status">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="pending" id="A_pending">
                                                                    <label class="custom-control-label pt-1" for="A_pending">Pending</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="shipped" id="A_shipped">
                                                                    <label class="custom-control-label pt-1" for="A_shipped">Shipped</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="pickup_requested" id="A_pickup_requested">
                                                                    <label class="custom-control-label pt-1" for="A_pickup_requested">Pickup Requested</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="manifested" id="A_manifested">
                                                                    <label class="custom-control-label pt-1" for="A_manifested">Manifested</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="picked_scheduled" id="A_picked_scheduled">
                                                                    <label class="custom-control-label pt-1" for="A_picked_scheduled">Picked Scheduled</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="picked_up" id="A_picked_up">
                                                                    <label class="custom-control-label pt-1" for="A_picked_up">Picked Up</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="cancelled" id="A_cancelled">
                                                                    <label class="custom-control-label pt-1" for="A_cancelled">Cancelled</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="in_transit" id="A_in_transit">
                                                                    <label class="custom-control-label pt-1" for="A_in_transit">In Transit</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="out_for_delivery" id="A_out_for_delivery">
                                                                    <label class="custom-control-label pt-1" for="A_out_for_delivery">Out for Delivery</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="rto_initiated" id="A_rto_initiated">
                                                                    <label class="custom-control-label pt-1" for="A_rto_initiated">RTO Initiated</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="rto_delivered" id="A_rto_delivered">
                                                                    <label class="custom-control-label pt-1" for="A_rto_delivered">RTO Delivered</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="delivered" id="A_delivered">
                                                                    <label class="custom-control-label pt-1" for="A_delivered">Delivered</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="ndr" id="A_ndr">
                                                                    <label class="custom-control-label pt-1" for="A_ndr">NDR</label>
                                                                </div>
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="order_status" data-modal="A_StatusFilterModal"" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_StatusFilterModal" data-form="A_StatusForm" data-id="filter_order" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="15%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_AmountFilterModal" role="button" aria-expanded="false" aria-controls="A_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">

                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="max_value" placeholder="Max Amount">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="filter_order" data-key="min_value,max_value" data-modal="A_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="A_AmountFilterModal" data-form="A_OrderAmountForm" data-id="filter_order" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Payment
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="A_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbyPaymentModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_PaymentTypeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="payment_type">
                                                            <input type="hidden" name="filter_status" value="all_order">

                                                            <label>Filter by Payment Type</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="cod" id="A_cod">
                                                                <label class="custom-control-label pt-1" for="A_cod">Cash on
                                                                    Delivery</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="prepaid" id="A_prepaid">
                                                                <label class="custom-control-label pt-1" for="A_prepaid">Prepaid</label>
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="payment_type" data-modal="A_FilterbyPaymentModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="A_FilterbyPaymentModal" data-form="A_PaymentTypeForm" data-id="filter_order" class="applyFilterPayment btn btn-primary mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Product
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="A_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbyProductModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_ProductFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="product">
                                                            <input type="hidden" name="filter_status" value="all_order">

                                                            <div class="form-group">
                                                                <label for="A_searchByProduct">Product
                                                                    Name</label>
                                                                <input type="text" class="form-control" name="value" id="A_searchByProduct" placeholder="Product Name">
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="product" data-modal="A_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_FilterbyProductModal" data-form="A_ProductFormFilter" data-id="filter_order" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="A_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class=filterForm mt-0" method="post" id="A_SKUFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="sku">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="A_searchByProductSku">Search SKU</label>
                                                                        <input type="text" class="form-control" name="value" id="A_searchByProductSku" placeholder="Product SKU">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="A_searchBySingleProduct" name="single_sku" value="y">
                                                                        <label for="A_searchBySingleProduct">Single SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="A_searchByMultiProduct" name="multiple_sku" value="y">
                                                                        <label for="A_searchByMultiProduct">Multi SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="A_searchByMatchExactProduct" name="match_exact_sku" value="y">
                                                                        <label for="A_searchByMatchExactProduct">Match Exact</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="filter_order" data-key="single_sku,sku,multiple_sku,match_exact_sku" data-modal="A_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="A_FilterbySKUModal" data-form="A_SKUFormFilter" data-id="filter_order" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Qty
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbyQtyModal" role="button" aria-expanded="false" aria-controls="A_FilterbyQtyModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbyQtyModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class=filterForm mt-0" method="post" id="A_QtyFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="A_searchByMinQty">Min Quantity</label>
                                                                        <input type="text" class="form-control" name="min_quantity" id="A_searchByMinQty" placeholder="Min Qty">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="A_searchByMaxQty">Max Quantity</label>
                                                                        <input type="text" class="form-control" name="max_quantity" id="A_searchByMaxQty" placeholder="Max Qty">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="filter_order" data-key="min_quantity,max_quantity" data-modal="A_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="A_FilterbyQtyModal" data-form="A_QtyFormFilter" data-id="filter_order" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            Customer Details
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Pickup <br>Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="A_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse" id="A_FilterbyPAddress" style="z-index:1; position: absolute;left: 50%;transform: translateX(-50%);background: #ffffff;padding: 15px;border: 1px solid #dee2e6;font-size: 12px;line-height: 18px;border-radius: 5px;">
                                                        <label>Filter By Warehouse</label>
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->warehouse_name}}" id="A_pending{{$w->warehouse_name}}">
                                                                    <label class="custom-control-label pt-1" for="A_pending{{$w->warehouse_name}}">{{$w->warehouse_name}}</label>
                                                                </div>
                                                            @endforeach

{{--                                                            <div class="form-group">--}}
{{--                                                                <label for="A_searchByPAddress">Search Pickup Address</label>--}}
{{--                                                                <input type="text" class="form-control" name="value" id=A_searchByPAddress" placeholder="Pickup Address">--}}
{{--                                                            </div>--}}
                                                            <button type="reset" data-id="filter_order" data-key="pickup_address" data-modal="A_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_FilterbyPAddress" data-form="A_PickupAddressForm" data-id="filter_order" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Delivery <br>Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="A_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbyDAddress" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_DeliveryAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="delivery_address">
                                                            <input type="hidden" name="filter_status" value="all_order">

                                                            <div class="form-group">
                                                                <label for="A_searchByDAddress">Search Delivery
                                                                    Address</label>
                                                                <input type="text" class="form-control" name="value" id="A_searchByDAddress" placeholder="Delivery Address">
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="delivery_address" data-modal="A_FilterbyDAddress"" class="reset_value btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_FilterbyDAddress" data-form="A_DeliveryAddressForm" data-id="filter_order" class="applyFilterDeliveryAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-center w-100 mb-1">
                                                Dimension(CM)<br>Weight(Kg.)
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_WeightFilterModal" role="button" aria-expanded="false" aria-controls="A_WeightFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_WeightFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm" method="post" id="A_OrderWeightForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Weight (In Kgs)</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="min_weight" placeholder="Min Weight">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="max_weight" placeholder="Max Weight">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="min_weight,max_weight" data-modal="A_WeightFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="A_WeightFilterModal" data-form="A_OrderWeightForm" data-id="filter_order" class="applyFilterWeight btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Courier Partner
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_CourierFilterModal" role="button" aria-expanded="false" aria-controls="A_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                        <label>Filter by Courier Partner</label>
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                @foreach($partners as $p)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="A_c_{{$p->keyword}}">
                                                                        <label class="custom-control-label pt-1" for="A_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="courier_partner" data-modal="A_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_CourierFilterModal" data-form="A_CourierForm" data-id="filter_order" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                AWB Number
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_AWBNumberFilterModal" role="button" aria-expanded="false" aria-controls="A_AWBNumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_AWBNumberFilterModal" style="width: 230px !important; z-index:1;">
                                                        <form action="{{route('seller.my_oms.set_filter')}}" class="filterForm mt-0" method="post" id="A_AWBNumberForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="awb_number">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="form-group">
                                                                <label for="A_searchByAWB">Search by AWB Number</label>
                                                                <input type="text" class="form-control" name="value" id="A_searchByAWB" placeholder="AWB Number">
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="awb_number" data-modal="A_AWBNumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_AWBNumberFilterModal" data-form="A_AWBNumberForm" data-id="filter_order" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="10%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="filter_order">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text" class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                <div class="float-right">
                                    <a>Show
                                        <select name="per_page_record" class="perPageRecord">
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="250">250</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                        </select>
                                        Per Page</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="content-inner" id="form_div" style="display: none;">
            <form id="order_form" method="post" action="{{route('seller.add_order')}}">
                @csrf
                <input type="hidden" name="order_id" id="oid">
                <div class="card mb-1">
                    <div class="card-header">
                        <h4>Add New Order
                            <div class="float-right">
                                <button type="button" class="btn btn-primary BackButton btn-sm mx-0"><i class="fal fa-arrow-alt-left"></i> Go Back</button>
                            </div></h4>
                    </div>
                </div>
                <div class="card mb-1">
                    <div class="card-header">
                        <h5>Order Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="order_tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Customer Order Number</label>
                                    <input type="text" class="form-control" placeholder="Customer Order Number" id="customer_order_number" name="customer_order_number" required>
                                </div>
                            </div>
                            <div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <label>Order Id</label>
                                    <input type="text" class="form-control" placeholder="Order Id" id="order_number" name="order_number" value="" required readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <div class="input-group mb-3">
                                        <select class="custom-select" id="order_type" name="order_type" required>
                                            <!-- <option value="" disabled>Payment Type</option> -->
                                            <option value="prepaid" id="type_prepaid" selected>Prepaid</option>
                                            <option value="cod" id="type_cod">Cash on Delivery</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="mb-3">Order Type</label><br>
                                    <input type="radio" name="o_type" value="forward" checked id="o_type_forward"> Forward
                                    <input type="radio" name="o_type" value="reverse" class="ml-3" id="o_type_reverse"> Reverse
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="mb-3">Shipment Type</label><br>
                                    <input type="radio" name="shipment_type" value="single" class="shipment_type" checked id="shipment_type_single"> Single
                                    <input type="radio" name="shipment_type" value="mps" class="shipment_type ml-3" id="shipment_type_mps"> MPS
                                </div>
                            </div>
                            <div class="col-md-4" style="display: none;">
                                <div class="form-group">
                                    <label>Number of packets</label>
                                    <input type="number" class="form-control" placeholder="Number of packets" id="number_of_packets" name="number_of_packets" required>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="orderTabButton" type="button">Next</button>
                    </div>
                </div>
                <div class="card mb-1">
                    <div class="card-header">
                        <h5>Shipping Information <span class="h6" id="reverse_ship_message"></span></h5>
                    </div>
                    <div class="card-body all_tabs" id="shipping_tab" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="text" class="form-control" placeholder="Customer Name" id="customer_name" name="customer_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Mobile Number</label>
                                    <div class="input-group ship-form-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control" id="country" name="contact_code" required>
                                                <option value="+91">+91</option>
                                                <option value="+7">+7</option>
                                                <option value="+1">+1</option>
                                            </select>
                                        </div>
                                        <input type="number" class="form-control" maxlength="10" placeholder="Phone Number" id="contact" name="contact" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea type="text" class="form-control" rows="3" placeholder="Address 1" id="address" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address 2 (optional)</label>
                                    <textarea type="text" class="form-control" rows="3" placeholder="Address 2" id="address2" name="address2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="number" class="form-control" placeholder="Pincode" id="pincode" name="pincode" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" class="form-control" placeholder="Country" id="txtCountry" name="country" required {{Session()->get('MySeller')->pincode_editable == 'n' ? 'readonly' : ''}}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" class="form-control" placeholder="State" id="state" name="state" required {{Session()->get('MySeller')->pincode_editable == 'n' ? 'readonly' : ''}}>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" placeholder="City" id="city" name="city" required {{Session()->get('MySeller')->pincode_editable == 'n' ? 'readonly' : ''}}>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn-primary pull-right" id="ShippingTabButton" type="button">Next</button>
                        <button class="btn btn-primary pull-right" id="PreviousTabButton" type="button">Previous
                        </button>
                    </div>
                </div>
                <div class="card mb-1">
                    <div class="card-header">
                        <h5>Product Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="product_tab" style="display:none;">
                        <div class="table-responsive">
                            <div id="single-packets">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Field</th>
                                        </tr>
                                    </thead>
                                    <tbody id="single_shipment_product_details">
                                        <tr>
                                            <td><input type="text" data-id="1" id="product_sku1" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU" required/></td>
                                            <td><input type="text" data-id="1" id="product_name1" name="product_name[]" class="form-control product_name" placeholder="Product Name" required/></td>
                                            <td><input type="number" data-id="1" id="product_qty1" name="product_qty[]" class="form-control product_qty" value="1" placeholder="Product Quantity" required/></td>
                                            <td>
                                                <button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="mps-packets" style="display: none">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <!-- Dynamically create tab -->
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <!-- Dynamically create tab -->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weight">Weight (kg)</label>
                                    <input type="text" class="form-control weightfield" placeholder="Weight (In Kg.)" id="weight" name="weight" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="length">Length (cm)</label>
                                    <input type="text" class="form-control" placeholder="Length" id="length" name="length" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="breadth">Breadth (cm)</label>
                                    <input type="text" class="form-control" placeholder="Breadth" id="breadth" name="breadth" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="height">Height (cm)</label>
                                    <input type="text" class="form-control" placeholder="Height" id="height" name="height" required>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="ProductTabButton" type="button">Next</button>
                        <button class="btn btn-primary pull-right" id="PreviousTabButton2" type="button">Previous
                        </button>
                    </div>
                </div>
                <div class="card mb-1">
                    <div class="card-header">
                        <h5>Other Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="other_tab" style="display:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Invoice Amount</label>
                                    <input type="text" class="form-control" placeholder="Invoice Amount" id="invoice_amount" name="invoice_amount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reseller Name</label>
                                    <input type="text" class="form-control" placeholder="Reseller Name" id="reseller_name" name="reseller_name">
                                </div>
                            </div>
                            <div class="col-md-3" id="ewaybillDiv" style="display: none;">
                                <div class="form-group">
                                    <label>E-Way Bill Number</label>
                                    <input type="text" class="form-control" placeholder="E-Way Bill Number" id="ewaybill_number" name="ewaybill_number" value="">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="WarehouseTabButton" type="button">Next</button>
                        <button class="btn btn-primary pull-right" id="PreviousProductTabButton" type="button">
                            Previous
                        </button>

                    </div>
                </div>
                <div class="card mb-1">
                    <div class="card-header">
                        <h5>Warehouse Information <span class="h6" id="reverse_warehouse_message"></span></h5>
                    </div>
                    <div class="card-body warehouse_card all_tabs" id="warehouse_tab" style="display:none;">
                        <h6>Select Warehouse</h6>
                        <div class="row">
                            @forelse($wareHouse as $w)
                                <div class="col-sm-6 col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="radio" id="warehouse_{{$w->id}}" name="warehouse" data-id="{{$w->id}}" class="warehouse_select" value="{{$w->id}}" {{$w->default == 'y' ? 'checked':''}}>
                                            <label for="warehouse_{{$w->id}}" class="h6 text-dark font-weight-bold">{{$w->warehouse_name}}</label><br>
                                            <div class="h6 mb-0 text-muted">{{$w->address_line1}},{{$w->address_line2}}</div>
                                            <div class="h6 mb-0 text-muted">{{$w->state}},{{$w->city}},{{$w->pincode}}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-sm-6 col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="h5 mb-0 font-weight-bold">No Warehouse Found</div>
                                            <h6 class="h6 mb-0 text-muted"><a href="{{route('seller.warehouses')}}">Click Here to Add Warehouse</a></h6>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button class="btn btn-primary pull-right" id="SubmitOrderData" type="submit" {{count($wareHouse) == 0 ? 'disabled' : ''}}>Submit</button>
                        <button class="btn btn-primary pull-right" id="PreviousOtherTabButton" type="button">
                            Previous
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="modal fade" id="bulkupload" tabindex="-1" role="dialog" aria-labelledby="bulkuploadTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="{{route('seller.import_csv_my_oms_order')}}" enctype="multipart/form-data" id="bulkimportform">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 pb-10 mb-2">
                            Download sample order upload file : <a class="text-info" href="{{url('public/assets/seller/my-oms-order.csv')}}">Download</a>
                        </div>
                        <div class="col-sm-12">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile02" name="importFile">
                                        <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-info btn-sm" id="BulkImportSubmitButton">Upload</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Ware house Modal -->
<div class="modal fade bd-example-modal-XL" id="courier_partner_select" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Select Courier Partner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('seller.single_ship_order')}}" method="post" name="singleForm" id="singleForm">
                <div class="modal-body" id="partner_details_ship">
                </div>
            </form>
        </div>
    </div>
</div>

<!--Dimension Modal -->
<div class="modal fade" id="dimensionModal" tabindex="-1" role="dialog" aria-labelledby="dimensionModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dimensionModal23">Modify Product Dimension</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('seller.modify_dimension')}}" method="post" id="dimensionForm">
                    <input type="hidden" name="order_id" id="order_id_dimesion">
                    @csrf
                    <input type="hidden" name="weight_page_type" id="weight_page_type">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="weight">Weight (In Kg.)</label>
                                <input type="number" class="form-control input-sm weightfield" placeholder="Weight" id="weight_single" name="weight" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="length">Length (cm)</label>
                                <input type="number" class="form-control" placeholder="Length (cm)" id="length_single" name="length" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="breadth">Breadth (cm)</label>
                                <input type="number" class="form-control" placeholder="Breadth (cm)" id="breadth_single" name="breadth" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="height">Height (cm)</label>
                                <input type="number" class="form-control" placeholder="Height (cm)" id="height_single" name="height" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close
                                </button>
                                <button type="button" id="dimesionSubmitBtn" class="btn btn-info btn-sm">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="multipleDimensionModal" tabindex="-1" role="dialog" aria-labelledby="dimensionModal24" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title" id="dimensionModal24"><img src="{{asset('assets/1.png')}}" width="25" height="25"/> Bulk Update (Wt./Dim.)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('seller.modify_multiple_dimension')}}" method="post" id="multipleDimensionForm">
                    <input type="hidden" name="number_of_orders" id="number_of_orders">
                    @csrf
                    <div id="multiple_dimension_details">
                    </div>
                    <div class="modal-footer pb-0">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close
                                </button>
                                <button type="button" id="multipleDimesionSubmitBtn" class="btn btn-info btn-sm">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Total Order Modal -->
<div class="modal fade" id="allOrderDetail" tabindex="-1" role="dialog" aria-labelledby="allOrderDetail" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title h3" id="exampleModalLabel"><img src="{{asset($config->favicon)}}" style="height:30px;width:30px;"> {{$config->title}}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <h6>No of Orders : <span id="total_selected_order"></span></h6>
                        <h6>Required Freight Charge : <span id="total_freight_charge"></span></h6>
                        <hr>
                        <!-- <h6>Your Total Balance is : <span id="seller_balance"></span></h6>
                        <h6>Your Available Shipment Balance is : <span id="available_balance"></span></h6> -->
                        <h6 id="error_message" class="text-danger">You don't have a enough balance to ship these orders. Please recharge with ₹ <span id="remaining_ship_charge"></span> to ship </h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" style="display: none;" id="rechargeButton" data-toggle="modal" data-target="#exampleModal" data-placement="top" data-original-title="Make a Recharge">Recharge Now</button>
                <button type="button" class="btn btn-primary btn-sm MultiShipButton" style="display: none;">Proceed to Ship</button>
            </div>
        </div>
    </div>
</div>

@include('seller.pages.scripts')


<script type="text/javascript">
    var totalWeight = 0,rowCounter=1;
    $('[data-toggle="popover"]').popover();

    var pageCount=1,sel_ids=[],currentTab='all_order',isFilter = false,selectedTab='{{isset($_GET['tab'])?$_GET['tab']:"all_orders"}}',totalRecord = 0,perpageLimit = 0;
    $(document).click(function(event) {
        //if you click on anything except the modal itself or the "open modal" link, close the modal
        if (!$(event.target).closest(".filter,.filter-collapse").length) {
            $("body").find(".filter-collapse").removeClass("show");
        }
    });
    var del_ids = [], cnt = 1;
    var limitOrder = $('.limit_order').val();



    function get_value_filter(divId) {
        isFilter = true;
        showOverlay();
        $.ajax({
            method : 'get',
            data : {
                'page' : pageCount
            },
            url: "{{ route('seller.my_oms.ajax_filter_order')}}",
            success: function (response) {
                $('#'+divId).html(response);
                $('#total_filter_order').html($('#total_order_count').val());
                var orderCount = $('#total_order_count').val();
                var perPage=$('.perPageRecord').val();
                // console.log(orderCount + " = " + limitOrder + " = " + $('.perPageRecord').val());
                if(parseInt(orderCount) < parseInt(perPage))
                    $('.order_display_limit').html(orderCount);
                else
                {
                    if(parseInt(perPage) > parseInt(orderCount))
                        $('.order_display_limit').html(orderCount);
                    else
                        $('.order_display_limit').html(perPage);
                }
                $('#order_count').html($('#total_order_count').val());
                $('.totalPage').html(Math.ceil($('#total_order_count').val() / $('.order_display_limit').html()));
                hideOverlay();
            }
        });
    }

    // $('#total_filter_order').html($('#total_order_count').val());

    $(document).ready(function () {

        $("#A_searchBySingleProduct").change(function() {
            if($(this).prop('checked')) {
                $("#A_searchByProductSku").val('');
                $("#A_searchByProductSku").attr('readonly', true);
                $("#A_searchByMatchExactProduct").prop('checked', false);
                $("#A_searchByMatchExactProduct").attr('disabled', true);
                $("#A_searchByMultiProduct").prop('checked', false);
            } else {
                $("#A_searchByProductSku").attr('readonly', false);
                $("#A_searchByMatchExactProduct").attr('disabled', false);
            }
        });

        $("#A_searchByMultiProduct").change(function() {
            if($(this).prop('checked')) {
                $("#A_searchByProductSku").val('');
                $("#A_searchByProductSku").attr('readonly', true);
                $("#A_searchByMatchExactProduct").prop('checked', false);
                $("#A_searchByMatchExactProduct").attr('disabled', true);
                $("#A_searchBySingleProduct").prop('checked', false);
            } else {
                $("#A_searchByProductSku").attr('readonly', false);
                $("#A_searchByMatchExactProduct").attr('disabled', false);
            }
        });

        $('.export_order_btn').click(function () {
            export_ids = [];
            if(currentTab === 'ready_to_ship'){
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        export_ids.push($(this).val());
                });
            }
            else{
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        export_ids.push($(this).val());
                });
            }
            $('#export_order_id').val(export_ids);
            $('#ExportOrderForm').submit();
        });



        $('#o_type_reverse').click(function(){
            $('#type_prepaid').prop("selected", true);
            $('#type_cod').prop("disabled", true);
        });

        $('#o_type_forward').click(function(){
            $('#type_cod').prop("disabled", false);
        });

        $(".shipment_type").change(function() {
            if($(this).val() == 'mps') {
                $("#number_of_packets").parent().parent().show();
                // $("#mps-packets").show();
                // $("#single-packets").hide();
                // $("#single_shipment_product_details").empty();
            } else {
                $("#number_of_packets").parent().parent().hide();
                // $("#mps-packets").hide();
                // $("#single-packets").show();
                // $("#single_shipment_product_details").html(`
                //     <tr>
                //         <td><input type="text" data-id="1" id="product_sku1" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU" required/></td>
                //         <td><input type="text" data-id="1" id="product_name1" name="product_name[]" class="form-control product_name" placeholder="Product Name" required/></td>
                //         <td><input type="number" data-id="1" id="product_qty1" name="product_qty[]" class="form-control product_qty" value="1" placeholder="Product Quantity" required/></td>
                //         <td>
                //             <button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button>
                //         </td>
                //     </tr>
                // `);
            }
        });

        // $("#number_of_packets").change(function() {
        //     $("#myTab").empty();
        //     $("#myTabContent").empty();
        //     for(let i=1; i <= $(this).val(); i++) {
        //         ++rowCounter;
        //         $("#myTab").append(`
        //             <li class="nav-item" role="presentation">
        //                 <a class="nav-link ${i == 1 ? 'active' : '' }" id="packet-${i}-tab" data-toggle="tab" href="#packet-${i}" role="tab" aria-controls="packet-${i}" aria-selected="${i == 1 ? 'true' : 'false' }">Packet ${i}</a>
        //             </li>
        //         `);

        //         $("#myTabContent").append(`
        //             <div class="tab-pane fade show ${i == 1 ? 'active' : '' }" id="packet-${i}" role="tabpanel" aria-labelledby="packet-${i}-tab">
        //                 <table class="table table-hover">
        //                     <thead>
        //                         <tr>
        //                             <th>SKU</th>
        //                             <th>Product</th>
        //                             <th>Quantity</th>
        //                             <th>Field</th>
        //                         </tr>
        //                     </thead>
        //                     <tbody id="mps-product-detail-${i}">
        //                         <tr>
        //                             <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku${i}[]" class="form-control product_sku" placeholder="Product SKU" required/></td>
        //                             <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name${i}[]" class="form-control product_name" placeholder="Product Name" required/></td>
        //                             <td><input type="number" data-id="${i}" id="product_qty${i}" name="product_qty${i}[]" class="form-control product_qty" value="1" placeholder="Product Quantity" required/></td>
        //                             </td>
        //                             <td>
        //                                 <button type="button" name="add" class="btn btn-info btn-sm add" data-target="mps-product-detail-${i}" data-tab="${i}"><i class="fa fa-plus"></i></button>
        //                             </td>
        //                         </tr>
        //                     </tbody>
        //                 </table>
        //             </div>
        //         `);
        //     }
        // });


        //for pagination page number searching
        $('#nav-tabContent').on('keyup', '#txtPageCount', function (e) {
            // $('#txtPageCount').keyup(function(e){
            if(e.keyCode == 13){
                if(parseInt($(this).val().trim()) > 0){
                    if(parseInt($(this).val().trim()) <= parseInt($('.totalPage').html()) ){
                        showOverlay();
                        pageCount = parseInt($(this).val().trim());
                        all_order();
                    }
                }
            }
        });

        $('#weight').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#invoice_amount').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        }).blur(function () {
            let invoiceAmount = parseInt($(this).val());
            if(!isNaN(invoiceAmount)){
                if(invoiceAmount >= 50000)
                    $('#ewaybillDiv').fadeIn();
                else
                    $('#ewaybillDiv').hide();
            }
        });

        // if(pageCount == 1){
        // $('.previousPageButton').hide();
        // }

        $(document).on('keypress','#contact',function(e){
            if($(e.target).prop('value').length>=10){
                if(e.keyCode!=32)
                {return false}
            }})

        $(document).on('keypress','#pincode',function(e){
            if($(e.target).prop('value').length>=6){
                if(e.keyCode!=32)
                {return false}
            }})


        $('#bulkupload').on('click', '#BulkImportSubmitButton', function () {
            if($('#bulkimportform').valid()){
                showOverlay();
            }
        });

        $('#courier_partner_select').on('click', '.ShipOrderBtn', function () {
            $(this).prop('disabled',true);
            var id = $(this).data('id');
            $('#partner_' + id).trigger('click');
            showOverlay();
            document.singleForm.submit();
        });

        //get the file name
        $('#inputGroupFile02').on('change',function(){
            var fileName = $(this).val();
            $(this).next('.custom-file-label').html(fileName);
        })

        //For Collapsing other model when one is open
        var $collapsing = $('#nav-tabContent');
        $collapsing.on('show.bs.collapse', '.collapse', function () {
            $collapsing.find('.collapse.show').collapse('hide');
        });

        //To prevent the Enter Submit of ajax searching
        $('form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        all_order();

        $('.firstPageButton').click(function(){
            if(pageCount > 1){
                pageCount = 1 ;
                //showOverlay();
                all_order();
            }

        });

        $('.previousPageButton').click(function(){
            if(pageCount > 1){
                pageCount--;
                //showOverlay();
                all_order();
            }
        });

        $('.nextPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount++;
                //showOverlay();
                all_order();
            }
        });

        $('.lastPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount = $('.totalPage').html();
                //showOverlay();
                all_order();
            }
        });


        $('#nav-tabContent').on('change', '.perPageRecord', function () {
            showOverlay();
            cnt = 0;
            $('.total_order_selected').html(cnt);
            var page = $(this).val();
            $.ajax({
                url: '{{url('/')."/per_page_record/"}}' +page,
                success: function (response) {
                    $('.perPageRecord').val(page);
                    all_order();
                    hideOverlay();
                },
                error: function (response) {
                    hideOverlay();
                    // $.notify(" Oops... Something went wrong!", {
                    //     animationType: "scale",
                    //     align: "right",
                    //     type: "danger",
                    //     icon: "close"
                    // });
                }
            });
        });

        function all_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.my_oms.get')}}',
                success: function (response) {
                    $('#filter_order').html(response);
                    $('#total_filter_order').html($('#total_order_count').val());
                    var orderCount = $('#total_order_count').val();
                    var perPage=$('.perPageRecord').val();
                    // console.log(orderCount + " = " + limitOrder + " = " + $('.perPageRecord').val());
                    if(parseInt(orderCount) < parseInt(perPage))
                        $('.order_display_limit').html(orderCount);
                    else
                    {
                        if(parseInt(perPage) > parseInt(orderCount))
                            $('.order_display_limit').html(orderCount);
                        else
                            $('.order_display_limit').html(perPage);
                    }
                    $('#order_count').html($('#total_order_count').val());
                    $('.totalPage').html(Math.ceil($('#total_order_count').val() / $('.order_display_limit').html()));
                    hideOverlay();
                },
                error: function (response) {
                    hideOverlay();
                    // $.notify(" Oops... Something went wrong!", {
                    //     animationType: "scale",
                    //     align: "right",
                    //     type: "danger",
                    //     icon: "close"
                    // });
                }
            });
        }

        $('.currentPage').val(pageCount);

        // filter code goes here  nav-tabContent
        $('#nav-tabContent').on('click', '.applyChannelFilter', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            // alert(that.data('id'));
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Order Id Filter
        $('#nav-tabContent').on('click', '.applyFilterOrder', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        $('.applyFilterOrderSearch').click(function () {
            var that=$(this);
            var form=that.data('form');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Payment Type Filter
        $('#nav-tabContent').on('click', '.applyFilterPayment', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //order Amount Filter
        $('#nav-tabContent').on('click', '.applyFilterAmount', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Product Name or SKU Filter
        $('#nav-tabContent').on('click', '.applyFilterProduct', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            // alert(that.data('id'));
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Date Filter
        $('#nav-tabContent').on('click', '.applyFilterDate', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Reset Key Filtering
        $('#data_div').on('click', '.reset_value', function () {
            var that=$(this);
            var key=that.data('key');
            var id=that.data('id');
            var modal=that.data('modal');
            $.ajax({
                type : 'get',
                url :  '{{url('/')."/my-oms/reset_key/"}}' + key,
                success : function(){
                    get_value_filter(that.data('id'));
                    $('#'+modal).collapse('hide');
                    $("#A_searchByProductSku").attr('readonly', false);
                    $("#A_searchByMatchExactProduct").attr('disabled', false);
                }

            });
        });
        //Pickup Address Filter
        $('#nav-tabContent').on('click', '.applyFilterPickupAddress', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Delivery Address Filter
        $('#nav-tabContent').on('click', '.applyFilterDeliveryAddress', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Order Status Filter
        $('#nav-tabContent').on('click', '.applyStatusFilter', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //AWB Number Filter
        $('#nav-tabContent').on('click', '.applyFilterAWBNumber', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Weight Filter
        $('#nav-tabContent').on('click', '.applyFilterWeight', function () {
            var that=$(this);
            var form=that.data('form');
            var modal=that.data('modal');
            $('#'+modal).collapse('hide');
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //All Order AWB or Order Id Reset
        $('#nav-tabContent').on('click', '.allOrderResetBtn', function () {
            showOverlay();
            all_order();
        });


        //Fetch  dimension data using weight
        $('#form_div').on('blur', '.product_sku', function () {
            var that = $(this);
            if(that.val().trim()!=="") {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/fetch_product_sku/"}}' + that.val(),
                    success: function (response) {
                        hideOverlay();
                        if (response != '0') {
                            var info = JSON.parse(response);
                            $('#product_name' + that.data('id')).val(info.product_name);
                            $('#product_unitprice' + that.data('id')).val(info.product_price);
                            var weight = parseFloat(info.weight);
                            if(weight != NaN)
                                totalWeight += weight;
                            $('#weight').val(totalWeight);
                        }
                    },
                    error: function (response) {
                        hideOverlay();
                        // $.notify(" Oops... Something went wrong!", {
                        //     animationType: "scale",
                        //     align: "right",
                        //     type: "danger",
                        //     icon: "close"
                        // });
                    }
                });
            }
        });

        //Fetch  dimension data using weight
        $('#form_div').on('blur', '.weightfield', function () {
            var that = $(this);
            if(that.val().trim() === '')
                return false;
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + (that.val() * 1000),
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#length').val(info.length);
                    $('#breadth').val(info.width);
                    $('#height').val(info.height);
                },
                error: function (response) {
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

        //get dimensi data in modal
        $('#nav-tabContent').on('click', '.dimensiondata', function () {
            showOverlay();
            var that = $(this);

            $.ajax({
                url: '{{url('/')."/modify_dimension_data/"}}' + that.data('id'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#order_id_dimesion').val(info.id);
                    $('#weight_single').val((info.weight / 1000));
                    $('#length_single').val(info.length);
                    $('#breadth_single').val(info.breadth);
                    $('#height_single').val(info.height);
                    $('#weight_page_type').val(that.data('type'));
                    $('#dimensionModal').modal('show');
                    hideOverlay();
                },
                error: function (response) {
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


        // Ajax Submit Dimension Form
        $('#dimesionSubmitBtn').click(function () {
            showOverlay();
            $('#dimensionForm').ajaxSubmit({
                success: function (response) {
                    $.notify(" Success... Dimensions Updated Successfully", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#dimensionModal').modal('hide');
                    // $('#filter_order').html(response);
                    // $('#unprocessable_order_data').html(response);
                    // $('#ready_to_ship_data').html(response);
                    // $('#processing_order_data').html(response);
                    // $('#shipped_order_data').html(response);
                    // $('#return_order_data').html(response);
                    // countOrder();
                    hideOverlay();
                    // Get selected deimensions
                    let l = $('#length_single').val();
                    let b = $('#breadth_single').val();
                    let h = $('#height_single').val();
                    // console.log(l,b,h);
                    let vol_weight = (l * b * h) / 5000;
                    // console.log(vol_weight);
                    $(".OD-"+$("#order_id_dimesion").val()).html('\
                        <div>\
                            <span class="">Wt</span> : '+$('#weight_single').val()+'\
                            <a class="dimensiondata ml-1" data-type="all_order" data-placement="top" data-toggle="tooltip" data-original-title="Edit Dimension" data-id="'+$('#order_id_dimesion').val()+'"> <i class="fas fa-edit fa-lg text-primary"></i></a>\
                            <br>\
                            (<span class="">L</span> * <span class="">B</span> * <span class="">H</span>) :\
                            <span class="">'+l+'</span> * <span class="">'+b+'</span> * <span class="">'+h+'</span><br>\
                            <span>Vol.Wt</span> : '+vol_weight.toFixed(2)+'<br>\
                        </div>\
                    ');
                }
            });
        });

        $('#multipleDimesionSubmitBtn').click(function () {
            showOverlay();
            $('#multipleDimensionForm').ajaxSubmit({
                success: function (res) {
                    console.log(res);
                    res.forEach(function(order) {
                        $(`.OD-${order.id}`).html(`
                            <div>
                                <span class="">Wt</span> : ${order.weight/1000}
                                <a class="dimensiondata ml-1" data-type="all_order" data-placement="top" data-toggle="tooltip" data-original-title="Edit Dimension" data-id="${order.id}"> <i class="fas fa-edit fa-lg text-primary"></i></a>
                                <br>
                                (<span class="">L</span> * <span class="">B</span> * <span class="">H</span>) :
                                <span class="">${order.length}</span> * <span class="">${order.breadth}</span> * <span class="">${order.height}</span><br>
                                <span>Vol.Wt</span> : ${(order.vol_weight/1000).toFixed(2)}<br>
                            </div>
                        `);
                    });
                    $.notify(" Success... Dimensions Updated Successfully", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#multipleDimensionModal').modal('hide');
                    hideOverlay();
                }
            });
        });

        $('#nav-tabContent').on('click', '.modify_data', function () {
            showOverlay();
            var that = $(this);
            $.ajax({
                url: '{{url('/')."/my-oms/modify-order/"}}' + that.data('id'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#order_form').prop('action', '{{route('seller.my_oms.update')}}');
                    $('#oid').val(info.order.id);
                    $('#customer_order_number').val(info.order.customer_order_number);
                    $('#order_number').val(info.order.order_number);

                    if (info.order.shipment_type == 'mps') {
                        $('#shipment_type_mps').prop('checked', true);
                        $('#shipment_type_mps').trigger('change');
                        $('#number_of_packets').val(info.order.number_of_packets);
                        $('#number_of_packets').trigger('change');
                    } else {
                        $('#shipment_type_single').prop('checked', true);
                    }

                    $('#customer_name').val(info.order.b_customer_name);
                    // $('#country').val(info.order.b_contact_code);
                    $('#contact').val(info.order.b_contact);
                    $('#address').val(info.order.b_address_line1);
                    $('#address2').val(info.order.b_address_line2);
                    $('#pincode').val(info.order.b_pincode);
                    $('#txtCountry').val(info.order.b_country);
                    $('#state').val(info.order.b_state);
                    $('#ewaybill_number').val(info.order.ewaybill_number);
                    $('#city').val(info.order.b_city);
                    $('#weight').val(info.order.weight / 1000);
                    $('#height').val(info.order.height);
                    $('#length').val(info.order.length);
                    $('#breadth').val(info.order.breadth);

                    if (info.order.order_type == 'cod')
                        $('#type_cod').prop('selected', true);
                    else if (info.order.order_type == 'prepaid')
                        $('#type_prepaid').prop('selected', true);
                    else
                        $('#type_reverse').prop('selected', true);

                    if (info.order.o_type == 'forward')
                        $('#o_type_forward').prop('checked', true);
                    else
                        $('#o_type_reverse').prop('checked', true);

                    // if($(".shipment_type:checked").val() == "single") {
                    //     $('#single_shipment_product_details').empty();
                    //     for (var i = 0; i < info.product.length; i++) {
                    //         add_row_update(i);
                    //     }
                    // } else {
                    //     $('#mps-product-detail-1').empty();
                    //     for (var i = 1; i <= info.product.length; i++) {
                    //         add_mps_row_update(i, 'mps-product-detail-1', '1');
                    //     }
                    // }

                    $('#single_shipment_product_details').empty();
                    for (var i = 0; i < info.product.length; i++) {
                        add_row_update(i);
                    }

                    // if($(".shipment_type:checked").val() == "single") {
                    //     for (var i = 0; i < info.product.length; i++) {
                    //         $('#product_sku' + [i]).val(info.product[i].product_sku);
                    //         $('#product_name' + [i]).val(info.product[i].product_name);
                    //         $('#product_unitprice' + [i]).val(info.product[i].product_unitprice);
                    //         $('#product_qty' + [i]).val(info.product[i].product_qty);
                    //         $('#total_amount' + [i]).val(info.product[i].total_amount);
                    //     }
                    // } else {
                    //     for (var i = 0; i < info.product.length; i++) {
                    //         $('#product_sku' + (i+1)).val(info.product[i].product_sku);
                    //         $('#product_name' + (i+1)).val(info.product[i].product_name);
                    //         $('#product_unitprice' + (i+1)).val(info.product[i].product_unitprice);
                    //         $('#product_qty' + (i+1)).val(info.product[i].product_qty);
                    //         $('#total_amount' + (i+1)).val(info.product[i].total_amount);
                    //     }
                    // }

                    for (var i = 0; i < info.product.length; i++) {
                        $('#product_sku' + [i]).val(info.product[i].product_sku);
                        $('#product_name' + [i]).val(info.product[i].product_name);
                        $('#product_unitprice' + [i]).val(info.product[i].product_unitprice);
                        $('#product_qty' + [i]).val(info.product[i].product_qty);
                        $('#total_amount' + [i]).val(info.product[i].total_amount);
                    }

                    $('#invoice_amount').val(info.order.invoice_amount);
                    $('#warehouse_'+info.order.warehouse_id).prop('checked', true);
                    $('#reseller_name').val(info.order.reseller_name);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    hideOverlay();
                },
                error: function (response) {
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

        //axxording of order detail form
        $('#orderTabButton').click(function () {
            $('input[name="customer_order_number"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#shipping_tab').slideDown();
            }
        });

        $('#ShippingTabButton').click(function () {
            $('input[name="customer_name"]').valid();
            $('input[name="contact"]').valid();
            $('textarea[name="address"]').valid();
            $('input[name="pincode"]').valid();
            $('input[name="state"]').valid();
            $('input[name="city"]').valid();
            $('input[name="country"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#product_tab').slideDown();
            }
        });

        $('#ProductTabButton').click(function () {
            // $('input[name="weight"]').valid();
            // $('input[name="length"]').valid();
            // $('input[name="height"]').valid();
            // $('input[name="breadth"]').valid();
            // $('input[name="product_name[]"]').valid();
            // $('input[name="product_sku[]"]').valid();
            // $('input[name="breadth"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#other_tab').slideDown();
            }
        });
        $('#WarehouseTabButton').click(function () {
            $('input[name="invoice_amount"]').valid();
            $('input[name="ewaybill_number"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('.warehouse_card').show();
                $('#warehouse_tab').slideDown();
            }
        });

        $('#SubmitOrderData').click(function () {
            $('input[name="warehouse"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
            }
            showOverlay();
        });

        $('#PreviousTabButton2').click(function () {
            $('.all_tabs').slideUp();
            $('#shipping_tab').slideDown();
        });

        $('#PreviousTabButton').click(function () {
            $('.all_tabs').slideUp();
            $('#order_tab').slideDown();
        });

        $('#PreviousProductTabButton').click(function () {
            $('.all_tabs').slideUp();
            $('#product_tab').slideDown();
        });

        $('#PreviousOtherTabButton').click(function () {
            $('.all_tabs').slideUp();
            $('#other_tab').slideDown();
        });


        $(document).on('click', '.add', function () {
            ++rowCounter;
            // if($(".shipment_type:checked").val() == "mps") {
            //     add_mps_row(rowCounter, $(this).data("target"), $(this).data("tab"));
            // } else if($(".shipment_type:checked").val() == "single") {
            //     add_row(rowCounter);
            // }
            add_row(rowCounter);
        });

        function add_row(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            $('#single_shipment_product_details').append(html);
        }

        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity"/></td>';
            if(cnt === 0){
                html += '<td><button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button></td></tr>';
            }else{
                html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            }
            $('#single_shipment_product_details').append(html);
        }

        function add_mps_row(i, target, tab) {
            var html = `
                <tr>
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku${tab}[]" class="form-control product_sku" placeholder="Product SKU" /></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name${tab}[]" class="form-control product_name" required="" placeholder="Product Name" /></td>
                    <td><input type="number" data-id="${i}" id="product_qty${i}" name="product_qty${tab}[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" /></td>
                    </td>
                    <td>
                        <button type="button" name="remove" class="btn btn-danger btn-sm remove" data-id="${i}"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $(`#${target}`).append(html);
        }

        function add_mps_row_update(i, target, tab) {
            var html = `
                <tr>
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku1[]" class="form-control product_sku" placeholder="Product SKU" /></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name1[]" class="form-control product_name" required="" placeholder="Product Name" /></td>
                    <td><input type="number" data-id="${i}" id="product_qty${i}" name="product_qty1[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" /></td>
                    </td>
                    <td>
                        ${(i == 1 ?
                            '<button type="button" name="add" class="btn btn-info btn-sm add" data-target="mps-product-detail-1" data-tab="1"><i class="fa fa-plus"></i></button>'
                        :
                            '<button type="button" name="remove" class="btn btn-danger btn-sm remove" data-id="'+i+'"><i class="fa fa-minus"></i></button>'
                        )}
                    </td>
                </tr>
            `;
            $(`#${target}`).append(html);
        }

        $(document).on('click', '.remove', function () {
            var id = $(this).data('id');
            $('#total_amount' + id).val('');
            $(this).closest('tr').remove();
        });

        $('.addInfoButton').click(function () {
            $('#data_div').hide();
            $('#form_div').fadeIn();
            $('#order_form').trigger("reset");
            $('#reverse_ship_message').html('');
            $('#reverse_warehouse_message').html('');
        });

        $('.BackButton').click(function() {
            // $('.card-body').show();
            $('#data_div').show();
            $('#form_div').hide();
            $('#order_form').trigger('reset');
        });

        $('#cancelButton').click(function () {
            $('#order_form').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });

        $('#pincode').blur(function () {
            var that = $(this);
            if (that.val().trim().length === 6) {
                that.removeClass('invalid');
                showOverlay();
                $.ajax({
                    type: 'get',
                    url: '{{url('/')}}' + '/pincode-detail/' + that.val(),
                    success: function (response) {
                        hideOverlay();
                        var info = JSON.parse(response);
                        if (info.status == "Success") {
                            $('#city').val(info.city);
                            $('#state').val(info.state);
                            $('#txtCountry').val(info.country);
                        } else {
                            $.notify(" Oops... Invalid Pincode", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                            that.val('');
                        }
                    },
                    error: function (response) {
                        hideOverlay();
                    }
                });
            } else {
                that.addClass('invalid');
            }
        });

        $('#partner_details_ship').on('click', '.partner_select', function () {
            var that = $(this);
            $('#shipping_charge_single').val($('#shipping_charge_' + that.data('id')).html());
            $('#cod_charge_single').val($('#cod_charge_' + that.data('id')).html());
            $('#early_cod_charge').val($('#early_cod_charge_' + that.data('id')).html());
            $('#gst_charge').val($('#gst_charge_' + that.data('id')).html());
            $('#rto_charge_single').val($('#rto_charge_' + that.data('id')).html());
            $('#total_charge').val($('#total_charge_' + that.data('id')).html());
        });


        $('#nav-tabContent').on('click', '.remove_data', function () {

            var that = $(this);
            if (window.confirm("Are you sure want to Delete?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/my-oms/delete-order"}}/' + that.data('id'),
                    success: function (response) {
                        location.reload();
                        hideOverlay();
                        // $.notify(" Order has been deleted.", {
                        //     blur: 0.2,
                        //     delay: 0,
                        //     verticalAlign: "top",
                        //     animationType: "scale",
                        //     align: "right",
                        //     type: "success",
                        //     icon: "check"
                        // });
                        // $('#row' + that.data('id')).remove();
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
                });
            }
        });

        $('#nav-tabContent').on('click', '#checkAllButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                // $('.selectedCheck').trigger("click");
                $('.selectedCheck').prop('checked', true);
                $('#removeAllButton').fadeIn();
                // if(currentTab == 'all_order' || currentTab == 'unprocessable'){
                //     $('#shipAllButton').hide();
                // }else{
                //     $('#shipAllButton').fadeIn();
                // }
                if(currentTab == 'processing'){
                    $('#shipAllButton').fadeIn();
                    $('#editAllButton').fadeIn();
                }
                if(currentTab == 'all_order' || currentTab == 'ready_to_ship' || currentTab == 'manifest'){
                    $('#cancelSelectButton').fadeIn();
                }
                // $('.total_order_selected').html($('.total_order_display').html());
                $('.total_order_selected').html($('.order_display_limit').html());
                $('#LabelSelectButton').fadeIn();
            } else {
                $('.selectedCheck').prop('checked', false);
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#editAllButton').hide();
                $('#cancelSelectButton').hide();
                $('#LabelSelectButton').hide();
                $('.total_order_selected').html(0);
            }
            updateSelectedCounter();
        });

        if(currentTab == 'all_order'){
            $('#ManifestSelectButton').hide();
            $('#LabelSelectButton').hide();
            $('#InvoiceSelectButton').hide();
        }

        $('#nav-tabContent').on('click', '#checkManifestButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                $('.ManifestCheck').prop('checked', true);
                $('#ManifestSelectButton').fadeIn();
                $('#LabelSelectButton').fadeIn();
                $('#InvoiceSelectButton').fadeIn();
                $('#cancelSelectButton').fadeIn();
                // $('.total_order_selected').html($('.total_order_display').html());
                $('.total_order_selected').html($('.order_display_limit').html());
            } else {
                $('.ManifestCheck').prop('checked', false);
                $('#ManifestSelectButton').hide();
                $('#LabelSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#cancelSelectButton').hide();
                $('.total_order_selected').html(0);
            }
            updateSelectedCounterManifest();
        });

        $('#nav-tabContent').on('click', '.selectedCheck', function () {
            var cnt = 0;
            var that = $(this);
            // $('.selectedCheck').each(function () {
            //     if ($(this).prop('checked'))
            //         cnt++;
            // });
            $('.selectedCheck:visible').each(function () {
                if($(this).prop('checked'))
                    cnt++;
            });
            $('.total_order_selected').html(cnt);
            // updateSelectedCounter();
            if (cnt > 0) {
                if(that.data('status') == 'cancelled'){
                    $('#shipAllButton').hide();
                    $('#editAllButton').hide();
                    $('#removeAllButton').fadeIn();
                }else{
                    $('#removeAllButton').fadeIn();
                    if(currentTab == 'processing'){
                        $('#shipAllButton').fadeIn();
                        $('#editAllButton').fadeIn();
                    }
                    // $('#shipAllButton').fadeIn();
                    if(currentTab == 'all_order' || currentTab == 'ready_to_ship' || currentTab == 'manifest'){
                        $('#cancelSelectButton').fadeIn();
                    }
                }
                if(currentTab == 'all_order') {
                    $('#LabelSelectButton').fadeIn();
                }
            } else {
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#editAllButton').hide();
                $('#cancelSelectButton').hide();
                $('#LabelSelectButton').hide();
            }
            // console.log(cnt);
            // $('.total_order_selected').html(cnt);
        });

        // console.log(cnt);


        $('#nav-tabContent').on('click', '.ManifestCheck', function () {
            var cnt = 0;
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                    cnt++;
            });
            if (cnt > 0) {
                $('#ManifestSelectButton').fadeIn();
                $('#InvoiceSelectButton').fadeIn();
                $('#LabelSelectButton').fadeIn();
                $('#cancelSelectButton').fadeIn();
                $('#PickupRequetedButton').fadeIn();
            } else {
                $('#ManifestSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#LabelSelectButton').hide();
                $('#cancelSelectButton').hide();
                $('#PickupRequetedButton').hide();
            }
            $('.total_order_selected').html(cnt);
        });


        //Download Selected Label
        $('#LabelSelectButton').click(function () {
            order_ids = [];
            // $('.ManifestCheck').each(function () {
            //     if ($(this).prop('checked'))
            //         order_ids.push($(this).val());
            // });
            if(currentTab == 'ready_to_ship') {
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        order_ids.push($(this).val());
                });
            } else {
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        order_ids.push($(this).val());
                });
            }
            //alert(order_ids); return false;
            $('#multilable_id').val(order_ids);
            $('#MultilabelForm').submit();
        });


        $('#removeAllButton').click(function () {
            del_ids = [];

            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    del_ids.push($(this).val());
            });

            if (window.confirm("Are you sure want to Delete?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': del_ids
                    },
                    url: '{{url('/')."/my-oms/remove-selected-order"}}',
                    success: function (response) {
                        hideOverlay();
                        location.reload();
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
                });
            }
        });


        $('#editAllButton').click(function() {
            showOverlay();

            let selectedIds = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    selectedIds.push($(this).val());
            });

            $("#multiple_dimension_details").empty();

            $.ajax({
                url: '{{url('/')."/modify_multiple_dimension_data/"}}',
                data: {
                    ids: selectedIds
                },
                async: false,
                success: function (res) {
                    $("#number_of_orders").val(res.length);
                    sessionStorage.setItem("modify_multiple_dimension_data", JSON.stringify(res));
                    res.forEach(function(order, i) {
                        $("#multiple_dimension_details").append(`
                            <div class="row">
                                <input type="hidden" name="order_id_${i}" value="${order.id}">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        ${i == 0 ? `<input type="checkbox" id="copy_to_all" value="y" data-placement="top" data-toggle="tooltip" data-original-title="Copy first dimension to all"> <label for="copy_to_all" class="ml-2 pb-2"><b>Order Number</b></label> <br>` : ''}
                                        <span>${order.customer_order_number}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="weight_${i}" class="pb-2"><b>Weight (Kg)</b></label>` : ''}
                                        <input type="number" class="form-control input-sm weightfield" placeholder="Weight" name="weight_${i}" id="weight_${i}" value="${order.weight/1000}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="length_${i}" class="pb-2"><b>Length (cm)</b></label>` : ''}
                                        <input type="number" class="form-control" placeholder="Length (cm)" name="length_${i}" id="length_${i}" value="${order.length}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="breadth_${i}" class="pb-2"><b>Breadth (cm)</b></label>` : ''}
                                        <input type="number" class="form-control" placeholder="Breadth (cm)" name="breadth_${i}" id="breadth_${i}" value="${order.breadth}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="height_${i}" class="pb-2"><b>Height (cm)</b></label>` : ''}
                                        <input type="number" class="form-control" placeholder="Height (cm)" name="height_${i}" id="height_${i}" value="${order.height}" required>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                },
                error: function (response) {
                    hideOverlay();
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });

            $('#multipleDimensionModal').modal('show');
            hideOverlay();
        });

        $("#multiple_dimension_details").on("click", "#copy_to_all", function() {
            if($(this).prop('checked')) {
                for(let i=1; i<$("#number_of_orders").val(); i++) {
                    $(`#weight_${i}`).val($("#weight_0").val());
                    $(`#length_${i}`).val($("#length_0").val());
                    $(`#breadth_${i}`).val($("#breadth_0").val());
                    $(`#height_${i}`).val($("#height_0").val());
                }
            } else {
                let data = JSON.parse(sessionStorage.getItem("modify_multiple_dimension_data"));
                for(let i=1; i<$("#number_of_orders").val(); i++) {
                    $(`#weight_${i}`).val(data[i].weight/1000);
                    $(`#length_${i}`).val(data[i].length);
                    $(`#breadth_${i}`).val(data[i].breadth);
                    $(`#height_${i}`).val(data[i].height);
                }
            }
        });

    });


    $('#order_form').validate({
        rules: {
            customer_name: {
                required: true
            },
            customer_order_number: {
                required: true
            },
            contact: {
                required: true,
                minlength: 10,
                maxlength:10
            },
            address: {
                required: true
            },
            pincode: {
                required: true,
                minlength: 6,
                maxlength: 6,
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
            country: {
                required: true
            },
            "product_sku[]": {
                required: true
            },
            "product_name[]": {
                required: true
            },
            weight: {
                required: true,
                number : true
            },
            length: {
                required: true,
                number : true
            },
            breadth: {
                required: true,
                number : true
            },
            height: {
                required: true,
                number : true
            },
            invoice_amount: {
                required: true
            },
            ewaybill_number: {
                required: true
            },
            warehouse: {
                required: true
            },
        },
        messages: {
            customer_order_number: {
                required: "Please enter Your Order Number",
            },
            customer_name: {
                required: "Please enter a Customer Name",
            },
            contact: {
                required: "Please Enter a Mobile Number",
                minlength: "Your mobile number must be 10 digits",
                maxlength: "Your Mobile number must be 10 digits"
            },
            address: {
                required: "Please Enter Address",
            },
            pincode: {
                required: "Please Enter Pincode",
                minlength: "Your Pincode number must be 6 digits",
                maxlength: "Your Pincode number must be 6 digits",
            },
            city: {
                required: "Please Enter City",
            },
            state: {
                required: "Please Enter State",
            },
            country: {
                required: "Please Enter Country",
            },
            "product_sku[]": {
                required: "Please Enter Product SKU",
            },
            "product_name[]": {
                required: "Please Enter Product Name",
            },
            weight: {
                required: "Please Enter Weight",
            },
            length: {
                required: "Please Enter Length",
            },
            breadth: {
                required: "Please Enter Breadth",
            },
            height: {
                required: "Please Enter Height",
            },
            invoice_amount: {
                required: "Please Enter Invoice Amount",
            },
            ewaybill_number: {
                required: "Please Enter Ewaybill Number",
            },
            warehouse: {
                required: "Please Select Warehouse",
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).removeClass('was-validated');
        }
    });

    $('#bulkimportform').validate({
        rules: {
            importFile: {
                required: true
            },
        },
        messages: {
            importFile: {
                required: "Please Select a File",
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).removeClass('was-validated');
        }
    });
    $(document).ready(function(){
        <?php
        if(isset($_GET['shipped']) && isset($_GET['total']) && !Session::get('notified')){ Session::put('notified', true);  ?>
        $.notify(" {{$_GET['shipped']}} orders are Shipped from {{$_GET['total']}} Orders", {
            blur: 0.2,
            delay: 0,
            verticalAlign: "top",
            animationType: "scale",
            align: "right",
            type: "success",
            icon: "check"
        });
        <?php }
        ?>
    });


    function updateSelectedCounter(){
        var cnt=0;
        $('.selectedCheck:visible').each(function () {
            if($(this).prop('checked'))
                cnt++;
        });
        $('.total_order_selected').html(cnt);
    }

    function updateSelectedCounterManifest(){
        var cnt=0;
        $('.ManifestCheck:visible').each(function () {
            if($(this).prop('checked'))
                cnt++;
        });
        $('.total_order_selected').html(cnt);
    }
    function checkProcessedOrders() {
        $.ajax({
            type : 'get',
            url : '{{route('seller.get_processed_orders')}}',
            success : function (response) {
                var info = JSON.parse(response);
                if(info.notify === 'true'){
                    $.notify(" "+info.shipped + " orders are Shipped from " + info.total + " Orders", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "bottom",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#nav-processing-tab').click();
                    countOrder();
                }
            },
            error  : function (response) {
                // do your stuff
            }
        });
    }
    function countOrder(){
        $.ajax({
            url: '{{route('seller.countOrder')}}',
            success: function (response) {
                $('#total_unprocessable_order_data').html(response.unprocessable);
                $('#total_processing_order_data').html(response.processing);
                $('#total_ready_to_ship_data').html(response.ready_to_ship);
                $('#total_manifest').html(response.manifest);
                $('#total_return_order_data').html(response.return);
                $('#total_filter_order').html(response.all_order);
            },
        });
    }
    function resetFilterForm() {
        $('.filterForm').trigger('reset');
    }

</script>
</body>
</html>
