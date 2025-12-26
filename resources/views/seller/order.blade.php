<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Orders | {{$config->title}} </title>
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
        div.gallery {
            margin: 5px;
            border: 1px solid #ccc;
            float: left;
            width: 180px;
        }

        div.gallery:hover {
            border: 1px solid #777;
        }

        div.gallery img {
            width: 100%;
            height: auto;
        }

        div.desc {
            padding: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
@php
    $orderStatus = array(
        "pending" => "Pending",
        "shipped" => "Shipped",
        "pickup_requested" => "Pickup Requested",
        "manifested" => "Manifested",
        "pickup_scheduled" => "Pickup Scheduled",
        "picked_up" => "Picked Up",
        "cancelled" => "Cancelled",
        "in_transit" => "In Transit",
        "out_for_delivery" => "Out for Delivery",
        "rto_initated" => "RTO Initiated",
        "rto_initiated" => "RTO Initiated",
        "delivered" => "Delivered",
        "rto_delivered" => "RTO Delivered",
        "rto_in_transit" => "RTO In Transit",
        "ndr" => "NDR",
        "lost" => "Lost",
        "damaged" => "Damaged"
    );
@endphp
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="data_div">
            <div class="card">
                <div class="card-body" style="min-height:500px;">
                    <h3 class="h4 mb-4">All Orders
                        <div class="float-right">
                            <!-- <a class="btn btn-primary FetchOrder btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Fetch Order"><i class="fa fa-sync"></i></a> -->
                            @if(Session()->get('MySeller')->is_international == 'y')<button type="button" class="btn btn-primary btn-sm mx-0" id="showInternationalOrder" data-placement="top" data-toggle="tooltip" data-original-title="Enable Cross Border" value="international"><i class="fa fa-plane"></i></button>@endif
                            @if(Session()->get('MySeller')->duplicate_order_number_flag == 'y')<button type="button" class="btn btn-danger btn-sm mx-0" id="btnRemoveDuplicateOrders" data-placement="top" data-toggle="tooltip" data-original-title="Remove Duplicate Orders"><i class="fa fa-recycle"></i></button>@endif
                            <button type="button" style="display:none;" class="btn btn-success btn-sm mx-0" id="showDomesticOrder" data-placement="top" data-toggle="tooltip" data-original-title="Disable Cross Border" value="domestic"><i class="fa fa-plane"></i></button>
                            <button id="AddOrderButton" type="button" class="btn btn-primary addInfoButton btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Add Order"><i class="fa fa-plus"></i></button>
                        <!-- <a href="{{route('seller.export_csv_order')}}">
                                <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                            </a> -->
                            <form action="{{route('seller.export_csv_order')}}" method="post" id="ExportOrderForm" class="d-inline">
                                @csrf
                                <input type="hidden" name="export_order_id" id="export_order_id">
                                <button type="button" id="ExportOrderButton" class="btn btn-primary btn-sm mx-0 export_order_btn" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                            </form>
                            <span data-toggle="modal" data-target="#bulkupload" id="ImportCsvButton"><button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Import CSV"><i class="fa fa-download"></i></button></span>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="shipAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Ship Order"><i class="fa fa-shipping-fast"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="editAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Bulk Update (Wt./Dim.)"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="editWarehouse" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Bulk Update Warehouse"><i class="fas fa-money-check-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm mx-0" id="cancelSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="ReassignOrder" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Re-assign Order"><i class="far fa-shipping-fast"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="ManifestSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Generate Manifest"><i class="far fa-file-invoice"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="PickupRequetedButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Generate Pickup Request"><i class="fas fa-truck-pickup"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="InvoiceSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Download Invoice"><i class="far fa-print"></i></button>
                            <button type="button" class="btn btn-info btn-sm mx-0" id="LabelSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Download Label"><i class="far fa-tag"></i></button>
                            <button type="button" class="btn btn-danger btn-sm mx-0" id="removeAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Delete Order"><i class="fa fa-trash"></i></button>
                            <button type="button" class="btn btn-info btn-sm mx-0" id="manifestDownload" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Download Manifest"><i class="fa fa-print"></i></button>

                            <form action="{{route('seller.multipleLableDownload')}}" method="post" id="MultilabelForm">
                                @csrf
                                <input type="hidden" name="multilable_id" id="multilable_id">
                            </form>

                            <form action="{{route('seller.multipleInvoiceDownload')}}" method="post" id="MultiInvoiceForm">
                                @csrf
                                <input type="hidden" name="multiinvoice_id" id="multiinvoice_id">
                            </form>
                            <form action="{{route('seller.multipleManifest')}}" method="post" id="ManifestForm">
                            @csrf
                                <input type="hidden" name="manifest_id" id="manifest_id">
                         </form>
                        </div>
                    </h3>
                    <div style="display: block;overflow-x:auto">
                        <nav class="float-left mb-0" style="display: inline-block;">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-all_orders-tab" data-toggle="tab" href="#nav-all_orders" role="tab" aria-controls="nav-all_orders" aria-selected="true"><i class="far fa-bring-forward"></i> All Orders <span class="badge  badge-pill badge-dark" id="total_filter_order">0</span> </a>
                                <a class="nav-item nav-link" id="nav-unprocessable-tab" data-toggle="tab" href="#nav-unprocessable" role="tab" aria-controls="nav-unprocessable" aria-selected="false"><i class="far fa-exclamation-circle" id="unprocessableIcon"></i> Unprocessable <span class="badge badge-pill badge-danger" id="total_unprocessable_order_data">0</span></a>
                                <a class="nav-item nav-link" id="nav-processing-tab" data-toggle="tab" href="#nav-processing" role="tab" aria-controls="nav-processing" aria-selected="false"><i class="far fa-cogs"></i> Processing <span class="badge badge-pill badge-warning" id="total_processing_order_data">0</span></a>
                                <a class="nav-item nav-link" id="nav-ready-ship-tab" data-toggle="tab" href="#nav-ready-ship" role="tab" aria-controls="nav-ready-ship" aria-selected="false"><i class="far fa-dolly"></i> Ready to Ship <span class="badge badge-pill badge-success" id="total_ready_to_ship_data">0</span></a>
                                <a class="nav-item nav-link" id="nav-manifest-tab" data-toggle="tab" href="#nav-manifest" role="tab" aria-controls="nav-manifest" aria-selected="false"><i class="far fa-file-invoice"></i> Manifest <span class="badge badge-pill badge-success" id="total_manifest">0</span></a>
                                <a class="nav-item nav-link" id="nav-return-tab" data-toggle="tab" href="#nav-return" role="tab" aria-controls="nav-contact" aria-selected="false"><i class="far fa-undo-alt" id="returnIcon"></i> Returns <span class="badge badge-pill badge-success" id="total_return_order_data">0</span></a>
                            </div>
                        </nav>
                        <span class="float-right" style="display: inline-block;">
                                <p class="mb-0 h6 f-14">Showing <span class="order_display_limit"></span> of <span id="order_count"></span></p>
                                <p class="mb-0 h6 f-14">Selected <span class="total_order_selected">0</span> out of <span class="order_display_limit"></span></p>
                            </span>
                        <input type="hidden" class="limit_order" value="{{$limit_order}}">
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-all_orders" role="tabpanel" aria-labelledby="nav-all_orders-tab">
                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="AllOrderSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="all_order">
                                <div class="row">
                                    <div class="col-md-5 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="AllOrderSearchingForm" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="filter_order" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="AllOrderSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_DateFilterModal" role="button" aria-expanded="false" aria-controls="A_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="A_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" min="{{date('Y-m-d',strtotime('-90 days'))}}" name="end_date" required max="{{date('Y-m-d')}}">
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
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Source
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="A_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Source</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="A_custom">
                                                                <label class="custom-control-label pt-1" for="A_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
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
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Store
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_StoreFilterModal" role="button" aria-expanded="false" aria-controls="A_StoreFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_StoreFilterModal" style="z-index:1;">
                                                        <label>Filter by Store Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_storeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_name">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="A_{{$c->id}}">
                                                                    <label class="custom-control-label pt-1" for="A_{{$c->id}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="filter_order" data-key="channel_name" data-modal="A_StoreFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                                            <button type="button" data-modal="A_StoreFilterModal" data-form="A_storeForm" data-id="filter_order" class="submit btn btn-primary btm-sm mt-2 ml-0 applyStoreFilter">Apply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_ChannelNameFilterModal" role="button" aria-expanded="false" aria-controls="A_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_ChannelNameFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_channelNameForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="A_custom1">
                                                                <label class="custom-control-label pt-1" for="A_custom1">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="Al_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="Al_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="filter_order" data-key="channel" data-modal="A_ChannelNameFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_ChannelNameFilterModal" data-form="A_channelNameForm" data-id="filter_order" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                <span>Order Id</span>
                                                <div class="filter justify-content-right" style="display:inline;">
                                                    <a data-toggle="collapse" href="#A_NumberFilterModal" role="button" aria-expanded="false" aria-controls="A_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_orderIdForm">
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_StatusForm">
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
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="pickup_scheduled" id="A_pickup_scheduled">
                                                                    <label class="custom-control-label pt-1" for="A_pickup_scheduled">Pickup Scheduled</label>
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
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="rto_in_transit" id="A_rto_in_transit">
                                                                    <label class="custom-control-label pt-1" for="A_rto_in_transit">RTO In Transit</label>
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
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="hold" id="A_hold">
                                                                    <label class="custom-control-label pt-1" for="A_hold">Hold</label>
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
                                            @if(Session()->get('MySeller')->shopify_tag_flag_enabled == 1)
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                <span>Tag</span>
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_TagsFilterModal1" role="button" aria-expanded="false" aria-controls="A_TagsFilterModal1"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_TagsFilterModal1" style="z-index:1px;">
                                                        <label>Filter by Tag</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_TagsForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="order_tag">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                @foreach($tags as $t)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$t->shopify_tag}}" id="A_Tags{{$t->id}}">
                                                                        <label class="custom-control-label pt-1" for="A_Tags{{$t->id}}">{{ucfirst($t->shopify_tag)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="filter_order" data-key="order_tag" data-modal="A_TagsFilterModal1" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="A_TagsFilterModal1" data-form="A_TagsForm" data-id="filter_order" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </th>
                                        <th width="15%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_AmountFilterModal" role="button" aria-expanded="false" aria-controls="A_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">

                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_value" placeholder="Max Amount">
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_PaymentTypeForm">
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_ProductFormFilter">
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
                                                <i class="mb-2 fa fa-sort skusorting"></i>&nbsp; SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#A_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="A_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="A_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_SKUFormFilter">
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
                                                                <button type="reset" data-id="filter_order" data-key="sku,single_sku,multiple_sku,match_exact_sku" data-modal="A_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_QtyFormFilter">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="all_order">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="A_searchByMinQty">Min Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="min_quantity" id="A_searchByMinQty" placeholder="Min Qty" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="A_searchByMaxQty">Max Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="max_quantity" id="A_searchByMaxQty" placeholder="Max Qty" value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button type="reset" data-id="filter_order" data-key="sku,min_quantity,max_quantity" data-modal="A_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_FilterbyQtyModal" data-form="A_QtyFormFilter" data-id="filter_order" data-min="A_searchByMinQty" data-max="A_searchByMaxQty" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                        <label for="A_searchByPAddress">Filter By Warehouse</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->id}}" id="A_pending{{$w->id}}">
                                                                    <label class="custom-control-label pt-1" for="A_pending{{$w->id}}">{{$w->warehouse_name}}</label>
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_DeliveryAddressForm">
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="A_OrderWeightForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="all_order">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Weight (In Kgs)</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_weight" placeholder="Min Weight">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_weight" placeholder="Max Weight">
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <!--<div class="form-group">-->
                                                                <!--    <label for="A_searchByAWB">Search by Courier Partner</label>-->
                                                                <!--    <input type="text" class="form-control" name="value" id="A_searchByAWB" placeholder="Courier Partner">-->
                                                                <!--</div>-->
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_AWBNumberForm">
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
                            <!-- <div class="flex justify-between flex-1 sm:hidden mt-3">
                                    <a class="previousPageButton relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">« Previous</a>
                                    <a class="nextPageButton relative inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Next »</a>
                            </div> -->
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

                        <div class="tab-pane fade" id="nav-unprocessable" role="tabpanel" aria-labelledby="nav-unprocessable-tab">
                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="unprocessableOrderSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                <div class="row">
                                    <div class="col-md-5 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="unprocessableOrderSearchingForm" data-id="unprocessable_order_data" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="unprocessable_order_data" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="unprocessableOrderSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th width="10%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_DateFilterModal" role="button" aria-expanded="false" aria-controls="U_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="U_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" min="{{date('Y-m-d',strtotime('-90 days'))}}" name="end_date" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="unprocessable_order_data" data-key="start_date,end_date" data-modal="U_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="U_DateFilterModal" data-form="U_DateFilterForm" data-id="unprocessable_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Source
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="U_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="U_custom">
                                                                <label class="custom-control-label pt-1" for="U_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="U_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="U_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="channel" data-modal="U_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_ChannelFilterModal" data-form="U_channelForm" data-id="unprocessable_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Store
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_StoreFilterModal" role="button" aria-expanded="false" aria-controls="U_StoreFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_StoreFilterModal" style="z-index:1;">
                                                        <label>Filter by Store Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_storeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_name">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="U_{{$c->id}}">
                                                                    <label class="custom-control-label pt-1" for="U_{{$c->id}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="unprocessable_order_data" data-key="channel_name" data-modal="U_StoreFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                                            <button type="button" data-modal="U_StoreFilterModal" data-form="U_storeForm" data-id="unprocessable_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyStoreFilter">Apply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_ChannelNameFilterModal" role="button" aria-expanded="false" aria-controls="U_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_ChannelNameFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_channelNameForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_code">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            @foreach($channel_name as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="U_{{$c->channel_name}}">
                                                                    <label class="custom-control-label pt-1" for="U_{{$c->channel_name}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="filter_order" data-key="channel_code" data-modal="U_ChannelNameFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_ChannelNameFilterModal" data-form="U_channelNameForm" data-id="filter_order" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_NumberFilterModal" role="button" aria-expanded="false" aria-controls="U_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_number">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="form-group">
                                                                <label for="U_searchByOrderId">Search by Order Id</label>
                                                                <input type="text" class="form-control" name="value" id="U_searchByOrderId" placeholder="Enter Order Id Here">
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="order_number" data-modal="U_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_NumberFilterModal" data-form="U_orderIdForm" data-id="unprocessable_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_StatusFilterModal" role="button" aria-expanded="false" aria-controls="U_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_StatusFilterModal" style="z-index:1;">
                                                        <label>Filter by Status</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_status">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="pending" id="U_pending">
                                                                <label class="custom-control-label pt-1" for="U_pending">Pending</label>
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="order_status" data-modal="U_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_StatusFilterModal" data-form="U_StatusForm" data-id="unprocessable_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(Session()->get('MySeller')->shopify_tag_flag_enabled == 1)
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                    <span>Tag</span>
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_TagsFilterModal1" role="button" aria-expanded="false" aria-controls="U_TagsFilterModal1"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_TagsFilterModal1" style="z-index:1px;">
                                                            <label>Filter by Tag</label>
                                                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_TagsForm">
                                                                @csrf
                                                                <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                    <input type="hidden" name="key" value="order_tag">
                                                                    <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                                    @foreach($tags as $t)
                                                                        <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                            <input type="checkbox" class="custom-control-input" name="value[]" value="{{$t->shopify_tag}}" id="U_Tags{{$t->id}}">
                                                                            <label class="custom-control-label pt-1" for="U_Tags{{$t->id}}">{{ucfirst($t->shopify_tag)}}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="order_tag" data-modal="U_TagsFilterModal1" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_TagsFilterModal1" data-form="U_TagsForm" data-id="unprocessable_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_AmountFilterModal" role="button" aria-expanded="false" aria-controls="U_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_value" placeholder="Max Amount">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="unprocessable_order_data" data-key="min_value,max_value" data-modal="U_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="U_AmountFilterModal" data-form="U_OrderAmountForm" data-id="unprocessable_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Payment
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="U_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_FilterbyPaymentModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_PaymentTypeForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <input type="hidden" name="key" value="payment_type">
                                                            <label>Filter by Payment Type</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="cod" id="U_cod">
                                                                <label class="custom-control-label pt-1" for="U_cod">Cash on
                                                                    Delivery</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="prepaid" id="U_prepaid">
                                                                <label class="custom-control-label pt-1" for="U_prepaid">Prepaid</label>
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="payment_type" data-modal="U_FilterbyPaymentModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="U_FilterbyPaymentModal" data-form="U_PaymentTypeForm" data-id="unprocessable_order_data" class="applyFilterPayment btn btn-primary mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#U_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="U_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_FilterbyProductModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_ProductFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="product">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="form-group">
                                                                <label for="U_searchByProduct">Search by Product Name</label>
                                                                <input type="text" class="form-control" name="value" id="U_searchByProduct" placeholder="Product Name">
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="product" data-modal="U_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_FilterbyProductModal" data-form="U_ProductFormFilter" data-id="unprocessable_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                <i class="mb-2 fa fa-sort unprocess-skusorting"></i>&nbsp; SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="U_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_SKUFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="sku">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="U_searchByProductSku">Search SKU</label>
                                                                        <input type="text" class="form-control" name="value" id="U_searchByProductSku" placeholder="Product SKU">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="U_searchBySingleProduct" name="single_sku" value="y">
                                                                        <label for="U_searchBySingleProduct">Single SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="U_searchByMultiProduct" name="multiple_sku" value="y">
                                                                        <label for="U_searchByMultiProduct">Multi SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="U_searchByMatchExactProduct" name="match_exact_sku" value="y">
                                                                        <label for="U_searchByMatchExactProduct">Match Exact</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="unprocessable_order_data" data-key="sku,single_sku,multiple_sku,match_exact_sku" data-modal="U_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="U_FilterbySKUModal" data-form="U_SKUFormFilter" data-id="unprocessable_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#U_FilterbyQtyModal" role="button" aria-expanded="false" aria-controls="U_FilterbyQtyModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_FilterbyQtyModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_QtyFormFilter">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="U_searchByMinQty">Min Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="min_quantity" id="U_searchByMinQty" placeholder="Min Qty" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="U_searchByMaxQty">Max Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="max_quantity" id="U_searchByMaxQty" placeholder="Max Qty" value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="sku,min_quantity,max_quantity" data-modal="U_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_FilterbyQtyModal" data-form="U_QtyFormFilter" data-id="unprocessable_order_data" data-min="U_searchByMinQty" data-max="U_searchByMaxQty" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Customer<br> Details</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Pickup<br> Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#U_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="U_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse" id="U_FilterbyPAddress" style="z-index:1; position: absolute;left: 50%;transform: translateX(-50%);background: #ffffff;padding: 15px;border: 1px solid #dee2e6;font-size: 12px;line-height: 18px;border-radius: 5px;">
                                                        <label>Filter By Warehouse</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->id}}" id="B_pending{{$w->id}}">
                                                                    <label class="custom-control-label pt-1" for="B_pending{{$w->id}}">{{$w->warehouse_name}}</label>
                                                                </div>
                                                            @endforeach
{{--                                                            <div class="form-group">--}}
{{--                                                                <label for="U_searchByPAddress">Search Pickup Address</label>--}}
{{--                                                                <input type="text" class="form-control" name="value" id=U_searchByPAddress" placeholder="Pickup Address">--}}
{{--                                                            </div>--}}
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="pickup_address" data-modal="U_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_FilterbyPAddress" data-form="U_PickupAddressForm" data-id="unprocessable_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#U_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="U_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_FilterbyDAddress" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="U_DeliveryAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="delivery_address">
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="form-group">
                                                                <label for="U_searchByDAddress">Search Delivery
                                                                    Address</label>
                                                                <input type="text" class="form-control" name="value" id="U_searchByDAddress" placeholder="Delivery Address">
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="delivery_address" data-modal="U_FilterbyDAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="U_FilterbyDAddress" data-form="U_DeliveryAddressForm" data-id="unprocessable_order_data" class="applyFilterDeliveryAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#U_WeightFilterModal" role="button" aria-expanded="false" aria-controls="U_WeightFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="U_WeightFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="U_OrderWeightForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Weight (In Kgs)</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_weight" placeholder="Min Weight">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_weight" placeholder="Max Weight">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="reset" data-id="unprocessable_order_data" data-key="min_weight,max_weight" data-modal="U_WeightFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="U_WeightFilterModal" data-form="U_OrderWeightForm" data-id="unprocessable_order_data" class="applyFilterWeight btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <!--<div class="form-group">-->
                                                                <!--    <label for="A_searchByAWB">Search by Courier Partner</label>-->
                                                                <!--    <input type="text" class="form-control" name="value" id="A_searchByAWB" placeholder="Courier Partner">-->
                                                                <!--</div>-->
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_AWBNumberForm">
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
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="unprocessable_order_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
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

                        <div class="tab-pane fade" id="nav-processing" role="tabpanel" aria-labelledby="nav-processing-tab">
                        <!-- <form action="{{route('seller.processing_searching')}}" method="post" id="ProcessingSearchingForm">
                                        @csrf
                            <div class="row">
                                <div class="col-md-5 mt-2 pr-1">
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control" style="height:38px;" name="order" id="processing_searching" placeholder="Search Orders (Provide comma separated IDs)">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-2 pl-0">
                                    <div class="form-group mb-2">
                                        <button type="button" class="btn btn-primary mx-0 processingSearchingBtn" data-form="ProcessingSearchingForm" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                        <button type="reset" class="btn btn-primary mx-0 processingResetBtn" data-form="ProcessingSearchingForm" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </form> -->

                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="ProccessingSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="processing_order_data">
                                <div class="row">
                                    <div class="col-md-5 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="ProccessingSearchingForm" data-id="processing_order_data" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="processing_order_data" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="ProccessingSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th width="10%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_DateFilterModal" role="button" aria-expanded="false" aria-controls="P_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="P_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" min="{{date('Y-m-d',strtotime('-90 days'))}}" name="end_date" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="processing_order_data" data-key="start_date,end_date" data-modal="P_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="P_DateFilterModal" data-form="P_DateFilterForm" data-id="processing_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Source
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="P_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="P_custom">
                                                                <label class="custom-control-label pt-1" for="P_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="P_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="P_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="processing_order_data" data-key="channel" data-modal="P_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_ChannelFilterModal" data-form="P_channelForm" data-id="processing_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Store
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_StoreFilterModal" role="button" aria-expanded="false" aria-controls="P_StoreFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_StoreFilterModal" style="z-index:1;">
                                                        <label>Filter by Store Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_storeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_name">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="P_{{$c->id}}">
                                                                    <label class="custom-control-label pt-1" for="P_{{$c->id}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="processing_order_data" data-key="channel_name" data-modal="A_StoreFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                                            <button type="button" data-modal="P_StoreFilterModal" data-form="P_storeForm" data-id="processing_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyStoreFilter">Apply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_ChannelNameFilterModal" role="button" aria-expanded="false" aria-controls="P_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_ChannelNameFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_channelNameForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_code">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            @foreach($channel_name as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="P_{{$c->channel_name}}">
                                                                    <label class="custom-control-label pt-1" for="P_{{$c->channel_name}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="processing_order_data" data-key="channel_code" data-modal="P_ChannelNameFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_ChannelNameFilterModal" data-form="P_channelNameForm" data-id="processing_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_NumberFilterModal" role="button" aria-expanded="false" aria-controls="P_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_number">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="form-group">
                                                                <label for="P_searchByOrderId">Search by Order Id</label>
                                                                <input type="text" class="form-control" name="value" id="P_searchByOrderId" placeholder="Enter Order Id Here">
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="order_number" data-modal="P_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_NumberFilterModal" data-form="P_orderIdForm" data-id="processing_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_StatusFilterModal" role="button" aria-expanded="false" aria-controls="P_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_StatusFilterModal" style="z-index:1;">
                                                        <label>Filter by Status</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_status">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="pending" id="P_pending">
                                                                <label class="custom-control-label pt-1" for="P_pending">Pending</label>
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="order_status" data-modal="P_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_StatusFilterModal" data-form="P_StatusForm" data-id="processing_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(Session()->get('MySeller')->shopify_tag_flag_enabled == 1)
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                    <span>Tag</span>
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_TagsFilterModal1" role="button" aria-expanded="false" aria-controls="P_TagsFilterModal1"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_TagsFilterModal1" style="z-index:1px;">
                                                            <label>Filter by Tag</label>
                                                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_TagsForm">
                                                                @csrf
                                                                <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                    <input type="hidden" name="key" value="order_tag">
                                                                    <input type="hidden" name="filter_status" value="processing_order_data">
                                                                    @foreach($tags as $t)
                                                                        <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                            <input type="checkbox" class="custom-control-input" name="value[]" value="{{$t->shopify_tag}}" id="P_Tags{{$t->id}}">
                                                                            <label class="custom-control-label pt-1" for="P_Tags{{$t->id}}">{{ucfirst($t->shopify_tag)}}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button type="reset" data-id="processing_order_data" data-key="order_tag" data-modal="P_TagsFilterModal1" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_TagsFilterModal1" data-form="P_TagsForm" data-id="processing_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_AmountFilterModal" role="button" aria-expanded="false" aria-controls="P_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_value" placeholder="Max Amount">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="processing_order_data" data-key="min_value,max_value" data-modal="P_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="P_AmountFilterModal" data-form="P_OrderAmountForm" data-id="processing_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Payment
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="P_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbyPaymentModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_PaymentTypeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="payment_type">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <label>Filter by Payment Type</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="cod" id="P_cod">
                                                                <label class="custom-control-label pt-1" for="P_cod">Cash on
                                                                    Delivery</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="prepaid" id="P_prepaid">
                                                                <label class="custom-control-label pt-1" for="P_prepaid">Prepaid</label>
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="payment_type" data-modal="P_FilterbyPaymentModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="P_FilterbyPaymentModal" data-form="P_PaymentTypeForm" data-id="processing_order_data" class="applyFilterPayment btn btn-primary mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#P_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="P_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbyProductModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_ProductFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="product">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="form-group">
                                                                <label for="P_searchByProduct">Search by Product
                                                                    Name</label>
                                                                <input type="text" class="form-control" name="value" id="P_searchByProduct" placeholder="Product Name">
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="product" data-modal="P_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_FilterbyProductModal" data-form="P_ProductFormFilter" data-id="processing_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                <i class="mb-2 fa fa-sort process-skusorting"></i>&nbsp; SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="P_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_SKUFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="sku">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="P_searchByProductSku">Search SKU</label>
                                                                        <input type="text" class="form-control" name="value" id="P_searchByProductSku" placeholder="Product SKU">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="P_searchBySingleProduct" name="single_sku" value="y">
                                                                        <label for="P_searchBySingleProduct">Single SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="P_searchByMultiProduct" name="multiple_sku" value="y">
                                                                        <label for="P_searchByMultiProduct">Multi SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="P_searchByMatchExactProduct" name="match_exact_sku" value="y">
                                                                        <label for="P_searchByMatchExactProduct">Match Exact</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="processing_order_data" data-key="sku,single_sku,multiple_sku,match_exact_sku" data-modal="P_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="P_FilterbySKUModal" data-form="P_SKUFormFilter" data-id="processing_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#P_FilterbyQtyModal" role="button" aria-expanded="false" aria-controls="P_FilterbyQtyModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbyQtyModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_QtyFormFilter">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="processing_order_data">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="P_searchByMinQty">Min Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="min_quantity" id="P_searchByMinQty" placeholder="Min Qty" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="P_searchByMaxQty">Max Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="max_quantity" id="P_searchByMaxQty" placeholder="Max Qty" value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button type="reset" data-id="processing_order_data" data-key="sku,min_quantity,max_quantity" data-modal="P_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_FilterbyQtyModal" data-form="P_QtyFormFilter" data-id="processing_order_data" data-min="P_searchByMinQty" data-max="P_searchByMaxQty" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Customer<br> Details</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Pickup<br>Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="P_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbyPAddress" style="z-index:1; position: absolute;left: 50%;transform: translateX(-50%);background: #ffffff;padding: 15px;border: 1px solid #dee2e6;font-size: 12px;line-height: 18px;border-radius: 5px;">
                                                        <label>Filter By Warehouse</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->id}}" id="P_pending{{$w->id}}">
                                                                    <label class="custom-control-label pt-1" for="P_pending{{$w->id}}">{{$w->warehouse_name}}</label>
                                                                </div>
                                                            @endforeach
{{--                                                            <div class="form-group">--}}
{{--                                                                <label for="P_searchByPAddress">Search Pickup Address</label>--}}
{{--                                                                <input type="text" class="form-control" name="value" id=P_searchByPAddress" placeholder="Pickup Address">--}}
{{--                                                            </div>--}}
                                                            <button type="reset" data-id="processing_order_data" data-key="pickup_address" data-modal="P_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_FilterbyPAddress" data-form="P_PickupAddressForm" data-id="processing_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Delivery<br> Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#P_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="P_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_FilterbyDAddress" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="P_DeliveryAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="delivery_address">
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="form-group">
                                                                <label for="P_searchByDAddress">Search Delivery
                                                                    Address</label>
                                                                <input type="text" class="form-control" name="value" id="P_searchByDAddress" placeholder="Delivery Address">
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="delivery_address" data-modal="P_FilterbyDAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="P_FilterbyDAddress" data-form="P_DeliveryAddressForm" data-id="processing_order_data" class="applyFilterDeliveryAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#P_WeightFilterModal" role="button" aria-expanded="false" aria-controls="P_WeightFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="P_WeightFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="P_OrderWeightForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="processing_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Weight (In Kgs)</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_weight" placeholder="Min Weight">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_weight" placeholder="Max Weight">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="reset" data-id="processing_order_data" data-key="min_weight,max_weight" data-modal="P_WeightFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="P_WeightFilterModal" data-form="P_OrderWeightForm" data-id="processing_order_data" class="applyFilterWeight btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <!--<div class="form-group">-->
                                                                <!--    <label for="A_searchByAWB">Search by Courier Partner</label>-->
                                                                <!--    <input type="text" class="form-control" name="value" id="A_searchByAWB" placeholder="Courier Partner">-->
                                                                <!--</div>-->
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
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="A_AWBNumberForm">
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
                                        </th>                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="processing_order_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
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

                        <div class="tab-pane fade" id="nav-ready-ship" role="tabpanel" aria-labelledby="nav-ready-tab">
                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="readyToShipOrderSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                <div class="row">
                                    <div class="col-md-5 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="readyToShipOrderSearchingForm" data-id="ready_to_ship_data" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="ready_to_ship_data" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="readyToShipOrderSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkManifestButton" value="y"></th>
                                        <th width="10%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Shipped Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_DateFilterModal" role="button" aria-expanded="false" aria-controls="R_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="R_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" max="{{date('Y-m-d')}}" min="{{date('Y-m-d',strtotime('-90 days'))}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" name="end_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="ready_to_ship_data" data-key="start_date,end_date" data-modal="R_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="R_DateFilterModal" data-form="R_DateFilterForm" data-id="ready_to_ship_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Source
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="R_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="R_custom">
                                                                <label class="custom-control-label pt-1" for="R_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="R_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="R_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="channel" data-modal="R_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_ChannelFilterModal" data-form="R_channelForm" data-id="ready_to_ship_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Store
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_StoreFilterModal" role="button" aria-expanded="false" aria-controls="R_StoreFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_StoreFilterModal" style="z-index:1;">
                                                        <label>Filter by Store Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_storeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_name">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="R_{{$c->id}}">
                                                                    <label class="custom-control-label pt-1" for="R_{{$c->id}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="ready_to_ship_data" data-key="channel_name" data-modal="R_StoreFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                                            <button type="button" data-modal="R_StoreFilterModal" data-form="R_storeForm" data-id="ready_to_ship_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyStoreFilter">Apply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_ChannelNameFilterModal" role="button" aria-expanded="false" aria-controls="R_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_ChannelNameFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_channelNameForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_code">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            @foreach($channel_name as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="R_{{$c->channel_name}}">
                                                                    <label class="custom-control-label pt-1" for="R_{{$c->channel_name}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="channel_code" data-modal="R_ChannelNameFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_ChannelNameFilterModal" data-form="R_channelNameForm" data-id="ready_to_ship_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_NumberFilterModal" role="button" aria-expanded="false" aria-controls="R_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_number">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="form-group">
                                                                <label for="R_searchByOrderId">Search by Order Id</label>
                                                                <input type="text" class="form-control" name="value" id="R_searchByOrderId" placeholder="Enter Order Id Here">
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="order_number" data-modal="R_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_NumberFilterModal" data-form="R_orderIdForm" data-id="ready_to_ship_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_StatusFilterModal" role="button" aria-expanded="false" aria-controls="R_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_StatusFilterModal" style="z-index:1;">
                                                        <label>Filter by Status</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_status">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">

                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="shipped" id="R_shipped">
                                                                <label class="custom-control-label pt-1" for="R_shipped">Shipped</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="pickup_requested" id="R_pickup_requested">
                                                                <label class="custom-control-label pt-1" for="R_pickup_requested">Pickup Requested</label>
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="order_status" data-modal="R_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_StatusFilterModal" data-form="R_StatusForm" data-id="ready_to_ship_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_AmountFilterModal" role="button" aria-expanded="false" aria-controls="R_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_value" placeholder="Max Amount">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="ready_to_ship_data" data-key="min_value,max_value" data-modal="R_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="R_AmountFilterModal" data-form="R_OrderAmountForm" data-id="ready_to_ship_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Payment
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="R_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_FilterbyPaymentModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="R_PaymentTypeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="payment_type">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <label>Filter by Payment Type</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="cod" id="R_cod">
                                                                <label class="custom-control-label pt-1" for="R_cod">Cash on
                                                                    Delivery</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="prepaid" id="R_prepaid">
                                                                <label class="custom-control-label pt-1" for="R_prepaid">Prepaid</label>
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="payment_type" data-modal="R_FilterbyPaymentModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="R_FilterbyPaymentModal" data-form="R_PaymentTypeForm" data-id="ready_to_ship_data" class="applyFilterPayment btn btn-primary mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#R_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="R_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_FilterbyProductModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_ProductFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="product">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="form-group">
                                                                <label for="R_searchByProduct">Search by Product
                                                                    Name</label>
                                                                <input type="text" class="form-control" name="value" id="R_searchByProduct" placeholder="Product Name">
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="product" data-modal="R_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_FilterbyProductModal" data-form="R_ProductFormFilter" data-id="ready_to_ship_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                <i class="mb-2 fa fa-sort ready-to-ship-skusorting"></i>&nbsp; SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="P_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_SKUFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="sku">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="R_searchByProductSku">Search SKU</label>
                                                                        <input type="text" class="form-control" name="value" id="R_searchByProductSku" placeholder="Product SKU">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="R_searchBySingleProduct" name="single_sku" value="y">
                                                                        <label for="R_searchBySingleProduct">Single SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="R_searchByMultiProduct" name="multiple_sku" value="y">
                                                                        <label for="R_searchByMultiProduct">Multi SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="R_searchByMatchExactProduct" name="match_exact_sku" value="y">
                                                                        <label for="R_searchByMatchExactProduct">Match Exact</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="ready_to_ship_data" data-key="sku,single_sku,multiple_sku,match_exact_sku" data-modal="R_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="R_FilterbySKUModal" data-form="R_SKUFormFilter" data-id="ready_to_ship_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#R_FilterbyQtyModal" role="button" aria-expanded="false" aria-controls="R_FilterbyQtyModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_FilterbyQtyModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_QtyFormFilter">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="R_searchByMinQty">Min Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="min_quantity" id="R_searchByMinQty" placeholder="Min Qty" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="R_searchByMaxQty">Max Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="max_quantity" id="R_searchByMaxQty" placeholder="Max Qty" value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="sku,min_quantity,max_quantity" data-modal="R_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_FilterbyQtyModal" data-form="R_QtyFormFilter" data-id="ready_to_ship_data" data-min="R_searchByMinQty" data-max="R_searchByMaxQty" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Customer<br> Details</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Pickup<br> Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="R_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse" id="R_FilterbyPAddress" style="z-index:1; position: absolute;left: 50%;transform: translateX(-50%);background: #ffffff;padding: 15px;border: 1px solid #dee2e6;font-size: 12px;line-height: 18px;border-radius: 5px;">
                                                        <label>Filter By Warehouse</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->id}}" id="R_pending{{$w->id}}">
                                                                    <label class="custom-control-label pt-1" for="R_pending{{$w->id}}">{{$w->warehouse_name}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="pickup_address" data-modal="R_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_FilterbyPAddress" data-form="R_PickupAddressForm" data-id="ready_to_ship_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Delivery<br> Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="R_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_FilterbyDAddress" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_DeliveryAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="delivery_address">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="form-group">
                                                                <label for="R_searchByDAddress">Search Delivery
                                                                    Address</label>
                                                                <input type="text" class="form-control" name="value" id="R_searchByDAddress" placeholder="Delivery Address">
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="delivery_address" data-modal="R_FilterbyDAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_FilterbyDAddress" data-form="R_DeliveryAddressForm" data-id="ready_to_ship_data" class="applyFilterDeliveryAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    <!-- <th>
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Shipping <br>Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#R_AWBNumberFilterModal" role="button" aria-expanded="false" aria-controls="R_AWBNumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_AWBNumberFilterModal" style="width: 230px !important; z-index:1;">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_AWBNumberForm">
                                                                @csrf
                                        <input type="hidden" name="key" value="awb_number">
                                        <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                        <div class="form-group">
                                            <label for="R_searchByAWB">Search by AWB Number</label>
                                            <input type="text" class="form-control" name="value" id="R_searchByAWB" placeholder="AWB Number">
                                        </div>
                                        <button type="reset" data-id="ready_to_ship_data" data-key="awb_number" data-modal="R_AWBNumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                        </button>
                                        <button type="button" data-modal="R_AWBNumberFilterModal" data-form="R_AWBNumberForm" data-id="ready_to_ship_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </th> -->
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Courier Partner
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_CourierFilterModal" role="button" aria-expanded="false" aria-controls="R_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                        <label>Filter by Courier Partner</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <!--<div class="form-group">-->
                                                                <!--    <label for="R_searchByAWB">Search by Courier Partner</label>-->
                                                                <!--    <input type="text" class="form-control" name="value" id="R_searchByAWB" placeholder="Courier Partner">-->
                                                                <!--</div>-->
                                                                @foreach($partners as $p)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="R_c_{{$p->keyword}}">
                                                                        <label class="custom-control-label pt-1" for="R_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="courier_partner" data-modal="R_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_CourierFilterModal" data-form="R_CourierForm" data-id="ready_to_ship_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                AWB Number
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#R_AWBNumberFilterModal" role="button" aria-expanded="false" aria-controls="R_AWBNumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="R_AWBNumberFilterModal" style="width: 230px !important; z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="R_AWBNumberForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="awb_number">
                                                            <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                            <div class="form-group">
                                                                <label for="R_searchByAWB">Search by AWB Number</label>
                                                                <input type="text" class="form-control" name="value" id="R_searchByAWB" placeholder="AWB Number">
                                                            </div>
                                                            <button type="reset" data-id="ready_to_ship_data" data-key="awb_number" data-modal="R_AWBNumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="R_AWBNumberFilterModal" data-form="R_AWBNumberForm" data-id="ready_to_ship_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="ready_to_ship_data">
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
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

                        <div class="tab-pane fade" id="nav-manifest" role="tabpanel" aria-labelledby="nav-manifest-tab">
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th>Manifest Id</th>
                                        <th>Created
                                            <div class="filter">
                                                <a data-toggle="collapse" href="#M_DateFilterModal" role="button" aria-expanded="false" aria-controls="M_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                <div class="collapse filter-collapse ml-5" id="M_DateFilterModal">
                                                    <label>Filter by Date</label>
                                                    <form action="{{route('seller.set_filter')}}" method="post" id="M_DateFilterForm">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="manifest_order">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Start Date</label>
                                                                    <input type="date" class="form-control" name="start_date" placeholder="Min Amount">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>End Date</label>
                                                                    <input type="date" class="form-control" name="end_date" placeholder="Max Amount">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button type="reset" data-key="start_date,end_date" class="btn btn-primary btm-sm mt-2 ml-0 reset_value" data-id="manifest_order_data" data-modal="M_DateFilterModal">
                                                            Reset
                                                        </button>
                                                        <button type="button" data-modal="M_DateFilterModal" data-form="M_DateFilterForm" data-id="manifest_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Created By</th>
                                        <th>Courier</th>
                                        <th>Number of Order</th>
                                        <th width="10%">Pickup Reference Number</th>
                                        <th>Status</th>
                                        <th>Download</th>
                                    </tr>
                                    </thead>
                                    <tbody id="manifest_order_data">

                                    </tbody>
                                </table>
                            </div>
                            {{--                            <div id="">--}}
                            {{--                            </div>--}}
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
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
                        <div class="tab-pane fade" id="nav-return" role="tabpanel" aria-labelledby="nav-return-tab">
                            <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="returnOrderSearchingForm">
                                @csrf
                                <input type="hidden" name="filter_status" value="return_order_data">
                                <div class="row">
                                    <div class="col-md-5 mt-2 pr-1">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" style="height:38px;" name="order_awb_search" id="order_awb_search" placeholder="Search Orders (Provide comma separated IDs/AWBs)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2 pl-0">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-primary mx-0 applyFilterOrderSearch" data-form="returnOrderSearchingForm" data-id="return_order_data" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                            <button type="reset" data-id="return_order_data" data-key="order_awb_search" class="btn btn-primary mx-0 reset_value" data-form="returnOrderSearchingForm" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Reset">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th width="10%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Date
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_DateFilterModal" role="button" aria-expanded="false" aria-controls="RE_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="RE_DateFilterModal">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_DateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" min="{{date('Y-m-d',strtotime('-90 days'))}}" name="end_date" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="return_order_data" data-key="start_date,end_date" data-modal="RE_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_DateFilterModal" data-form="RE_DateFilterForm" data-id="return_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Source
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="RE_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_ChannelFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_channelForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <input type="hidden" name="key" value="channel">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="RE_custom">
                                                                <label class="custom-control-label pt-1" for="RE_custom">Custom</label>
                                                            </div>
                                                            @foreach($channel->unique('channel') as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="RE_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="RE_{{$c->channel}}">{{\Illuminate\Support\Str::ucfirst($c->channel)}}</label>
                                                                </div>
                                                            @endforeach
                                                            <button type="reset" data-id="return_order_data" data-key="channel" data-modal="RE_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_ChannelFilterModal" data-form="RE_channelForm" data-id="return_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Store
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_StoreFilterModal" role="button" aria-expanded="false" aria-controls="RE_StoreFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_StoreFilterModal" style="z-index:1;">
                                                        <label>Filter by Store Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_storeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_name">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="RE_{{$c->id}}">
                                                                    <label class="custom-control-label pt-1" for="RE_{{$c->id}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="return_order_data" data-key="channel_name" data-modal="RE_StoreFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                                            <button type="button" data-modal="RE_StoreFilterModal" data-form="RE_storeForm" data-id="return_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyStoreFilter">Apply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                Channel
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_ChannelNameFilterModal" role="button" aria-expanded="false" aria-controls="RE_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_ChannelNameFilterModal" style="z-index:1;">
                                                        <label>Filter by Channel Name</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_channelNameForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="channel_code">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            @foreach($channel_name as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel_name}}" id="RE_{{$c->channel_name}}">
                                                                    <label class="custom-control-label pt-1" for="RE_{{$c->channel_name}}">{{\Illuminate\Support\Str::ucfirst($c->channel_name)}}</label>
                                                                </div>
                                                            @endforeach

                                                            <button type="reset" data-id="return_order_data" data-key="channel_code" data-modal="RE_ChannelNameFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_ChannelNameFilterModal" data-form="RE_channelNameForm" data-id="return_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_NumberFilterModal" role="button" aria-expanded="false" aria-controls="RE_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <input type="hidden" name="key" value="order_number">
                                                            <div class="form-group">
                                                                <label for="RE_searchByOrderId">Search by Order Id</label>
                                                                <input type="text" class="form-control" name="value" id="RE_searchByOrderId" placeholder="Enter Order Id Here">
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="order_number" data-modal="RE_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_NumberFilterModal" data-form="RE_orderIdForm" data-id="return_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_StatusFilterModal" role="button" aria-expanded="false" aria-controls="RE_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_StatusFilterModal" style="z-index:1;">
                                                        <label>Filter by Status</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="order_status">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="rto_initiated" id="RE_rto_initiated">
                                                                <label class="custom-control-label pt-1" for="RE_rto_initiated">RTO Initiated</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="rto_in_transit" id="RE_in_transit">
                                                                <label class="custom-control-label pt-1" for="RE_in_transit">RTO In Transit</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="rto_delivered" id="RE_rto_delivered">
                                                                <label class="custom-control-label pt-1" for="RE_rto_delivered">RTO Delivered</label>
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="order_status" data-modal="RE_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_StatusFilterModal" data-form="RE_StatusForm" data-id="return_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Amount
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_AmountFilterModal" role="button" aria-expanded="false" aria-controls="RE_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_AmountFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_OrderAmountForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Amount</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_value" placeholder="Min Amount">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_value" placeholder="Max Amount">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="return_order_data" data-key="min_value,max_value" data-modal="RE_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_AmountFilterModal" data-form="RE_OrderAmountForm" data-id="return_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Payment
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="RE_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_FilterbyPaymentModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_PaymentTypeForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="payment_type">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <label>Filter by Payment Type</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="cod" id="RE_cod">
                                                                <label class="custom-control-label pt-1" for="RE_cod">Cash on
                                                                    Delivery</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="prepaid" id="RE_prepaid">
                                                                <label class="custom-control-label pt-1" for="RE_prepaid">Prepaid</label>
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="payment_type" data-modal="RE_FilterbyPaymentModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_FilterbyPaymentModal" data-form="RE_PaymentTypeForm" data-id="return_order_data" class="applyFilterPayment btn btn-primary mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#RE_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="RE_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_FilterbyProductModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_ProductFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="product">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="form-group">
                                                                <label for="RE_searchByProduct">Search by Product
                                                                    Name</label>
                                                                <input type="text" class="form-control" name="value" id="RE_searchByProduct" placeholder="Product Name ">
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="product" data-modal="RE_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_FilterbyProductModal" data-form="RE_ProductFormFilter" data-id="return_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline-flex align-items-end justify-content-left w-50 mb-1">
                                                <i class="mb-2 fa fa-sort return-skusorting"></i>&nbsp; SKU
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_FilterbySKUModal" role="button" aria-expanded="false" aria-controls="RE_FilterbySKUModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_FilterbySKUModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_SKUFormFilter">
                                                            @csrf
                                                            <input type="hidden" name="key" value="sku">
                                                            <input type="hidden" name="filter_status" value="return_order_data">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="RE_searchByProductSku">Search SKU</label>
                                                                        <input type="text" class="form-control" name="value" id="RE_searchByProductSku" placeholder="Product SKU">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="RE_searchBySingleProduct" name="single_sku" value="y">
                                                                        <label for="RE_searchBySingleProduct">Single SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="RE_searchByMultiProduct" name="multiple_sku" value="y">
                                                                        <label for="RE_searchByMultiProduct">Multi SKU</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mb-1">
                                                                        <input type="checkbox" id="RE_searchByMatchExactProduct" name="match_exact_sku" value="y">
                                                                        <label for="RE_searchByMatchExactProduct">Match ExacU</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="reset" data-id="return_order_data" data-key="sku,single_sku,multiple_sku,match_exact_sku" data-modal="RE_FilterbySKUModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                    </button>
                                                                    <button type="button" data-modal="RE_FilterbySKUModal" data-form="RE_SKUFormFilter" data-id="return_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#RE_FilterbyQtyModal" role="button" aria-expanded="false" aria-controls="RE_FilterbyQtyModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_FilterbyQtyModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_QtyFormFilter">
                                                        @csrf
                                                        <input type="hidden" name="filter_status" value="return_order_data">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="RE_searchByMinQty">Min Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="min_quantity" id="RE_searchByMinQty" placeholder="Min Qty" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="RE_searchByMaxQty">Max Quantity</label>
                                                                    <input type="number" class="form-control numberonly" name="max_quantity" id="RE_searchByMaxQty" placeholder="Max Qty" value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button type="reset" data-id="return_order_data" data-key="sku,min_quantity,max_quantity" data-modal="RE_FilterbyQtyModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_FilterbyQtyModal" data-form="RE_QtyFormFilter" data-id="return_order_data" data-min="RE_searchByMinQty" data-max="RE_searchByMaxQty" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">Customer<br> Details</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Pickup<br>Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="RE_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse" id="RE_FilterbyPAddress" style="z-index:1; position: absolute;left: 50%;transform: translateX(-50%);background: #ffffff;padding: 15px;border: 1px solid #dee2e6;font-size: 12px;line-height: 18px;border-radius: 5px;">
                                                        <label>Filter By Warehouse</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_PickupAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="pickup_address">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            @foreach($wareHouse as $w)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="{{$w->id}}" id="RE_pending{{$w->id}}">
                                                                    <label class="custom-control-label pt-1" for="RE_pending{{$w->id}}">{{$w->warehouse_name}}</label>
                                                                </div>
                                                            @endforeach
{{--                                                            <div class="form-group">--}}
{{--                                                                <label for="RE_searchByPAddress">Search Pickup Address</label>--}}
{{--                                                                <input type="text" class="form-control" name="value" id=RE_searchByPAddress" placeholder="Pickup Address">--}}
{{--                                                            </div>--}}
                                                            <button type="reset" data-id="return_order_data" data-key="pickup_address" data-modal="RE_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_FilterbyPAddress" data-form="RE_PickupAddressForm" data-id="return_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="8%">
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Delivery<br> Address
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_FilterbyDAddress" role="butt
on" aria-expanded="false" aria-controls="RE_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_FilterbyDAddress" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_DeliveryAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="delivery_address">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="form-group">
                                                                <label for="RE_searchByDAddress">Search Delivery
                                                                    Address</label>
                                                                <input type="text" class="form-control" name="value" id="RE_searchByDAddress" placeholder="Delivery Address">
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="delivery_address" data-modal="RE_FilterbyDAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_FilterbyDAddress" data-form="RE_DeliveryAddressForm" data-id="return_order_data" class="applyFilterDeliveryAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#RE_WeightFilterModal" role="button" aria-expanded="false" aria-controls="RE_WeightFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_WeightFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm" method="post" id="RE_OrderWeightForm">
                                                            @csrf
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="row">
                                                                <h6 class="pl-3">Search by Order Weight (In Kgs)</h6>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="min_weight" placeholder="Min Weight">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control numberonly" name="max_weight" placeholder="Max Weight">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="min_weight,max_weight" data-modal="RE_WeightFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_WeightFilterModal" data-form="RE_OrderWeightForm" data-id="return_order_data" class="applyFilterWeight btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                    <a data-toggle="collapse" href="#RE_CourierFilterModal" role="button" aria-expanded="false" aria-controls="RE_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                        <label>Filter by Courier Partner</label>
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <!--<div class="form-group">-->
                                                                <!-- <label for="A_searchByAWB">Search by Courier Partner</label>-->
                                                                <!-- <input type="text" class="form-control" name="value" id="A_searchByAWB" placeholder="Courier Partner">-->
                                                                <!--</div>-->
                                                                @foreach($partners as $p)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="RE_c_{{$p->keyword}}">
                                                                        <label class="custom-control-label pt-1" for="RE_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="courier_partner" data-modal="RE_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_CourierFilterModal" data-form="RE_CourierForm" data-id="return_order_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                AWB Number
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#RE_AWBNumberFilterModal" role="button" aria-expanded="false" aria-controls="RE_AWBNumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="RE_AWBNumberFilterModal" style="width: 230px !important; z-index:1;">
                                                        <form action="{{route('seller.set_filter')}}" class="filterForm mt-0" method="post" id="RE_AWBNumberForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="awb_number">
                                                            <input type="hidden" name="filter_status" value="return_order_data">
                                                            <div class="form-group">
                                                                <label for="RE_searchByAWB">Search by AWB Number</label>
                                                                <input type="text" class="form-control" name="value" id="RE_searchByAWB" placeholder="AWB Number">
                                                            </div>
                                                            <button type="reset" data-id="return_order_data" data-key="awb_number" data-modal="RE_AWBNumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="RE_AWBNumberFilterModal" data-form="RE_AWBNumberForm" data-id="return_order_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>

                                        <!--<th>Action</th>-->
                                    </tr>
                                    </thead>
                                    <tbody id="return_order_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" disabled style="width: 4%; text-align:center"></a>
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
            <form id="order_form" method="post" action="{{route('seller.add_order')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" id="oid">
                <div class="card mb-1">
                    <div class="card-header">
                        <h4>Add New <?= ($_GET['tab'] ?? "") == "add_reverse_order" ? "Reverse" : "" ?> Order
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Customer Order Number</label>
                                    <input type="text" class="form-control" placeholder="Customer Order Number" id="customer_order_number" name="customer_order_number" maxlength="30" required>
                                </div>
                            </div>
                            <div class="col-md-2" style="display:none;">
                                <div class="form-group">
                                    <label>Order Id</label>
                                    <input type="text" class="form-control" placeholder="Order Id" id="order_number" name="order_number" value="{{'1000' + $total_order+1}}" required readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                                    <input type="radio" name="o_type" class="o_type" value="forward" checked id="o_type_forward"> Forward
                                    <input type="radio" name="o_type" value="reverse" class="ml-3 o_type" id="o_type_reverse"> Reverse
                                </div>
                            </div>
                            <div class="col-md-1" style="display: {{Session()->get('MySeller')->is_international == 'y'  ? 'block' : 'none'}}">
                                <div class="form-group">
                                    <label class="mb-3">Cross Border</label><br>
                                    <input class="float-right" data-size="sm" data-width="80" type="checkbox" data-toggle="switchbutton" data-onstyle="success" data-onlabel="On" data-offlabel="Off" data-offstyle="primary" name="global_type" id="global_type" value="international">
                                    {{--                                    <input type="checkbox" data-toggle="switchbutton" checked data-onstyle="success" data-offstyle="danger">--}}
                                    {{--                                                                        <input type="checkbox" data-toggle="switchbutton" name="global_type" value="international" data-style="ios" id="global_type">--}}
                                    {{--                                    <input type="radio" name="global_type" value="domestic" checked id="domestic"> Domestic--}}
                                    {{--                                    <input type="radio" name="global_type" value="international" class="ml-3" id="international"> International--}}
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label class="mb-3">MPS</label><br>
                                    <input class="float-right shipment_type" data-size="sm" data-width="80" type="checkbox" data-toggle="switchbutton" data-onstyle="success" data-onlabel="On" data-offlabel="Off" data-offstyle="primary" value="mps" name="shipment_type" id="shipment_type">
                                    {{--                                    <input type="checkbox" data-toggle="switchbutton" name="shipment_type" value="mps" data-style="ios" id="shipment_type_mps">--}}
                                    {{--                                    <input type="radio" name="shipment_type" value="single" class="shipment_type" checked id="shipment_type_single"> Single--}}
                                    {{--                                    <input type="radio" name="shipment_type" value="mps" class="shipment_type ml-3" id="shipment_type_mps"> MPS--}}
                                </div>
                            </div>
                            <div class="col-md-1" id="qc_enable_div" style="display: none">
                                <div class="form-group">
                                    <label class="mb-3">QC Enable</label><br>
                                    <input class="float-right" data-size="sm" data-width="80" type="checkbox" data-toggle="switchbutton" data-onstyle="success" data-onlabel="On" data-offlabel="Off" data-offstyle="primary" name="qc_enable" id="qc_enable" value="y">
                                    {{--                                    <input type="checkbox" data-toggle="switchbutton" checked data-onstyle="success" data-offstyle="danger">--}}
                                    {{--                                                                        <input type="checkbox" data-toggle="switchbutton" name="global_type" value="international" data-style="ios" id="global_type">--}}
                                    {{--                                    <input type="radio" name="global_type" value="domestic" checked id="domestic"> Domestic--}}
                                    {{--                                    <input type="radio" name="global_type" value="international" class="ml-3" id="international"> International--}}
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label>Number of packets</label>
                                    <input type="number" class="form-control numberonly" placeholder="Number of packets" id="number_of_packets" name="number_of_packets" min="1" required>
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
                                    <input type="text" class="form-control" placeholder="Customer Name" id="customer_name" name="customer_name" maxlength="100" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Mobile Number</label>
                                    <div class="input-group ship-form-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control" id="country" name="contact_code" required>
                                                <option value="+91">IN</option>
                                                <option value="+1" class="hidecountry" style="display:none;">US</option>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control numberonly" maxlength="10" placeholder="Phone Number" id="contact" name="contact" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea type="text" class="form-control" rows="3" placeholder="Address 1" id="address" name="address" maxlength="500" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address 2 (optional)</label>
                                    <textarea type="text" class="form-control" rows="3" placeholder="Address 2" id="address2" name="address2" maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="text" class="form-control" placeholder="Pincode" id="pincode" name="pincode" required>
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
                                <table class="table table-borderless">
                                    <thead>
                                    <tr>
                                        <th style="border: none;">SKU</th>
                                        <th style="border: none;">Product</th>
                                        <th style="border: none;">Quantity</th>
                                        <th style="border: none; display: none" id="hsnColumn">HSN Number</th>
                                        <th style="border: none; display: none" id="htsColumn">HTS Number</th>
                                        <th style="border: none;">Field</th>
                                    </tr>
                                    </thead>
                                    <tbody id="single_shipment_product_details">
                                    <tr>
                                        <td><input type="text" data-id="1" id="product_sku1" name="product_sku[]" class="form-control product_requierd product_sku" placeholder="Product SKU" required/></td>
                                        <td><input type="text" data-id="1" id="product_name1" name="product_name[]" class="form-control product_requierd product_name" placeholder="Product Name" required/></td>
                                        <td><input type="text" data-id="1" id="product_qty1" name="product_qty[]" class="form-control product_requierd product_qty" value="1" placeholder="Product Quantity" maxlength="4" required/></td>
                                        <td style="display: none" class="hsnTD"><input type="text" data-id="1" id="hsn_number" name="hsn_number[]" class="form-control hsn_number" value="" placeholder="HSN Number" maxlength="30" required/></td>
                                        <td style="display: none" class="htsTD"><input type="text" data-id="1" id="hts_number" name="hts_number[]" class="form-control hts_number" value="" placeholder="HTS Number" maxlength="30" required/></td>
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
                                    <input type="text" class="form-control weightfield numberonly" placeholder="Weight (In Kg.)" id="weight" name="weight" maxlength="6" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="length">Length (cm)</label>
                                    <input type="text" class="form-control numberonly" placeholder="Length" id="length" name="length" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="breadth">Breadth (cm)</label>
                                    <input type="text" class="form-control numberonly" placeholder="Breadth" id="breadth" name="breadth" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="height">Height (cm)</label>
                                    <input type="text" class="form-control numberonly" placeholder="Height" id="height" name="height" maxlength="4" required>
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
                                    <input type="number" class="form-control numberonly" placeholder="Invoice Amount" id="invoice_amount" maxlength="7" name="invoice_amount">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Shipping Charges</label>
                                    <input type="number" class="form-control numberonly" placeholder="Shiping Charges" id="shipping_charges" maxlength="7" name="shipping_charges">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>COD charges</label>
                                    <input type="number" class="form-control numberonly" placeholder="Cod Charges" id="cod_charges" maxlength="7" name="cod_charges">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Discount</label>
                                    <input type="number" class="form-control numberonly" placeholder="Discount" id="discount" maxlength="7" name="discount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reseller Name</label>
                                    <input type="text" class="form-control" placeholder="Reseller Name" id="reseller_name" name="reseller_name" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-3" id="ewaybillDiv" style="display: none;">
                                <div class="form-group">
                                    <label>E-Way Bill Number</label>
                                    <input type="text" class="form-control" placeholder="E-Way Bill Number" id="ewaybill_number" name="ewaybill_number" value="" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-3 international" id="invoiveNumberDiv" style="display: none;">
                                <div class="form-group">
                                    <label>Invoice Reference Number</label>
                                    <input type="text" class="form-control" placeholder="Invoice Reference Number" id="invoice_reference_number" name="invoice_reference_number" value="{{Session()->get('MySeller')->gst_number}}" maxlength="30" required>
                                </div>
                            </div>
                            <div class="col-md-3 international" id="iossDiv" style="display: none;">
                                <div class="form-group">
                                    <label>IOSS</label>
                                    <input type="text" class="form-control" placeholder="IOSS" id="ioss" name="ioss" value="" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-3 international" id="eoriDiv" style="display: none;">
                                <div class="form-group">
                                    <label>EORI</label>
                                    <input type="text" class="form-control" placeholder="EROI" id="eori" name="eori" value="" maxlength="30">
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
                        <div id="same_as_rto_div">
                            <div class="ui checked checkbox">
                                <label> Same as RTO:</label>
                                <input class="float-right" data-size="sm" data-width="80" type="checkbox" data-toggle="switchbutton" data-onstyle="success" data-onlabel="Yes" data-offlabel="No" data-offstyle="primary" value="y" name="same_as_rto" id="same_as_rto" checked>
                            </div>
                        </div>
                        <div id="warehouse_rto">
                            <h6>Select RTO Warehouse</h6>
                            <div class="row" >
                                @forelse($wareHouse as $w)
                                    <div class="col-sm-6 col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="radio" id="warehouse_{{$w->id}}" name="rto_warehouse_id" data-id="{{$w->id}}" class="warehouse_select" value="{{$w->id}}" {{$w->default == 'y' ? 'checked':''}}>
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
                        </div>
                        <button class="btn btn-primary pull-right" style="display:none;" id="QCInformationButton" type="button">Next</button>
                        <button class="btn btn-primary pull-right warehouse_submit_button" id="SubmitOrderData" type="submit" {{count($wareHouse) == 0 ? 'disabled' : ''}}>Submit</button>
                        <button class="btn btn-primary pull-right" id="PreviousOtherTabButton" type="button">
                            Previous
                        </button>
                    </div>
                </div>
                <div class="card mb-1" id="qc_information_div" style="display:none">
                    <div class="card-header">
                        <h5>QC Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="qc_tab" style="display:none;">
                        <div class="row">
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Label</label>--}}
{{--                                    <input type="text" class="form-control" placeholder="Label" id="qc_label" name="qc_label" required>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Value To Check</label>--}}
{{--                                    <input type="text" class="form-control" placeholder="Value" id="value_to_check" name="value_to_check" required>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <input type="hidden" name="clone_qc_image" id="clone_qc_image">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control" placeholder="Help Description" id="help_description" name="help_description" required>
                                </div>
                            </div>
                            <div class="col-md-6" id="product_image_div">
                                <div class="form-group">
                                    <label>Product Images</label>
                                    <input type="file" class="form-control" placeholder="Product Image" id="product_image" name="product_image[]" multiple required accept="image/*">
                                    <br>
                                    <div id="qc_display_images" style="display: none">
                                        <img style="max-height:60%;max-width:15%" src="{{asset('public/assets/admin/images/20201125223903LOGO.png')}}">
                                        <img style="max-height:60%;max-width:15%" src="{{asset('public/assets/admin/images/20201125223903LOGO.png')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="single-packets">
                                <table class="table table-borderless">
                                    <thead>
                                    <tr>
                                        <th style="border: none;">Label</th>
                                        <th style="border: none;">Value To Check</th>
                                        <th style="border: none;">Field</th>
                                    </tr>
                                    </thead>
                                    <tbody id="qc_labels_body">
                                    <tr>
                                        <td><input type="text" data-id="1" id="qc_label1" name="qc_label[]" class="form-control qc_label qc_info_required" placeholder="Label" required/></td>
                                        <td><input type="text" data-id="1" id="value_to_check1" name="value_to_check[]" class="form-control value_to_check qc_info_required" placeholder="Value To Check" required/></td>
                                        <td>
                                            <button type="button" name="add" class="btn btn-info btn-sm addLabel"><i class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="SubmitOrderData" type="submit" {{count($wareHouse) == 0 ? 'disabled' : ''}}>Submit</button>
                        <button class="btn btn-primary pull-right" id="previous_warehouse_button" type="button">
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
            <form method="post" action="{{route('seller.import_csv_order')}}" enctype="multipart/form-data" id="bulkimportform">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 pb-10 mb-2">
                            <span id="domesticDownload">Download sample order upload file : <a class="text-info" href="{{url('public/assets/seller/Twinnship.csv')}}">Download</a></span>
                            <span style="display:none" id="internationalDownload">Download sample order upload file : <a class="text-info" href="{{url('public/assets/seller/Twinnship_international.csv')}}">Download</a></span>
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
                        <div class="col-sm-12">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="row p-2">
                                        <div>
                                            <input type="radio" value="insert" id="inputGroupFile03" name="importType" checked>
                                            <label for="inputGroupFile03" class="p-2">Import Only</label>
                                        </div>
                                        <div>
                                            <input type="radio" value="update" id="inputGroupFile04" name="importType">
                                            <label for="inputGroupFile04" class="p-2">Find and Update</label>
                                        </div>
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
                                <input type="number" class="form-control input-sm weightfield numberonly" placeholder="Weight" id="weight_single" min="0.1" max="999" name="weight" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="length">Length (cm)</label>
                                <input type="number" class="form-control numberonly" placeholder="Length (cm)" id="length_single" name="length" min="0.1" max="9999" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="breadth">Breadth (cm)</label>
                                <input type="number" class="form-control numberonly" placeholder="Breadth (cm)" id="breadth_single" name="breadth" min="0.1" max="9999" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="height">Height (cm)</label>
                                <input type="number" class="form-control numberonly" placeholder="Height (cm)" id="height_single" name="height" min="0.1" max="9999" required>
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
<!--Delivery Address Modal -->
<div class="modal fade" id="deliveryAddressModal" tabindex="-1" role="dialog" aria-labelledby="dimensionModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dimensionModal23">Modify Delivery Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('seller.update-delivery-address')}}" method="post" id="deliveryAddressForm">
                    <input type="hidden" name="id" id="d_id">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_name">Name</label>
                                <input type="text" class="form-control" placeholder="Weight" id="delivery_name" name="name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="weight">Contact Number</label>
                                <input type="number" class="form-control numberonly" placeholder="Contact Number" id="delivery_contact" name="contact" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_name">Address 1</label>
                                <input type="text" class="form-control" placeholder="Address 1" id="delivery_address1" name="address1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_name">Address 2</label>
                                <input type="text" class="form-control" placeholder="Address 2" id="delivery_address2" name="address2" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_name">Pincode</label>
                                <input type="text" class="form-control" placeholder="Pincode" id="delivery_pincode" name="pincode" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_city">City</label>
                                <input type="text" class="form-control" placeholder="City" id="delivery_city" name="city" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_state">State</label>
                                <input type="text" class="form-control" placeholder="State" id="delivery_state" name="state" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_country">Country</label>
                                <input type="text" class="form-control" placeholder="Country" id="delivery_country" name="country" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close
                                </button>
                                <button type="button" id="btnSubmitDeliveryForm" class="btn btn-info btn-sm">Submit</button>
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
                                <button type="button" id="multipleDimensionSubmitBtn" class="btn btn-info btn-sm">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="multipleWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="multipleWarehouseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title" id="multipleWarehouseModalTitle"><img src="{{asset('assets/1.png')}}" width="25" height="25"/> Bulk Update Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('seller.modify_multiple_warehouse')}}" method="post" id="multipleWarehouseForm">
                    <input type="text" name="order_id" id="update_order_id" hidden>
                    @csrf
                    <div class="card-body">
                        <h6>Select Warehouse</h6>
                        RTO Warehouse <input class="mt-2" type="checkbox" name="changeRTOWarehouse" value="y">
                        <br>
                        <div class="row">
                            @forelse($wareHouse as $w)
                                <div class="col-sm-6 col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="radio" id="update_warehouse_{{$w->id}}" name="warehouse_id" data-id="{{$w->id}}" class="update_warehouse_id" value="{{$w->id}}" {{$w->default == 'y' ? 'checked':''}}>
                                            <label for="update_warehouse_{{$w->id}}" class="h6 text-dark font-weight-bold">{{$w->warehouse_name}}</label><br>
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
                    </div>
                    <div class="modal-footer pb-0">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <button type="button" id="multipleWarehouseSubmitBtn" class="btn btn-info btn-sm" {{ count($wareHouse) == 0 ? 'disabled' : ''}}>Submit</button>
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
                        <hr>
                        <!-- <h6>Your Total Balance is : <span id="seller_balance"></span></h6>
                        <h6>Your Available Shipment Balance is : <span id="available_balance"></span></h6> -->
                        <h6 id="error_message" class="text-danger" style="display:none;">You don't have a enough balance to ship these orders. Please recharge with ₹ <span id="remaining_ship_charge"></span> to ship </h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" style="display: none;" id="rechargeButton" data-toggle="modal" data-target="#exampleModal" data-placement="top" data-original-title="Make a Recharge">Recharge Now</button>
                <button type="button" class="btn btn-primary btn-sm MultiShipButton">Proceed to Ship</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="dimensionModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dimensionModal23">Modify Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('seller.update-invoice-amount')}}" method="post" id="invoiceForm">
                    <input type="hidden" name="id" id="editInvoiceId">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice Amount</label>
                                <input type="number" class="form-control numberonly" placeholder="Invoice Amount" id="editInvoiceAmount" maxlength="7" name="invoice_amount" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close
                                </button>
                                <button type="button" id="btnSubmitAmountForm" class="btn btn-info btn-sm">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qc_info_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quality Check Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Label : </label>
                    </div>
                    <div class="col-md-6">
                        <label id="modal_qc_label"></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Value To Check : </label>
                    </div>
                    <div class="col-md-6">
                        <label id="modal_qc_value_to_check"></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Help Description : </label>
                    </div>
                    <div class="col-md-6">
                        <label id="modal_qc_help_description"></label>
                    </div>
                </div>
                <div id="modal_qc_image" style="display: none;">
                    <br>
                    <h5>Images</h5>
                    <hr>
                    <div id="imageDivModal">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('seller.pages.scripts')


<script type="text/javascript">
    let div = $('#same_as_rto').parent('div');
    var totalWeight = 0,rowCounter=1,labelCounter=1;
    $('[data-toggle="popover"]').popover();
    var pageCount=1,sel_ids=[],currentTab='all_order',isFilter = false,selectedTab='{{isset($_GET['tab'])?$_GET['tab']:"all_orders"}}',totalRecord = 0,perpageLimit = 0,isInternational = false,isInternationalData = false;
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
            url: "{{ route('seller.ajax_filter_order')}}",
            success: function (response) {
                console.log(divId);
                $('#'+divId).html(response);
                var orderCount = $('#'+divId).find('#total_ajax').val();
                if(currentTab == 'all_order')
                    $('.shipOrderButton').hide();
                if(currentTab == "processing"){
                    $('.clone_data').hide();
                    $('.reverse_data').hide();
                }
                var perPage=$('.perPageRecord').val();
                //console.log(orderCount);
                if(parseInt(orderCount) < parseInt(perPage))
                    $('.order_display_limit').html(orderCount);
                else
                {
                    // if(parseInt(perPage) > parseInt(orderCount))
                    //     $('.order_display_limit').html(orderCount);
                    // else
                    $('.order_display_limit').html(perPage);
                }
                $('#order_count').html(orderCount);
                $('.totalPage').html(Math.ceil(orderCount / $('.order_display_limit').html()));
                hideOverlay();
            }
        });
    }

    $(document).ready(function () {

        {{--$('#nav-tabContent').on('dblclick','.shopify_tags',function(){--}}
        {{--    var that  = $(this);--}}
        {{--    $.ajax({--}}
        {{--        url: '{{url('/get-shopify-tag')}}/' + that.data('id'),--}}
        {{--        success: function (response) {--}}
        {{--            if(response != null) {--}}
        {{--                var info = JSON.parse(response);--}}
        {{--                that.attr('data-content', info.shopify_tag);--}}
        {{--            }--}}
        {{--            else{--}}
        {{--                that.attr('data-content', 'Tagged');--}}
        {{--            }--}}
        {{--            showOverlay();--}}
        {{--            $.ajax({--}}
        {{--                url: '{{route('seller.set_filter')}}',--}}
        {{--                type: 'post',--}}
        {{--                data: {--}}
        {{--                    _token: '{{csrf_token()}}',--}}
        {{--                    key: 'tag_value',--}}
        {{--                    value: that.data('content')--}}
        {{--                },--}}
        {{--                success: function (response) {--}}
        {{--                    get_value_filter("filter_order");--}}
        {{--                    hideOverlay();--}}
        {{--                },--}}
        {{--                error: function (response) {--}}
        {{--                    hideOverlay();--}}
        {{--                    $.notify("Something went wrong", {--}}
        {{--                        blur: 0.2,--}}
        {{--                        delay: 0,--}}
        {{--                        verticalAlign: "top",--}}
        {{--                        animationType: "scale",--}}
        {{--                        align: "right",--}}
        {{--                        type: "danger",--}}
        {{--                        icon: "close"--}}
        {{--                    });--}}
        {{--                }--}}
        {{--            });--}}
        {{--        },--}}
        {{--        error: function (response) {--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
        $('#nav-tabContent').on('click','.shopify_tags',function(){
            var that = $(this);
            if(that.hasClass('showed')){
                that.popover('hide');
                that.removeClass('showed');
            }
            else {
                $('.shopify_tags').popover('hide');
                $('.shopify_tags').removeClass('showed');
                $.ajax({
                    url: '{{url('/get-shopify-tag')}}/' + that.data('id'),
                    success: function (response) {
                        if(response != null) {
                            var info = JSON.parse(response);
                            that.attr('data-content', info.shopify_tag);
                        }
                        else{
                            that.attr('data-content', 'Tagged');
                        }
                        that.addClass('showed');
                        that.popover('toggle');
                    },
                    error: function (response) {
                        that.popover('hide');
                    }
                });
            }
        });

        setInterval(function () {
            $('.shopify_tags').popover('hide');
            $('.shopify_tags').removeClass('showed');
        },4000);

        $('#delivery_pincode').on('input', function() {
            var pincode = $(this).val();

            pincode = pincode.replace(/\D/g, '');

            if (pincode.length > 6) {
                pincode = pincode.slice(0, 6);
            }

            $(this).val(pincode);

            if (pincode.length === 6) {
                $('#validationResult').text('Pincode is valid.');
            } else {
                $('#validationResult').text('Please enter a 6-digit pincode.');
            }
        });

        $('#nav-all_orders #example1 .skusorting').click(function(){
            showOverlay();
            var that = $(this);
            var table = $("#filter_order");
            var rows = Array.from(table.children("tr"));

            if(that.hasClass('desc')){
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return bValue.localeCompare(aValue);
                });
                that.removeClass('desc');
            }
            else{
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return aValue.localeCompare(bValue);
                });
                that.addClass('desc');
            }
            rows.forEach(function(row) {
                table.append(row); // Reattach sorted rows to the table
            });

            hideOverlay();
        });

        $('#nav-processing #example1 .process-skusorting').click(function(){
            showOverlay();
            var that = $(this);
            var table = $("#processing_order_data");
            var rows = Array.from(table.children("tr")); // Convert the HTMLCollection to an array

            if(that.hasClass('desc')){
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return bValue.localeCompare(aValue);
                });
                that.removeClass('desc');
            }

            else{
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return aValue.localeCompare(bValue);
                });
                that.addClass('desc');
            }

            rows.forEach(function(row) {
                table.append(row); // Reattach sorted rows to the table
            });
            hideOverlay();
        });

        $('#nav-ready-ship #example1 .ready-to-ship-skusorting').click(function(){
            showOverlay();
            var that = $(this);
            var table = $("#ready_to_ship_data");
            var rows = Array.from(table.children("tr")); // Convert the HTMLCollection to an array

            if(that.hasClass('desc')){
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return bValue.localeCompare(aValue);
                });
                that.removeClass('desc');
            }

            else{
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return aValue.localeCompare(bValue);
                });
                that.addClass('desc');
            }

            rows.forEach(function(row) {
                table.append(row); // Reattach sorted rows to the table
            });
            hideOverlay();
        });

        $('#nav-return #example1 .return-skusorting').click(function(){
            showOverlay();
            var that = $(this);
            var table = $("#return_order_data");
            var rows = Array.from(table.children("tr")); // Convert the HTMLCollection to an array

            if(that.hasClass('desc')){
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return bValue.localeCompare(aValue);
                });
                that.removeClass('desc');
            }

            else{
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return aValue.localeCompare(bValue);
                });
                that.addClass('desc');
            }

            rows.forEach(function(row) {
                table.append(row); // Reattach sorted rows to the table
            });
            hideOverlay();
        });

        $('#nav-unprocessable #example1 .unprocess-skusorting').click(function(){
            showOverlay();
            var that = $(this);
            var table = $("#unprocessable_order_data");
            var rows = Array.from(table.children("tr")); // Convert the HTMLCollection to an array

            if(that.hasClass('desc')){
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return bValue.localeCompare(aValue);
                });
                that.removeClass('desc');
            }

            else{
                rows.sort(function(a, b) {
                    var aValue = a.getAttribute("data-sku").toLowerCase();
                    var bValue = b.getAttribute("data-sku").toLowerCase();
                    return aValue.localeCompare(bValue);
                });
                that.addClass('desc');
            }

            rows.forEach(function(row) {
                table.append(row); // Reattach sorted rows to the table
            });

            hideOverlay();
        });

        $('#same_as_rto').change(function () {
            let div = $('#same_as_rto').parent('div');
            if(div.hasClass('btn-success')){
                $('#warehouse_rto').hide();
            }
            else{
                $('#warehouse_rto').show();
            }
        });

        @if(session('global_type') == "international")
            isInternationalData = true;
        @endif
        setupInternational();

        function setupInternational(){
            if(isInternationalData){
                $('#bulkimportform').attr('action',"{{route('seller.import_csv_order_international')}}");
                $('#internationalDownload').show();
                $('#domesticDownload').hide();
                $('#showInternationalOrder').hide();
                $('#showDomesticOrder').show();
            }
            else{
                $('#bulkimportform').attr('action',"{{route('seller.import_csv_order')}}");
                $('#domesticDownload').show();
                $('#internationalDownload').hide();
                $('#showInternationalOrder').show();
                $('#showDomesticOrder').hide();
            }
        }
        $(".nav-item").click(function(){
            $("#ExportOrderButton,#ImportCsvButton,#AddOrderButton").show();
            $('#btnRemoveDuplicateOrders').hide();
        });
        $("#nav-manifest-tab").click(function(){
            $("#ExportOrderButton,#ImportCsvButton,#AddOrderButton").hide();
        });
        $(".numberonly").keypress("input", function(evt) {
            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
            return !((ASCIICode < 48 || ASCIICode > 57) && ASCIICode !== 46);

        });
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
        $('#nav-tabContent').on('click','.qc_information', function(){
            var that = $(this);
            $('#modal_qc_image').hide();
            showOverlay();
            $.ajax({
                url: "{{url('/get-qc-information')}}/" + that.data('id'),
                success: function(response){
                    var info = JSON.parse(response);
                    $('#modal_qc_value_to_check').html(info.data.qc_value_to_check);
                    $('#modal_qc_label').html(info.data.qc_label);
                    $('#modal_qc_help_description').html(info.data.qc_help_description);
                    $('#qc_info_modal').modal("show");
                    var imageDiv = '';
                    if(info.images.length > 0) {
                        for (var i = 0; i < info.images.length; i++) {
                            imageDiv += '<div class="gallery"><a target="_blank" href="' + info.images[i] + '"><img src="' + info.images[i] + '" alt="Image" width="600" height="400"></a></div>'
                        }
                        $('#imageDivModal').html(imageDiv);
                        $('#modal_qc_image').show();
                    }
                    hideOverlay();
                },
                error: function(response){

                }
            });
        });
        $('#nav-tabContent').on('click', '.editInvoice', function () {
            var that = $(this);
            $('#editInvoiceId').val(that.data('id'));
            $('#editInvoiceAmount').val(that.data('amount'));
            $('#invoiceModal').modal('show');
        });
        //update price
        $('#btnSubmitAmountForm').click(function(){
            if(!$("#invoiceForm").valid()) {
                return ;
            }
            $('#invoiceForm').ajaxSubmit({
                success: function (response) {
                    $.notify(" Success... Amount Updated Successfully", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#invoiceModal').modal('hide');
                    let id = $('#editInvoiceId').val();
                    $('#data_div #displayInvoiceAmount_'+id).html($('#editInvoiceAmount').val());
                    $('#data_div #editInvoice_'+id).data('amount',$('#editInvoiceAmount').val());
                }
            });
        });
        $("#U_searchBySingleProduct").change(function() {
            if($(this).prop('checked')) {
                $("#U_searchByProductSku").val('');
                $("#U_searchByProductSku").attr('readonly', true);
                $("#U_searchByMatchExactProduct").prop('checked', false);
                $("#U_searchByMatchExactProduct").attr('disabled', true);
                $("#U_searchByMultiProduct").prop('checked', false);
            } else {
                $("#U_searchByProductSku").attr('readonly', false);
                $("#U_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#U_searchByMultiProduct").change(function() {
            if($(this).prop('checked')) {
                $("#U_searchByProductSku").val('');
                $("#U_searchByProductSku").attr('readonly', true);
                $("#U_searchByMatchExactProduct").prop('checked', false);
                $("#U_searchByMatchExactProduct").attr('disabled', true);
                $("#U_searchBySingleProduct").prop('checked', false);
            } else {
                $("#U_searchByProductSku").attr('readonly', false);
                $("#U_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#P_searchBySingleProduct").change(function() {
            if($(this).prop('checked')) {
                $("#P_searchByProductSku").val('');
                $("#P_searchByProductSku").attr('readonly', true);
                $("#P_searchByMatchExactProduct").prop('checked', false);
                $("#P_searchByMatchExactProduct").attr('disabled', true);
                $("#P_searchByMultiProduct").prop('checked', false);
            } else {
                $("#P_searchByProductSku").attr('readonly', false);
                $("#P_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#P_searchByMultiProduct").change(function() {
            if($(this).prop('checked')) {
                $("#P_searchByProductSku").val('');
                $("#P_searchByProductSku").attr('readonly', true);
                $("#P_searchByMatchExactProduct").prop('checked', false);
                $("#P_searchByMatchExactProduct").attr('disabled', true);
                $("#P_searchBySingleProduct").prop('checked', false);
            } else {
                $("#P_searchByProductSku").attr('readonly', false);
                $("#P_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#R_searchBySingleProduct").change(function() {
            if($(this).prop('checked')) {
                $("#R_searchByProductSku").val('');
                $("#R_searchByProductSku").attr('readonly', true);
                $("#R_searchByMatchExactProduct").prop('checked', false);
                $("#R_searchByMatchExactProduct").attr('disabled', true);
                $("#R_searchByMultiProduct").prop('checked', false);
            } else {
                $("#R_searchByProductSku").attr('readonly', false);
                $("#R_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#R_searchByMultiProduct").change(function() {
            if($(this).prop('checked')) {
                $("#R_searchByProductSku").val('');
                $("#R_searchByProductSku").attr('readonly', true);
                $("#R_searchByMatchExactProduct").prop('checked', false);
                $("#R_searchByMatchExactProduct").attr('disabled', true);
                $("#R_searchBySingleProduct").prop('checked', false);
            } else {
                $("#R_searchByProductSku").attr('readonly', false);
                $("#R_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#RE_searchBySingleProduct").change(function() {
            if($(this).prop('checked')) {
                $("#RE_searchByProductSku").val('');
                $("#RE_searchByProductSku").attr('readonly', true);
                $("#RE_searchByMatchExactProduct").prop('checked', false);
                $("#RE_searchByMatchExactProduct").attr('disabled', true);
                $("#RE_searchByMultiProduct").prop('checked', false);
            } else {
                $("#RE_searchByProductSku").attr('readonly', false);
                $("#RE_searchByMatchExactProduct").attr('disabled', false);
            }
        });
        $("#RE_searchByMultiProduct").change(function() {
            if($(this).prop('checked')) {
                $("#RE_searchByProductSku").val('');
                $("#RE_searchByProductSku").attr('readonly', true);
                $("#RE_searchByMatchExactProduct").prop('checked', false);
                $("#RE_searchByMatchExactProduct").attr('disabled', true);
                $("#RE_searchBySingleProduct").prop('checked', false);
            } else {
                $("#RE_searchByProductSku").attr('readonly', false);
                $("#RE_searchByMatchExactProduct").attr('disabled', false);
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
            let div = $(this).parent('div');
            if(div.hasClass('btn-success')) {
                $("#number_of_packets").parent().parent().show();
                // $("#mps-packets").show();
                // $("#single-packets").hide();
                // $("#single_shipment_product_details").empty();
            } else {
                $("#number_of_packets").parent().parent().hide();
            }
        });
        //for pagination page number searching
        $('#nav-tabContent').on('keyup', '#txtPageCount', function (e) {
            // $('#txtPageCount').keyup(function(e){
            if(e.keyCode == 13){
                if(parseInt($(this).val().trim()) > 0){
                    if(parseInt($(this).val().trim()) <= parseInt($('.totalPage').html()) ){
                        showOverlay();
                        pageCount = parseInt($(this).val().trim());
                        fetch_orders();
                    }
                }
            }
        });
        $('#customer_name').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#reseller_name').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#txtCountry').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#state').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#city').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#contact').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#pincode').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('.table-responsive').on('keypress', '.product_qty', function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
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
        $('#length').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#breadth').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#height').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#shipping_charges').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#cod_charges').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('#discount').keypress(function (e) {
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
        $('#showInternationalOrder').click(function(){
            var that  = $(this);
            showOverlay();
            $.ajax({
                url: '{{route('seller.set_filter')}}',
                type: 'post',
                data: {
                    _token: '{{csrf_token()}}',
                    key: 'global_type',
                    value: that.val()
                },
                success: function (response) {
                    fetch_orders();
                    countOrder();
                    $('#showDomesticOrder').show();
                    $('#showInternationalOrder').hide();
                    isInternationalData = true;
                    setupInternational();
                    hideOverlay();
                },
                error: function (response) {
                    hideOverlay();
                    $.notify("Something went wrong", {
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
        });

        $('#showDomesticOrder').click(function(){
            var that  = $(this);
            showOverlay();
            $.ajax({
                url: '{{route('seller.set_filter')}}',
                type: 'post',
                data: {
                    _token: '{{csrf_token()}}',
                    key: 'global_type',
                    value: that.val()
                },
                success: function (response) {
                    fetch_orders();
                    countOrder();
                    isInternationalData = false;
                    $('#showDomesticOrder').hide();
                    $('#showInternationalOrder').show();
                    setupInternational();
                    hideOverlay();
                },
                error: function (response) {
                    hideOverlay();
                    $.notify("Something went wrong", {
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
        });
        $('#courier_partner_select').on('click', '.ShipOrderBtn', function () {
            $(this).prop('disabled',true);
            var id = $(this).data('id');
            $('#partner_' + id).trigger('click');
            showOverlay();
            //document.singleForm.action = $("#courier_partner_select").data("action");
            $('#singleForm').ajaxSubmit({
                success : function(response){
                    hideOverlay();
                    $('#courier_partner_select').modal('hide');
                    if(response.status === 'true'){
                        $.notify(response.message, {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                        fetchCurrentTabData();
                    }else{
                        $.notify(response.message, {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
                },
                error : function(){
                    $('#courier_partner_select').modal('hide');
                    hideOverlay();
                    $.notify(" Pincode is not Serviceable", {
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
        });
        //get the file name
        $('#inputGroupFile02').on('change',function(){
            var fileName = $(this).val();
            $(this).next('.custom-file-label').html(fileName);
        })
        countOrder();
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
        $('#nav-tabContent').on('click', '.clone_data', function () {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/clone_order/"}}' + $(this).data('number'),
                success: function (response) {
                    var info = JSON.parse(response);
                    if (info.order.shipment_type == 'mps') {
                        $('#shipment_type').prop('checked', true);
                        $('#shipment_type').parent('div').removeClass('off').removeClass('btn-primary').addClass('btn-success');
                        $('#shipment_type').trigger('change');
                        $('#number_of_packets').val(info.order.number_of_packets);
                        $('#number_of_packets').trigger('change');
                    } else {
                        // $('#shipment_type_single').prop('checked', true);
                        $('#shipment_type').prop('checked', false);
                        $('#shipment_type').parent('div').addClass('off').addClass('btn-primary').removeClass('btn-success');
                    }
                    $('#customer_order_number').val(info.order.customer_order_number+"_clone");
                    $('#customer_name').val(info.order.s_customer_name);
                    $('#contact').val(info.order.s_contact);
                    $('#address').val(info.order.s_address_line1);
                    $('#address2').val(info.order.s_address_line2);
                    $('#pincode').val(info.order.s_pincode);
                    $('#txtCountry').val(info.order.s_country);
                    $('#state').val(info.order.s_state);
                    $('#ewaybill_number').val(info.order.ewaybill_number);
                    $('#city').val(info.order.s_city);
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

                    if(info.order.is_qc === 'y'){
                        $('#o_type_reverse').trigger('click');
                        $('#qc_enable').prop('checked',true);
                        $('.o_type').trigger('change');
                        $('#qc_enable').parent('div').removeClass('off').removeClass('btn-primary').addClass('btn-success');
                        $('#qc_enable').trigger('change');
                        // $('#qc_label').val(info.qc_details.qc_label);
                        // $('#value_to_check').val(info.qc_details.qc_value_to_check);
                        $('#help_description').val(info.qc_details.qc_help_description);
                        $('#product_image').removeAttr('required');
                        $('#qc_labels_body').empty();
                        if((info.qc_details.qc_help_description).length > 0){
                            var imageHtml = '';
                            if(info.images.length > 0) {
                                for (var i = 0; i < info.images.length; i++) {
                                    imageHtml += '<a target="_blank" href="'+ info.images[i] +'"><img src="' + info.images[i] + '" alt="Image" style="max-width:15%;max-height:60%;"></a>&nbsp;&nbsp;'
                                }
                                $('#qc_display_images').show();
                                $('#qc_display_images').html(imageHtml);
                            }
                        }
                        $('#clone_qc_image').val(info.qc_details.qc_image);

                        for (var i = 0; i < (info.qc_details.qc_label).split(",").length; i++) {
                            add_row_qc_label_update(i);
                        }

                        var qcLabelInfo = info.qc_details.qc_label.split(",");
                        var qcValueToCheck = info.qc_details.qc_value_to_check.split(",");

                        for (var i = 0; i < qcLabelInfo.length; i++) {
                            $('#qc_label' + [i]).val((qcLabelInfo[i] === null || qcLabelInfo[i] === undefined) ? "" : qcLabelInfo[i]);
                            $('#value_to_check' + [i]).val(qcValueToCheck[i]);
                        }
                    }

                    else{
                        $('#qc_enable_div').hide();
                        $('#qc_enable').hide();
                        $('#qc_enable').prop('checked',false);
                        $('#qc_enable').parent('div').addClass('off').addClass('btn-primary').removeClass('btn-success');
                        $('#qc_enable').trigger('change');
                    }

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
                    $('#shipping_charges').val(info.order.s_charge);
                    $('#cod_charges').val(info.order.c_charge);
                    $('#warehouse_'+info.order.warehouse_id).prop('checked', true);
                    $('#discount').val(info.order.discount);
                    $('#reseller_name').val(info.order.reseller_name);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    // $('#customer_name').val(info.order.b_customer_name);
                    // $('#contact').val(info.order.b_contact);
                    // $('#address').val(info.order.b_address_line1);
                    // $('#address2').val(info.order.b_address_line2);
                    // $('#pincode').val(info.order.b_pincode);
                    // $('#txtCountry').val(info.order.b_country);
                    // $('#state').val(info.order.b_state);
                    // $('#city').val(info.order.b_city);
                    // $('#weight').val(info.order.weight/1000);
                    // $('#height').val(info.order.height);
                    // $('#length').val(info.order.length);
                    // $('#breadth').val(info.order.breadth);
                    // if (info.order.order_type == 'cod')
                    //     $('#type_cod').prop('selected', true);
                    // else if (info.order.order_type == 'prepaid')
                    //     $('#type_prepaid').prop('selected', true);
                    // else
                    //     $('#type_reverse').prop('selected', true);
                    // if (info.order.o_type == 'forward'){
                    //     $('#o_type_forward').prop('checked', true);
                    // }else{
                    //     $('#o_type_reverse').prop('checked', true);
                    //     $('#type_cod').prop('disabled', true);
                    // }
                    // $('#product_details').html('');
                    // for (var i = 0; i < info.product.length; i++) {
                    //     add_row_update(i);
                    // }
                    // for (var i = 0; i < info.product.length; i++) {
                    //     $('#product_sku' + [i]).val(info.product[i].product_sku);
                    //     $('#product_name' + [i]).val(info.product[i].product_name);
                    //     $('#product_unitprice' + [i]).val(info.product[i].product_unitprice);
                    //     $('#product_qty' + [i]).val(info.product[i].product_qty);
                    //     $('#total_amount' + [i]).val(info.product[i].total_amount);
                    // }
                    // $('#invoice_amount').val(info.order.invoice_amount);
                    // $('#shipping_charges').val(info.order.s_charge);
                    // $('#cod_charges').val(info.order.c_charge);
                    // $('#discount').val(info.order.discount);
                    // $('#reseller_name').val(info.order.reseller_name);
                    // $('#data_div').hide();
                    // $('#form_div').fadeIn();
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
        $('#nav-tabContent').on('click', '.reverse_data', function () {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/clone_order/"}}' + $(this).data('number'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#customer_order_number').val(info.order.customer_order_number+"_reverse");
                    $('#customer_name').val(info.order.s_customer_name);
                    $('#contact').val(info.order.s_contact);
                    $('#address').val(info.order.s_address_line1);
                    $('#address2').val(info.order.s_address_line2);
                    $('#pincode').val(info.order.s_pincode);
                    $('#txtCountry').val(info.order.s_country);
                    $('#state').val(info.order.s_state);
                    $('#city').val(info.order.s_city);
                    $('#weight').val(info.order.weight/1000);
                    $('#height').val(info.order.height);
                    $('#length').val(info.order.length);
                    $('#breadth').val(info.order.breadth);
                    $('#type_cod').prop('disabled', true);
                    $('#type_prepaid').prop('selected', true);
                    $('#o_type_forward').prop('disabled', true);
                    $('#o_type_reverse').prop('checked', true);
                    if (info.order.shipment_type == 'mps') {
                        $('#shipment_type_mps').prop('checked', true);
                        $('#shipment_type_mps').trigger('change');
                        $('#number_of_packets').val(info.order.number_of_packets);
                        $('#number_of_packets').trigger('change');
                    } else {
                        $('#shipment_type_single').prop('checked', true);
                    }

                    $('#qc_enable_div').show();
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
                    // $('#product_details').html('');
                    // for (var i = 0; i < info.product.length; i++) {
                    //     add_row_update(i);
                    // }
                    // for (var i = 0; i < info.product.length; i++) {
                    //     $('#product_sku' + [i]).val(info.product[i].product_sku);
                    //     $('#product_name' + [i]).val(info.product[i].product_name);
                    //     $('#product_unitprice' + [i]).val(info.product[i].product_unitprice);
                    //     $('#product_qty' + [i]).val(info.product[i].product_qty);
                    //     $('#total_amount' + [i]).val(info.product[i].total_amount);
                    // }
                    $('#invoice_amount').val(info.order.invoice_amount);
                    $('#shipping_charges').val(info.order.s_charge);
                    $('#cod_charges').val(info.order.c_charge);
                    $('#discount').val(info.order.discount);
                    $('#reseller_name').val(info.order.reseller_name);
                    $('#reverse_ship_message').html('(Order will be Pickup from Here)');
                    $('#reverse_warehouse_message').html('(Order will be Delivered from Here)');
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
        function fetch_orders(){
            // showOverlay();
            let divId = '';
            if(currentTab === 'unprocessable')
                divId = 'unprocessable_order_data';
            else if(currentTab === 'processing')
                divId = 'processing_order_data';
            else if(currentTab === 'all_order')
                divId = 'filter_order';
            else if(currentTab === 'ready_to_ship')
                divId = 'ready_to_ship_data';
            else if(currentTab === 'manifest')
                divId = 'manifest_order_data';
            else
                divId = 'filter_order';
            if(isFilter){
                get_value_filter(divId);
            }
            else{
                switch(currentTab){
                    case 'unprocessable':
                        unprocessable_order();
                        break;
                    case 'processing':
                        processing_order();
                        break;
                    case 'ready_to_ship':
                        ready_to_ship();
                        break;
                    case 'manifest':
                        manifest_order();
                        break;
                    case 'return':
                        return_order();
                        break;
                    default:
                        all_order();
                }
            }
            $('.currentPage').val(pageCount);
        }
        $('.firstPageButton').click(function(){
            if(pageCount > 1){
                pageCount = 1 ;
                //showOverlay();
                fetch_orders();
                if($("#nav-all_orders #checkAllButton").prop('checked') == true) {
                    $("#nav-all_orders #checkAllButton").click();
                }
                if($("#nav-unprocessable #checkAllButton").prop('checked') == true) {
                    $("#nav-unprocessable #checkAllButton").click();
                }
                if($("#nav-processing #checkAllButton").prop('checked') == true) {
                    $("#nav-processing #checkAllButton").click();
                }
                if($("#nav-ready-ship #checkAllButton").prop('checked') == true) {
                    $("#nav-ready-ship #checkAllButton").click();
                }
                if($("#nav-manifest #checkAllButton").prop('checked') == true) {
                    $("#nav-manifest #checkAllButton").click();
                }
                if($("#nav-return #checkAllButton").prop('checked') == true) {
                    $("#nav-return #checkAllButton").click();
                }
                if($(".selectedCheck:checked").length > 0) {
                    $(".selectedCheck:checked").click();
                }
            }
        });
        $('.previousPageButton').click(function(){
            if(pageCount > 1){
                pageCount--;
                //showOverlay();
                fetch_orders();
                if($("#nav-all_orders #checkAllButton").prop('checked') == true) {
                    $("#nav-all_orders #checkAllButton").click();
                }
                if($("#nav-unprocessable #checkAllButton").prop('checked') == true) {
                    $("#nav-unprocessable #checkAllButton").click();
                }
                if($("#nav-processing #checkAllButton").prop('checked') == true) {
                    $("#nav-processing #checkAllButton").click();
                }
                if($("#nav-ready-ship #checkAllButton").prop('checked') == true) {
                    $("#nav-ready-ship #checkAllButton").click();
                }
                if($("#nav-manifest #checkAllButton").prop('checked') == true) {
                    $("#nav-manifest #checkAllButton").click();
                }
                if($("#nav-return #checkAllButton").prop('checked') == true) {
                    $("#nav-return #checkAllButton").click();
                }
                if($(".selectedCheck:checked").length > 0) {
                    $(".selectedCheck:checked").click();
                }
            }
        });
        $('.nextPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount++;
                //showOverlay();
                fetch_orders();
                if($("#nav-all_orders #checkAllButton").prop('checked') == true) {
                    $("#nav-all_orders #checkAllButton").click();
                }
                if($("#nav-unprocessable #checkAllButton").prop('checked') == true) {
                    $("#nav-unprocessable #checkAllButton").click();
                }
                if($("#nav-processing #checkAllButton").prop('checked') == true) {
                    $("#nav-processing #checkAllButton").click();
                }
                if($("#nav-ready-ship #checkAllButton").prop('checked') == true) {
                    $("#nav-ready-ship #checkAllButton").click();
                }
                if($("#nav-manifest #checkAllButton").prop('checked') == true) {
                    $("#nav-manifest #checkAllButton").click();
                }
                if($("#nav-return #checkAllButton").prop('checked') == true) {
                    $("#nav-return #checkAllButton").click();
                }
                if($(".selectedCheck:checked").length > 0) {
                    $(".selectedCheck:checked").click();
                }
            }
        });
        $('.lastPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount = $('.totalPage').html();
                //showOverlay();
                fetch_orders();
                if($("#nav-all_orders #checkAllButton").prop('checked') == true) {
                    $("#nav-all_orders #checkAllButton").click();
                }
                if($("#nav-unprocessable #checkAllButton").prop('checked') == true) {
                    $("#nav-unprocessable #checkAllButton").click();
                }
                if($("#nav-processing #checkAllButton").prop('checked') == true) {
                    $("#nav-processing #checkAllButton").click();
                }
                if($("#nav-ready-ship #checkAllButton").prop('checked') == true) {
                    $("#nav-ready-ship #checkAllButton").click();
                }
                if($("#nav-manifest #checkAllButton").prop('checked') == true) {
                    $("#nav-manifest #checkAllButton").click();
                }
                if($("#nav-return #checkAllButton").prop('checked') == true) {
                    $("#nav-return #checkAllButton").click();
                }
                if($(".selectedCheck:checked").length > 0) {
                    $(".selectedCheck:checked").click();
                }
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
                    fetch_orders();
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
        //get data of unprocessable order
        $('#nav-tab').on('click', '#nav-unprocessable-tab', function () {
            isFilter = false;
            pageCount=1;
            resetFilterForm();
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='unprocessable';
            showOverlay();
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#LabelSelectButton').hide();
            unprocessable_order();
        });
        function unprocessable_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.order_unprocessable')}}',
                success: function (response) {
                    $('#unprocessable_order_data').html(response);
                    var orderCount = $('#total_unproccessed').val();
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
                    $('#order_count').html($('#total_unproccessed').val());
                    $('.totalPage').html(Math.ceil($('#total_unproccessed').val() / $('.order_display_limit').html()));
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
        }
        //get data of All order
        $('#nav-tab').on('click', '#nav-all_orders-tab', function () {
            resetFilterForm();
            isFilter = false;
            pageCount=1;
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='all_order';
            showOverlay();
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#ManifestSelectButton').hide();
            $('#LabelSelectButton').hide();
            $('#InvoiceSelectButton').hide();
            $('#manifestDownload').hide();
            all_order();
        });
        function all_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.all_order')}}',
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
        // //get data of all Reverse order
        // $('#nav-tab').on('click', '#nav-all_reverse_orders-tab', function () {
        //     pageCount=1;
        //     $('.currentPage').val(pageCount);
        //     currentTab='all_reverse_order';
        //      showOverlay();
        //     sel_ids = [];
        //     $(".selectedCheck").prop('checked', false);
        //     $('#removeAllButton').hide();
        //     $('#shipAllButton').hide();
        //     $('#cancelSelectButton').hide();
        //     all_reverse_order();
        // });
        //get data of processing order
        $('#nav-tab').on('click', '#nav-processing-tab', function () {
            showOverlay();
            isFilter = false;
            resetFilterForm();
            pageCount=1;
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='processing';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#LabelSelectButton').hide();
            processing_order();
            $('#btnRemoveDuplicateOrders').show();
        });
        $('#btnRemoveDuplicateOrders').click(function () {
            if(confirm('Are you sure to remove duplicate orders??')){
                showOverlay();
                $.ajax({
                    type : 'post',
                    data : {
                        '_token' : "{{csrf_token()}}",
                        'seller_id' : '{{Session()->get('MySeller')->id}}'
                    },
                    url : '{{route('seller.remove-duplicate-orders')}}',
                    success : function (response) {
                        hideOverlay();
                        showSuccess(response.message);
                    }
                });
            }
        });
        function processing_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.order_processing')}}',
                success: function (response) {
                    $('#processing_order_data').html(response);
                    var orderCount = $('#total_process_order').val();
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
                    $('#order_count').html($('#total_process_order').val());
                    $('.totalPage').html(Math.ceil($('#total_process_order').val() / $('.order_display_limit').html()));
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
        }
        //get Data of ready to ship order
        $('#nav-tab').on('click', '#nav-ready-ship-tab', function () {
            showOverlay();
            isFilter = false;
            resetFilterForm();
            pageCount=1;
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='ready_to_ship';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#LabelSelectButton').hide();
            $('#AddOrderButton').hide();
            $('#ImportCsvButton').hide();
            ready_to_ship();
        });
        function ready_to_ship() {
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.ready_to_ship')}}',
                success: function (response) {
                    $('#ready_to_ship_data').html(response);
                    var orderCount = $('#total_shipped_order').val();
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
                    $('#order_count').html($('#total_shipped_order').val());
                    $('.totalPage').html(Math.ceil($('#total_shipped_order').val() / $('.order_display_limit').html()));
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
        }
        //get data of Manifest order
        $('#nav-tab').on('click', '#nav-manifest-tab', function () {
            showOverlay();
            resetFilterForm();
            pageCount=1;
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='manifest';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#LabelSelectButton').hide();
            manifest_order();
        });
        function manifest_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.order_manifest')}}',
                success: function (response) {
                    $('#manifest_order_data').html(response);
                    var orderCount = $('#total_manifest_order').val();
                    var perPage=$('.perPageRecord').val();
                    if(parseInt(orderCount) < parseInt(perPage))
                        $('.order_display_limit').html(orderCount);
                    else
                    {
                        if(parseInt(perPage) > parseInt(orderCount))
                            $('.order_display_limit').html(orderCount);
                        else
                            $('.order_display_limit').html(perPage);
                    }
                    $('#order_count').html($('#total_manifest_order').val());
                    $('.totalPage').html(Math.ceil($('#total_manifest_order').val() / $('.order_display_limit').html()));
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
        }
        //get data of return order
        $('#nav-tab').on('click', '#nav-return-tab', function () {
            showOverlay();
            isFilter = false;
            resetFilterForm();
            pageCount=1;
            cnt = 0;
            $('.total_order_selected').html(cnt);
            $('.currentPage').val(pageCount);
            currentTab='return';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#editAllButton').hide();
            $("#editWarehouse").hide();
            $('#cancelSelectButton').hide();
            $('#ReassignOrder').hide();
            $('#LabelSelectButton').hide();
            $('#AddOrderButton').hide();
            $('#ImportCsvButton').hide();
            return_order();
        });
        function return_order(){
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.order_return')}}',
                success: function (response) {
                    $('#return_order_data').html(response);
                    var orderCount = $('#total_return_order').val();
                    var perPage=$('.perPageRecord').val();
                    if(parseInt(orderCount) < parseInt(perPage))
                        $('.order_display_limit').html(orderCount);
                    else
                    {
                        if(parseInt(perPage) > parseInt(orderCount))
                            $('.order_display_limit').html(orderCount);
                        else
                            $('.order_display_limit').html(perPage);
                    }
                    $('#order_count').html($('#total_return_order').val());
                    $('.totalPage').html(Math.ceil($('#total_return_order').val() / $('.order_display_limit').html()));
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
        // filter code goes here  nav-tabContent
        $('#nav-tabContent').on('click', '.applyStoreFilter', function () {
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
        $('#nav-tabContent').on('click', '.applyFilterOrderSearch', function () {
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
            isFilter = true;
            var that=$(this);
            if(typeof that.data("min") !== undefined && typeof that.data("max") !== undefined) {
                if(parseInt($("#"+that.data("min")).val()) > parseInt($("#"+that.data("max")).val())) {
                    alert("Invalid min and max quantity!");
                    return;
                }
            }
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
        $('#nav-tabContent').on('click', '.reset_value', function () {
            var that=$(this);
            var key=that.data('key');
            var id=that.data('id');
            var modal=that.data('modal');
            $.ajax({
                type : 'get',
                url :  '{{url('/')."/reset_key/"}}' + key,
                success : function(){
                    get_value_filter(that.data('id'));
                    $('#'+modal).collapse('hide');
                    $("#A_searchByProductSku").attr('readonly', false);
                    $("#A_searchByMatchExactProduct").attr('disabled', false);
                    $("#U_searchByProductSku").attr('readonly', false);
                    $("#U_searchByMatchExactProduct").attr('disabled', false);
                    $("#P_searchByProductSku").attr('readonly', false);
                    $("#P_searchByMatchExactProduct").attr('disabled', false);
                    $("#RE_searchByProductSku").attr('readonly', false);
                    $("#RE_searchByMatchExactProduct").attr('disabled', false);
                    //$('#nav-manifest-tab').click();
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
        //All Order AWB or Order Id Searching
        // $('#nav-tabContent').on('click', '.allOrderSearchingBtn', function () {
        //     var that=$(this);
        //     var form=that.data('form');
        //     showOverlay();
        //     $('#'+form).ajaxSubmit({
        //         success: function (response) {
        //             $('#filter_order').html(response);
        //             $('#total_filter_order').html($('#total_order_count').val());
        //             var orderCount = $('#total_order_count').val();
        //             var perPage=$('.perPageRecord').val();
        //             // console.log(orderCount + " = " + limitOrder + " = " + $('.perPageRecord').val());
        //             if(parseInt(orderCount) < parseInt(perPage))
        //                 $('.order_display_limit').html(orderCount);
        //             else
        //             {
        //                 if(parseInt(perPage) > parseInt(orderCount))
        //                     $('.order_display_limit').html(orderCount);
        //                 else
        //                     $('.order_display_limit').html(perPage);
        //             }
        //             $('#order_count').html($('#total_order_count').val());
        //             $('.totalPage').html(Math.ceil($('#total_order_count').val() / $('.order_display_limit').html()));
        //             hideOverlay();
        //         }
        //     });
        // });
        //All Order AWB or Order Id Reset
        $('#nav-tabContent').on('click', '.allOrderResetBtn', function () {
            showOverlay();
            all_order();
        });
        //Processing Order Id Searching
        $('#nav-tabContent').on('click', '.processingSearchingBtn', function () {
            var that=$(this);
            var form=that.data('form');
            showOverlay();
            $('#'+form).ajaxSubmit({
                success: function (response) {
                    $('#processing_order_data').html(response);
                    var orderCount = $('#total_process_order').val();
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
                    $('#order_count').html($('#total_process_order').val());
                    $('.totalPage').html(Math.ceil($('#total_process_order').val() / $('.order_display_limit').html()));
                    hideOverlay();
                }
            });
        });
        //Processing or Order Id Reset
        $('#nav-tabContent').on('click', '.processingResetBtn', function () {
            showOverlay();
            processing_order();
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
                            var length = parseFloat(info.length);
                            var breadth = parseFloat(info.width);
                            var height = parseFloat(info.height);

                            if(weight != NaN)
                                totalWeight += weight;
                            $('#weight').val(totalWeight);
                            $('#length').val(length);
                            $('#breadth').val(breadth);
                            $('#height').val(height);
                        }
                    },
                    error: function (response) {
                        hideOverlay();

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
        $('#weight_single').blur( function () {
            var that = $(this);
            if(that.val().trim() === '')
                return false;
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + (that.val() * 1000),
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#length_single').val(info.length);
                    $('#breadth_single').val(info.width);
                    $('#height_single').val(info.height);
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

        $('#multipleDimensionModal').on('blur', '.weightfield', function () {
            var that = $(this);
            var counter = that.data('id');
            if(that.val().trim() === '')
                return false;
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + (that.val() * 1000),
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#length_'+counter).val(info.length);
                    $('#breadth_'+counter).val(info.width);
                    $('#height_'+counter).val(info.height);
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

        $('#weight_').blur( function () {
            var that = $(this);
            if(that.val().trim() === '')
                return false;
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + (that.val() * 1000),
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#length_').val(info.length);
                    $('#breadth_').val(info.width);
                    $('#height_').val(info.height);
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
        //get dimensi data in modal
        $('#nav-tabContent').on('click', '.editDeliveryAddress', function () {
            var that = $(this);
            $('#d_id').val(that.data('id'));
            $('#delivery_name').val(that.data('name'));
            $('#delivery_contact').val(that.data('contact'));
            $('#delivery_address1').val(that.data('address1'));
            $('#delivery_address2').val(that.data('address2'));
            $('#delivery_pincode').val(that.data('pincode'));
            $('#delivery_city').val(that.data('city'));
            $('#delivery_state').val(that.data('state'));
            $('#delivery_country').val(that.data('country'));
            $('#deliveryAddressModal').modal('show');
        });
        $('#btnSubmitDeliveryForm').click(function(){
            if(!$("#deliveryAddressForm").valid()) {
                return;
            }
            $('#deliveryAddressForm').ajaxSubmit({
                success: function (response) {
                    $.notify(" Success... Address Updated Successfully", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#deliveryAddressModal').modal('hide');
                    let editbutton = ' <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="'+ $('#delivery_address1').val() +' <br> '+ $('#delivery_address2').val() +' <br> '+ $('#delivery_city').val() +' '+ $('#delivery_state').val() + ' '+ $('#delivery_pincode').val() +'"><i class="fas fa-eye text-primary"></i> <i data-id="'+ $('#d_id').val() +'" data-name="'+ $('#delivery_name').val() +'" data-contact="'+ $('#delivery_contact').val() +'" data-address1="'+ $('#delivery_address1').val() +'" data-address2="'+$('#delivery_address2').val()+'" data-pincode="'+$('#delivery_pincode').val()+'" data-city="'+$('#delivery_city').val()+'" data-state="'+$('#delivery_state').val()+'" data-country="'+$('#delivery_country').val()+'" class="fas fa-edit text-primary editDeliveryAddress"></i></a>'
                    $('#all_order_delivery_'+$('#d_id').val()).html($('#delivery_state').val() + '<br>' + $('#delivery_city').val() + '<br>' + $('#delivery_pincode').val() + editbutton);
                    //fetchCurrentTabData();
                }
            });
        });
        // Ajax Submit Dimension Form
        $('#dimesionSubmitBtn').click(function () {
            if(!$("#dimensionForm").valid()) {
                return;
            }
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
        $('#multipleDimensionSubmitBtn').click(function () {
            if(!$("#multipleDimensionForm").valid()) {
                return;
            }
            showOverlay();
            $('#multipleDimensionForm').ajaxSubmit({
                success: function (res) {
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
                url: '{{url('/')."/modify-order/"}}' + that.data('id'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#order_form').prop('action', '{{route('seller.update_order')}}');
                    $('#oid').val(info.order.id);
                    $('#customer_order_number').val(info.order.customer_order_number);
                    $('#order_number').val(info.order.order_number);
                    if (info.order.shipment_type == 'mps') {
                        $('#shipment_type').prop('checked', true);
                        $('#shipment_type').parent('div').removeClass('off').removeClass('btn-danger').addClass('btn-success');
                        $('#shipment_type').trigger('change');
                        $('#number_of_packets').val(info.order.number_of_packets);
                        $('#number_of_packets').trigger('change');
                    } else {
                        $('#shipment_type').prop('checked', false);
                        $('#shipment_type').parent('div').addClass('off').addClass('btn-danger').removeClass('btn-success');
                    }
                    $('#customer_name').val(info.order.s_customer_name);
                    $('#country').val(info.order.b_contact_code.includes("+") ? info.order.b_contact_code : "+"+ info.order.b_contact_code );
                    $('#contact').val(info.order.s_contact);
                    $('#address').val(info.order.s_address_line1);
                    $('#address2').val(info.order.s_address_line2);
                    $('#pincode').val(info.order.s_pincode);
                    $('#txtCountry').val(info.order.s_country);
                    $('#state').val(info.order.s_state);
                    $('#ewaybill_number').val(info.order.ewaybill_number);
                    $('#city').val(info.order.s_city);
                    $('#weight').val(info.order.weight / 1000);
                    $('#height').val(info.order.height);
                    $('#length').val(info.order.length);
                    $('#breadth').val(info.order.breadth);
                    if(info.order.global_type == 'international'){
                        $('#global_type').prop('checked',true);
                        isInternational = true;
                        $('#global_type').parent('div').removeClass('off').removeClass('btn-primary').addClass('btn-success');
                        $('#ioss').val(info.international_order.ioss);
                        $('#eori').val(info.international_order.eori);
                        $('#invoice_reference_number').val(info.international_order.invoice_number);
                    }
                    else{
                        $('#global_type').prop('checked',false);
                        isInternational = false;
                        $('#global_type').parent('div').addClass('off').addClass('btn-primary').removeClass('btn-success');
                    }
                    if (info.order.order_type == 'cod')
                        $('#type_cod').prop('selected', true);
                    else if (info.order.order_type == 'prepaid')
                        $('#type_prepaid').prop('selected', true);
                    else
                        $('#type_reverse').prop('selected', true);
                    if (info.order.o_type === 'forward') {
                        $('#o_type_forward').prop('checked', true);
                        $('#type_cod').attr('disabled',false);
                        $('#qc_enable_div').hide();
                        $('#qc_enable').hide();
                        $('#same_as_rto_div').show();
                    }
                    else {
                        $('#o_type_reverse').prop('checked', true);
                        $('#type_cod').attr('disabled',true);
                        $('#qc_enable_div').show();
                        $('#qc_enable').show();
                        $('#qc_enable').prop('checked',false);
                        $('#qc_enable').parent('div').addClass('off').addClass('btn-primary').removeClass('btn-success');
                        $('#qc_enable').trigger('change');
                        $('#same_as_rto_div').hide();
                    }
                    if(info.order.is_qc === 'y'){
                        $('.o_type').trigger('change');
                        $('#qc_enable').prop('checked',true);
                        $('#qc_enable').parent('div').removeClass('off').removeClass('btn-primary').addClass('btn-success');
                        $('#qc_enable').trigger('change');
                        // $('#qc_label').val(info.qc_details.qc_label);
                        // $('#value_to_check').val(info.qc_details.qc_value_to_check);
                        $('#help_description').val(info.qc_details.qc_help_description);
                        $('#product_image').removeAttr('required');

                        $('#qc_labels_body').empty();

                        for (var i = 0; i < (info.qc_details.qc_label).split(",").length; i++) {
                            add_row_qc_label_update(i);
                        }

                        if((info.qc_details.qc_help_description).length > 0){
                            var imageHtml = '';
                            if(info.images.length > 0) {
                                for (var i = 0; i < info.images.length; i++) {
                                    imageHtml += '<a target="_blank" href="'+ info.images[i] +'"><img src="' + info.images[i] + '" alt="Image" style="max-width:15%;max-height:60%;"></a>&nbsp;&nbsp;'
                                }
                                $('#qc_display_images').show();
                                $('#qc_display_images').html(imageHtml);
                            }
                        }

                        var qcLabelInfo = info.qc_details.qc_label.split(",");
                        var qcValueToCheck = info.qc_details.qc_value_to_check.split(",");

                        for (var i = 0; i < qcLabelInfo.length; i++) {
                            $('#qc_label' + [i]).val((qcLabelInfo[i] === null || qcLabelInfo[i] === undefined) ? "" : qcLabelInfo[i]);
                            $('#value_to_check' + [i]).val(qcValueToCheck[i]);
                        }
                    }

                    else{
                        $('#qc_enable').prop('checked',false);
                        $('#qc_enable').parent('div').addClass('off').addClass('btn-primary').removeClass('btn-success');
                        $('#qc_enable').trigger('change');
                    }
                    // $('.o_type').trigger('change');
                    // if(info.order.global_type == 'international'){
                    //     $('#global_type').prop('checked',true);
                    //     $('#global_type').parent('div').removeClass('off').removeClass('btn-danger').addClass('btn-success');
                    //     $('#ioss').val(info.international_order.ioss);
                    //     $('#eori').val(info.international_order.eori);
                    // }
                    // else{
                    //     $('#global_type').prop('checked',false);
                    //     $('#global_type').parent('div').addClass('off').addClass('btn-danger').removeClass('btn-success');
                    // }
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
                        $('#hsn_number' + [i]).val(info.product[i].hsn_number);
                        $('#hts_number' + [i]).val(info.product[i].hts_number);
                        $('#total_amount' + [i]).val(info.product[i].total_amount);
                    }
                    $('#invoice_amount').val(info.order.invoice_amount);
                    $('#shipping_charges').val(info.order.s_charge);
                    $('#cod_charges').val(info.order.c_charge);
                    $('#warehouse_'+info.order.warehouse_id).prop('checked', true);
                    $('#discount').val(info.order.discount);
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
        $('.o_type').change(function(){
            if($(this).val() === 'reverse') {
                $('#qc_enable_div').show();
                $('#qc_enable').show()
                $('#qc_enable').trigger('change');
                $('#same_as_rto_div').hide();
            }
            else {
                $('#qc_enable_div').hide();
                $('#qc_enable').hide();
                $('.warehouse_submit_button').show();
                $('#QCInformationButton').hide();
                $('#qc_information_div').hide();
                $('#same_as_rto_div').show();
            }
        });

        $('#qc_enable').change(function(){
            if($(this).prop('checked')){
                $('.warehouse_submit_button').hide();
                $('#QCInformationButton').show();
                $('#qc_information_div').show();
            }
            else {
                $('.warehouse_submit_button').show();
                $('#QCInformationButton').hide();
                $('#qc_information_div').hide();
            }
        });

        $('#previous_warehouse_button').click(function () {
            $('#warehouse_tab').slideDown();
            $('#qc_tab').slideUp();
        });

        $('#QCInformationButton').click(function(){
            $('.all_tabs').slideUp();
            $('#qc_tab').slideDown();
        });
        //axxording of order detail form
        $('#orderTabButton').click(function () {
            $('input[name="customer_order_number"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                var div = $('#global_type').parents('div');
                if(div.hasClass('btn-success')){
                    isInternational = true;
                    $('#global_type').attr('checked',true);
                    $('.international').show();
                    $('.hidecountry').show();
                    // $('#iec_code').attr('required',true);
                    // $('#ioss').attr('required',true);
                    // $('#ad_code').attr('required',true);
                    // $('#eori').attr('required',true);
                    $('#hsn').attr('required',true);
                    $('#hts').attr('required',true);
                    $('#pincode').attr('maxlength',5);
                    $('#pincode').attr('minlength',5);
                }

                else{
                    isInternational = false;
                    $('.hidecountry').hide();
                    $('#global_type').attr('checked',false);
                    $('.international').hide();
                    // $('#iec_code').attr('required',false);
                    // $('#ioss').attr('required',false);
                    // $('#ad_code').attr('required',false);
                    // $('#eori').attr('required',false);
                    $('#pincode').attr('maxlength',6);
                    $('#pincode').attr('minlength',6);
                }
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
                if(isInternational){
                    $('.hsnTD').show();
                    $('.htsTD').show();
                    $('#hsnColumn').show();
                    $('#htsColumn').show();
                    $('.hsn_number').show();
                    $('.hts_number').show();
                }

                else{
                    $('.hsnTD').hide();
                    $('.htsTD').hide();
                    $('#hsnColumn').hide();
                    $('#htsColumn').hide();
                    $('.hsn_number').hide();
                    $('.hts_number').hide();
                }
            }
        });
        $('#ProductTabButton').click(function () {
             $(".product_requierd").valid();
            // $('input[name="weight"]').valid();
            // $('input[name="length"]').valid();
            // $('input[name="height"]').valid();
            // $('input[name="breadth"]').valid();
            // $('input[name="product_name[]"]').valid();
            // $('input[name="product_sku[]"]').valid();
            // $('input[name="breadth"]').valid();
            if ($('#order_form').valid() && $(".product_requierd").valid()) {
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
                let div = $('#same_as_rto').parent('div');
                if(div.hasClass('btn-success')){
                    $('#warehouse_rto').hide();
                }
                else{
                    $('#warehouse_rto').show();
                }
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

        $('#order_form').submit(function(e){
            e.preventDefault();
            if($('#qc_enable').prop('checked')){
                if($('.qc_info_required').valid()){
                    showOverlay();
                    this.submit();
                }
                else{
                    return false;
                }
            }
            else{
                showOverlay();
                this.submit();
            }
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

        $(document).on('click', '.addLabel', function () {
            ++labelCounter;
            // if($(".shipment_type:checked").val() == "mps") {
            //     add_mps_row(rowCounter, $(this).data("target"), $(this).data("tab"));
            // } else if($(".shipment_type:checked").val() == "single") {
            //     add_row(rowCounter);
            // }
            add_row_label(rowCounter);
        });

        function add_row_label(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="qc_label' + cnt + '" name="qc_label[]" class="form-control qc_label qc_info_required" required="" placeholder="Label"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="value_to_check' + cnt + '" name="value_to_check[]" class="form-control value_to_check qc_info_required" required="" placeholder="Value To Check"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            $('#qc_labels_body').append(html);
        }

        function add_row_qc_label_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="qc_label' + cnt + '" name="qc_label[]" class="form-control qc_label qc_info_required" required="" placeholder="Label"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="value_to_check' + cnt + '" name="value_to_check[]" class="form-control value_to_check qc_info_required" required="" placeholder="Value To Check"/></td>';
            if(cnt === 0){
                html += '<td><button type="button" name="add" class="btn btn-info btn-sm addLabel"><i class="fa fa-plus"></i></button></td></tr>';
            }else{
                html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            }
            $('#qc_labels_body').append(html);
        }

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
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_requierd product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_requierd product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control  product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>';
            if(isInternational){
                html += '<td class="hsnTD"><input type="text" data-id="' + cnt + '" id="hsn_number' + cnt + '" name="hsn_number[]" class="form-control hsn_number" required="" value="" placeholder="HSN Number" maxlength="30"/></td>';
                html += '<td class="htsTD"><input type="text" data-id="' + cnt + '" id="hts_number' + cnt + '" name="hts_number[]" class="form-control hts_number" required="" value="" placeholder="HTS Number" maxlength="30"/></td>';
            }
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            $('#single_shipment_product_details').append(html);
        }
        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_requierd product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_requierd product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control  product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>';
            if(isInternational){
                html += '<td class="hsnTD"><input type="text" data-id="' + cnt + '" id="hsn_number' + cnt + '" name="hsn_number[]" class="form-control hsn_number" required="" value="" placeholder="HSN Number" maxlength="30"/></td>';
                html += '<td class="htsTD"><input type="text" data-id="' + cnt + '" id="hts_number' + cnt + '" name="hts_number[]" class="form-control hts_number" required="" value="" placeholder="HTS Number" maxlength="30"/></td>';
            }
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
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku${tab}[]" class="form-control product_requierd product_sku numberonly" placeholder="Product SKU"/></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name${tab}[]" class="form-control product_requierd product_name numberonly" required="" placeholder="Product Name"/></td>
                    <td><input type="number" data-id="${i}" id="product_qty${i}" name="product_qty${tab}[]" class="form-control product_qty numberonly" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>
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
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku1[]" class="form-control product_requierd product_sku" placeholder="Product SKU"/></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name1[]" class="form-control product_requierd product_name" required="" placeholder="Product Name"/></td>
                    <td><input type="number" data-id="${i}" id="product_qty${i}" name="product_qty1[]" class="form-control product_requierd product_qty numberonly" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>
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
            // $('#data_div').show();
            // $('#form_div').hide();
            // $('#order_form').trigger('reset');
                history.back();
        });
        $('#cancelButton').click(function () {
            $('#order_form').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });
        $('#pincode').blur(function () {
            var that = $(this);
            if(!isInternational){
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
            }
            else{
                if (that.val().trim().length === 5)
                    that.removeClass('invalid');
                else
                    that.addClass('invalid');
            }
        });
        $('#nav-tabContent').on('click','.internationalShipOrderButton', function(){
            var that = $(this);
            $.ajax({
                url: '{{url('/')."/ship-order"}}/' + that.data('id'),
                success: function(response){
                    $('#partner_details_ship').html(response);
                    $('#courier_partner_select').modal('show');
                },
                error: function (response) {
                    $.notify(" Pincode is not Serviceable.", {
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

        });
        $('#delivery_pincode').blur(function () {
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
                            $('#delivery_city').val(info.city);
                            $('#delivery_state').val(info.state);
                            $('#delivery_country').val(info.country);
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
                    url: '{{url('/')."/delete-order"}}/' + that.data('id'),
                    success: function (response) {
                        hideOverlay();
                        $.notify(" Order deleted Successfully", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                        fetchCurrentTabData();
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
        $('#nav-tabContent').on('click', '.shipOrderButton', function () {
            var that = $(this);
            if (that.data('status') == 'pending' || that.data('status') == 'manifested' || that.data('status') == 'shipped' || that.data('status') == 'pickup_requested' || that.data('status') == 'pickup_scheduled') {
                $.ajax({
                    url: '{{url('/')."/ship-order"}}/' + that.data('id'),
                    success: function (response) {
                        // console.log(response);
                        hideOverlay();
                        if (response == 1) {
                            $.notify(" Oops... Please add Proper Dimension!!", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }else if(response ==0){
                            $.notify(" Oops... Please add Default Warehouse!!", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }
                        else if(response == "false"){
                            $.notify(" Pincode is not Serviceable.", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }
                        else{
                            // console.log(response);
                            var cod_charge=0,early_cod=0;
                            $('#courier_partner_select').modal('show');
                            $('#partner_details_ship').html(response);
                            $('#order_id_single').val(that.data('id'));
                            if(that.data('status') == 'pending') {
                                $("#courier_partner_select").data('action', "{{route('seller.single_ship_order')}}");
                            } else {
                                $("#courier_partner_select").data('action', "{{route('seller.reassign_order')}}");
                            }
                            hideOverlay();
                        }
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Pincode is not Serviceable.", {
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
            } else {
                $.notify(" Oops... Something went wrong", {
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
        $('#nav-tabContent').on('click', '.cancelOrderButton', function () {
            var that = $(this);
            if (window.confirm("Are you sure want to Cancel?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/cancel-order"}}/' + that.data('id'),
                    success: function (response) {
                        hideOverlay();
                        $.notify(response.message, {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "close"
                        });
                        //location.reload();
                        fetchCurrentTabData();
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
                    $('#editWarehouse').fadeIn();
                } else if(currentTab == 'unprocessable'){
                    $('#editAllButton').fadeIn();
                    $('#editWarehouse').fadeIn();
                }
                if(currentTab == 'all_order' || currentTab == 'ready_to_ship' || currentTab == 'manifest'){
                    $('#cancelSelectButton').fadeIn();
                    //$('#ReassignOrder').fadeIn();
                    $('#LabelSelectButton').fadeIn();
                    $('#InvoiceSelectButton').fadeIn();
                    $('#manifestDownload').fadeIn();
                }
                // $('.total_order_selected').html($('.total_order_display').html());
                $('.total_order_selected').html($('.order_display_limit').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#editAllButton').hide();
                $("#editWarehouse").hide();
                $('#cancelSelectButton').hide();
                $('#ReassignOrder').hide();
                $('#LabelSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#manifestDownload').hide();
                $('.total_order_selected').html(0);
            }
            updateSelectedCounter();
        });
        if(currentTab == 'all_order'){
            $('#ManifestSelectButton').hide();
            $('#LabelSelectButton').hide();
            $('#InvoiceSelectButton').hide();
            $('#manifestDownload').hide();
        }
        $('#nav-tabContent').on('click', '#checkManifestButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                $('.ManifestCheck').prop('checked', true);
                $('#ManifestSelectButton').fadeIn();
                $('#LabelSelectButton').fadeIn();
                $('#InvoiceSelectButton').fadeIn();
                $('#manifestDownload').fadeIn();
                $('#cancelSelectButton').fadeIn();
                $('#ReassignOrder').hide();
                $('#PickupRequetedButton').fadeIn();
                // $('.total_order_selected').html($('.total_order_display').html());
                $('.total_order_selected').html($('.order_display_limit').html());
            } else {
                $('.ManifestCheck').prop('checked', false);
                $('#ManifestSelectButton').hide();
                $('#LabelSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#manifestDownload').hide();
                $('#cancelSelectButton').hide();
                $('#ReassignOrder').hide();
                $('#PickupRequetedButton').hide();
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
                    $("#editWarehouse").hide();
                    $('#removeAllButton').fadeIn();
                }else{
                    $('#removeAllButton').fadeIn();
                    if(currentTab === "return")
                    {
                        $('#removeAllButton').hide();
                    }
                    if(currentTab == 'processing'){
                        $('#shipAllButton').fadeIn();
                        $('#editAllButton').fadeIn();
                        $("#editWarehouse").fadeIn();
                    } else if(currentTab == 'unprocessable'){
                        $('#editAllButton').fadeIn();
                        $("#editWarehouse").fadeIn();
                    }
                    // $('#shipAllButton').fadeIn();
                    if(currentTab == 'all_order' || currentTab == 'ready_to_ship' || currentTab == 'manifest'){

                        $('#cancelSelectButton').fadeIn();
                        $('#ReassignOrder').fadeIn();
                    }
                }
                if(currentTab == 'all_order') {
                    $('#LabelSelectButton').fadeIn();
                    $('#InvoiceSelectButton').fadeIn();
                    $('#manifestDownload').fadeIn();
                    $('#ReassignOrder').hide();
                }
            } else {
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#editAllButton').hide();
                $("#editWarehouse").hide();
                $('#cancelSelectButton').hide();
                $('#ReassignOrder').hide();
                $('#LabelSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#manifestDownload').hide();
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
                $('#manifestDownload').fadeIn();
                $('#LabelSelectButton').fadeIn();
                $('#cancelSelectButton').fadeIn();
                $('#ReassignOrder').hide();
                $('#PickupRequetedButton').fadeIn();
                $('#InvoiceSelectButton').fadeIn();
            } else {
                $('#ManifestSelectButton').hide();
                $('#manifestDownload').hide();
                $('#LabelSelectButton').hide();
                $('#cancelSelectButton').hide();
                $('#ReassignOrder').hide();
                $('#PickupRequetedButton').hide();
                $('#InvoiceSelectButton').hide();
            }
            $('.total_order_selected').html(cnt);
        });
        //MANIFEST GENERATE
        $('#ManifestSelectButton').click(function () {
            var that=$(this);
            that.prop('disabled',true);
            order_ids = [];
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                    order_ids.push($(this).val());
            });
            showOverlay();
            // alert(order_ids);
            $.ajax({
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'ids': order_ids
                },
                url: '{{url('/')."/generate-manifest"}}',
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
        });
        // Pickup requested
        $('#PickupRequetedButton').click(function () {
            var that=$(this);
            that.prop('disabled',true);
            order_ids = [];
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                    order_ids.push($(this).val());
            });
            showOverlay();
            // alert(order_ids);
            $.ajax({
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'ids': order_ids
                },
                url: '{{url('/')."/pickup-requested"}}',
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
        });
        //Download Selected Invoice
        $('#InvoiceSelectButton').click(function () {
            let isAllowed = true;
            order_ids = [];
            if(currentTab == 'ready_to_ship') {
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            } else {
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            }
            if(!isAllowed){
                $.notify(" Oops... You can not select Pending or Cancelled Orders!", {
                    blur: 0.2,
                    delay: 0,
                    verticalAlign: "top",
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
                return;
            }
            //alert(order_ids); return false;
            $('#multiinvoice_id').val(order_ids);
            $('#MultiInvoiceForm').submit();
        });
        //ManifestDownload
        $('#manifestDownload').click(function () {
            let isAllowed = true;
            order_ids = [];
            if(currentTab == 'ready_to_ship') {
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            } else {
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            }
            if(!isAllowed){
                $.notify(" Oops... You can not select Pending or Cancelled Orders!", {
                    blur: 0.2,
                    delay: 0,
                    verticalAlign: "top",
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
                return;
            }
            //alert(order_ids); return false;
            $('#manifest_id').val(order_ids);
            $('#ManifestForm').submit();
        });
        //Download Selected Label
        $('#LabelSelectButton').click(function () {
            let isAllowed = true;
            order_ids = [];
            if(currentTab == 'ready_to_ship') {
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            } else {
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
            }
            if(!isAllowed){
                $.notify(" Oops... You can not select Pending or Cancelled Orders!", {
                    blur: 0.2,
                    delay: 0,
                    verticalAlign: "top",
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
                return;
            }
            $('#multilable_id').val(order_ids);
            $('#MultilabelForm').submit();
        });
        $('#removeAllButton').click(function () {
            let isAllowed = false;
            del_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                {
                    if($(this).data('status') !== 'pending'){
                        isAllowed = true;
                    }else{
                        del_ids.push($(this).val());
                    }
                }

            });
            if(isAllowed){
                $.notify(" Oops... You can delete only Pending Orders.", {
                    blur: 0.2,
                    delay: 0,
                    verticalAlign: "top",
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
                return;
            }
            if (window.confirm("Are you sure want to Delete?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': del_ids
                    },
                    url: '{{url('/')."/remove-selected-order"}}',
                    success: function (response) {
                        hideOverlay();
                        $.notify(" Orders deletes successfully.", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                        fetchCurrentTabData();
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
        // $('#cancelSelectButton').click(function () {
        //     cancel_ids = [];
        //     if(currentTab === 'ready_to_ship'){
        //         $('.ManifestCheck:visible').each(function () {
        //             if ($(this).prop('checked'))
        //                 cancel_ids.push($(this).val());
        //         });
        //     }else{
        //         $('.selectedCheck:visible').each(function () {
        //             if ($(this).prop('checked'))
        //                 cancel_ids.push($(this).val());
        //         });
        //     }
        //     if (window.confirm("Are you sure want to Cancel selected Order?")) {
        //         showOverlay();
        //         $.ajax({
        //             type: 'post',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //                 'ids': cancel_ids
        //             },
        //             url: '{{url('/')."/cancel-selected-order"}}',
        //             success: function (response) {
        //                 let info = JSON.parse(response);
        //                 hideOverlay();
        //                 if(info.job){
        //                     $.notify(info.message, {
        //                         blur: 0.2,
        //                         delay: 0,
        //                         verticalAlign: "top",
        //                         animationType: "scale",
        //                         align: "right",
        //                         type: "success",
        //                         icon: "check"
        //                     });
        //                 }
        //                 else{
        //                     $.notify(" Orders has been Cancelled.", {
        //                         blur: 0.2,
        //                         delay: 0,
        //                         verticalAlign: "top",
        //                         animationType: "scale",
        //                         align: "right",
        //                         type: "success",
        //                         icon: "check"
        //                     });
        //                     fetchCurrentTabData();
        //                 }
        //             },
        //             error: function (response) {
        //                 hideOverlay();
        //                 $.notify(" Oops... Something went wrong!", {
        //                     blur: 0.2,
        //                     delay: 0,
        //                     verticalAlign: "top",
        //                     animationType: "scale",
        //                     align: "right",
        //                     type: "danger",
        //                     icon: "close"
        //                 });
        //             }
        //         });
        //     }
        // });
        $('#cancelSelectButton').click(function () {
            cancel_ids = [];
            if(currentTab === 'ready_to_ship'){
                $('.ManifestCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        cancel_ids.push($(this).val());
                });
                $('#ManifestSelectButton').hide();
                $('#manifestDownload').hide();
                $('#LabelSelectButton').hide();
                $('#cancelSelectButton').hide();
                $('#ReassignOrder').hide();
                $('#PickupRequetedButton').hide();
                $('#InvoiceSelectButton').hide();
            }else{
                $('.selectedCheck:visible').each(function () {
                    if ($(this).prop('checked'))
                        cancel_ids.push($(this).val());
                });
            }
            if (window.confirm("Are you sure want to Cancel selected Order?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': cancel_ids
                    },
                    url: '{{url('/')."/cancel-selected-order"}}',
                    success: function (response) {
                        let info = JSON.parse(response);
                        hideOverlay();
                        $.notify(info.message, {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                        fetchCurrentTabData();
                    },
                    error: function () {
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
        $('#ReassignOrder').click(function () {
            sel_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked') && ($(this).data('status') == 'manifested' || $(this).data('status') == 'shipped' || $(this).data('status') == 'pickup_requested' || $(this).data('status') == 'pickup_scheduled')) {
                    sel_ids.push($(this).val());
                }
            });
            $('.ManifestCheck:visible').each(function () {
                if ($(this).prop('checked') && ($(this).data('status') == 'manifested' || $(this).data('status') == 'shipped' || $(this).data('status') == 'pickup_requested' || $(this).data('status') == 'pickup_scheduled')) {
                    sel_ids.push($(this).val());
                }
            });
            if (sel_ids.length > 0) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/ship-order"}}',
                    success: function (response) {
                        // console.log(response);
                        hideOverlay();
                        $('#courier_partner_select').modal('show');
                        $('#partner_details_ship').html(response);
                        $('#order_id_single').val(sel_ids.join(','));
                        $("#courier_partner_select").data('action', "{{route('seller.reassign_order')}}");
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Pincode is not Serviceable.", {
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
            } else {
                alert("Please select valid orders to reassign")
            }
        });
        $('#shipAllButton').click(function () {
            sel_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked') && $(this).data('status') == 'pending') {
                    sel_ids.push($(this).val());
                    //alert($(this).val());
                }
            });
            // alert(sel_ids.length);
            if (window.confirm("Are you sure want to Ship selected Order?")) {
                $('#allOrderDetail').modal('show');
                $('#total_selected_order').html(sel_ids.length);
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
                type : 'post',
                url: '{{url('/')."/modify_multiple_dimension_data"}}',
                data: {
                    '_token' : '{{csrf_token()}}',
                    'ids': selectedIds
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
                                        ${i == 0 ? `<input type="checkbox" id="copy_to_all" value="y" data-placement="top" data-toggle="tooltip" data-original-title="Copy first dimension to all" data-id="${i}"> <label for="copy_to_all" class="ml-2 pb-2"><b>Order Number</b></label> <br>` : ''}
                                        <span>${order.customer_order_number}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="weight_${i}" class="pb-2"><b>Weight (Kg)</b></label>` : ''}
                                        <input type="number" class="form-control input-sm weightfield numberonly" placeholder="Weight" name="weight_${i}" id="weight_${i}" value="${order.weight/1000}" min="0.1" data-id="${i}" max="999" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="length_${i}" class="pb-2"><b>Length (cm)</b></label>` : ''}
                                        <input type="number" class="form-control numberonly" placeholder="Length (cm)" name="length_${i}" id="length_${i}" value="${order.length}" min="0.1" data-id="${i}" max="9999" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="breadth_${i}" class="pb-2"><b>Breadth (cm)</b></label>` : ''}
                                        <input type="number" class="form-control numberonly" placeholder="Breadth (cm)" name="breadth_${i}" id="breadth_${i}" value="${order.breadth}" min="0.1" data-id="${i}" max="9999" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        ${i == 0 ? `<label for="height_${i}" class="pb-2"><b>Height (cm)</b></label>` : ''}
                                        <input type="number" class="form-control numberonly" placeholder="Height (cm)" name="height_${i}" id="height_${i}" value="${order.height}" min="0.1" data-id="${i}" max="9999" required>
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
        $('#editWarehouse').click(function() {
            showOverlay();
            let selectedIds = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    selectedIds.push($(this).val());
            });
            $("#update_order_id").val(selectedIds.join(','));
            $('#multipleWarehouseModal').modal('show');
            hideOverlay();
        });
        $('#multipleWarehouseSubmitBtn').click(function () {
            if(!$(".update_warehouse_id:checked").val()) {
                $.notify(' Please select warehouse', {
                    blur: 0.2,
                    delay: 0,
                    verticalAlign: "top",
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "check"
                });
                return;
            }
            showOverlay();
            $('#multipleWarehouseForm').ajaxSubmit({
                success: function (res) {
                    if(res.status == true) {
                        location.reload();
                    } else {
                        $.notify(` ${res.message}`, {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: res.status == true ? "success" : "danger",
                            icon: "check"
                        });
                    }
                }
            });
            $('#multipleWarehouseModal').modal('hide');
            hideOverlay();
        });
        $('.MultiShipButton').click(function () {
            var that = $(this);
            that.prop('disabled',true);
            //   alert(sel_ids.length);
            showOverlay();
            $.ajax({
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'ids': sel_ids
                },
                url: '{{url('/')."/ship-selected-order"}}',
                success: function (response) {
                    hideOverlay();
                    $('#allOrderDetail').modal('hide');
                    that.prop('disabled',false);
                    var info=JSON.parse(response);
                    if(info.status === 'true'){
                        if(info.job){
                            $.notify(" " + info.message, {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: 'success',
                                icon: 'check'
                            });
                        }else{
                            let message = " " + info.shipped + " orders shipped from " + info.total + " orders";
                            let type = "success";
                            let icon = "check";
                            if(info.balanceFlag == 1)
                                message = " " + info.shipped + " orders shipped from " + info.total + " orders. Balance Exhausted, Please Recharge";
                            if(info.balanceFlag == 2){
                                message = " Booking failed due to insufficient balance. Please recharge and try!! ";
                                type = "danger";
                                icon = "close";
                            }
                            $.notify(message, {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: type,
                                icon: icon
                            });
                        }
                        $('#nav-processing-tab').click();
                        countOrder();
                    }else{
                        $.notify(" Something went wrong please try again", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
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
            "product_qty[]": {
                required: true,
                notOnlyZero: '0'
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
            "product_qty[]": {
                required: "Please Enter Product Qty",
                notOnlyZero: "Please Enter Product Qty"
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
    $.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseInt(value) > 0;
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
        //  setInterval(function () {
        //     checkProcessedOrders();
        // },30000);

        $('#rto_warehouse').click(function () {
            if ($('#rto_warehouse').is(':checked')) {
                $('#warehouse_rto').hide();
            } else {
                $('#warehouse_rto').show();
            }

        });
        switch(selectedTab){
            case 'new_order':
                $('.addInfoButton').click();
                $('#nav-all_orders-tab').click();
                break;
            case 'add_reverse_order':
                $('.o_type').trigger('change');
                $('.addInfoButton').click();
                $('#o_type_reverse').prop('checked', true);
                $('#type_prepaid').prop("selected", true);
                $('#type_cod').prop("disabled", true);
                $('#reverse_ship_message').html('(Order will be Pickup from Here)');
                $('#reverse_warehouse_message').html('(Order will be Delivered from Here)');
                break;
            case 'unprocessable':
                $('#nav-unprocessable-tab').click();
                $('#unprocessableIcon').click();
                $('#total_unprocessable_order_data').click();
                break;
            case 'processing':
                $('#nav-processing-tab').click();
                break;
            case 'ready_ship':
                $('#nav-ready-ship-tab').click();
                break;
            case 'manifest':
                $('#nav-manifest-tab').click();
                break;
            case 'returns':
                $('#nav-return-tab').click();
                $('#returnIcon').click();
                $('#total_return_order_data').click();
                break;
            case 'return_orders':
                $('#nav-return-tab').click();
                $('#returnIcon').click();
                $('#total_return_order_data').click();
                break;
            case 'reverse_orders':
                all_reverse_order();
                break;
            default:
                $('#nav-all_orders-tab').click();
                break;
        }
        $("#ready_to_ship_data").on("click", ".printBtn", function() {
            var w = window.open($(this).data('src'));
            if (navigator.appName == 'Microsoft Internet Explorer') {
                window.print();
            } else {
                w.print();
            }
        });
    });
    function all_reverse_order(){
        currentTab='reverse_order';
        showOverlay()
        $.ajax({
            method : 'get',
            data : {
                'page' : pageCount
            },
            url: '{{route('seller.all_reverse_order')}}',
            success: function (response) {
                $('#filter_order').html(response);
                var orderCount = $('#total_reverse_order').val();
                var perPage=$('.perPageRecord').val();
                $('#total_filter_order').html(orderCount);
                if(parseInt(orderCount) < parseInt(perPage))
                    $('.order_display_limit').html(orderCount);
                else
                {
                    if(parseInt(perPage) > parseInt(orderCount))
                        $('.order_display_limit').html(orderCount);
                    else
                        $('.order_display_limit').html(perPage);
                }
                $('#order_count').html($('#total_reverse_order').val());
                $('.totalPage').html(Math.ceil($('#total_reverse_order').val() / $('.order_display_limit').html()));
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
    function fetchCurrentTabData() {
        if(currentTab === 'all_order')
            $('#nav-all_orders-tab').click();
        else if(currentTab === 'ready_to_ship')
            $('#nav-ready-ship-tab').click();
        else if(currentTab === 'unprocessable')
            $('#unprocessableIcon').click();
        else if(currentTab === 'processing')
            $('#nav-processing-tab').click();
        else if(currentTab === 'returns')
            $('#nav-return-tab').click();
        else
            location.reload();
        countOrder();
    }


</script>
</body>
</html>
