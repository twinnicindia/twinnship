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
                        <h3 class="h4 mb-4">All Orders
                        <div class="float-right">
                            <a class="btn btn-primary FetchOrder btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Fetch Order"><i class="fa fa-sync"></i></a>
                            <button type="button" class="btn btn-primary addInfoButton btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Add Order"><i class="fa fa-plus"></i></button>
                            <a href="{{route('seller.export_csv_order')}}">
                                <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                            </a>
                            <span data-toggle="modal" data-target="#bulkupload"><button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Import CSV"><i class="fa fa-download"></i></button></span>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="shipAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Ship Order"><i class="fa fa-shipping-fast"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="cancelSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order"><i class="fa fa-times"></i></button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="ManifestSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Generate Manifest"><i class="far fa-file-invoice"></i> Generate Manifest</button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="InvoiceSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Download Invoice"><i class="far fa-print"></i> Download Invoice</button>
                            <button type="button" class="btn btn-primary btn-sm mx-0" id="LabelSelectButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Download Label"><i class="far fa-tag"></i> Download Label</button>
                            <button type="button" class="btn btn-danger btn-sm mx-0" id="removeAllButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Delete Order"><i class="fa fa-trash"></i></button>

                            <form action="{{route('seller.multipleLableDownload')}}" method="post" id="MultilabelForm">
                                @csrf
                                <input type="hidden" name="multilable_id" id="multilable_id">
                            </form>

                            <form action="{{route('seller.multipleInvoiceDownload')}}" method="post" id="MultiInvoiceForm">
                                @csrf
                                <input type="hidden" name="multiinvoice_id" id="multiinvoice_id">
                            </form>
                        </div>
                        </h3>
                        <div>
                            <nav class="float-left mb-0">
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-all_orders-tab" data-toggle="tab" href="#nav-all_orders" role="tab" aria-controls="nav-all_orders" aria-selected="true"><i class="far fa-bring-forward"></i> All Orders <span class="badge  badge-pill badge-dark" id="total_filter_order">0</span> </a>
                                    <a class="nav-item nav-link" id="nav-unprocessable-tab" data-toggle="tab" href="#nav-unprocessable" role="tab" aria-controls="nav-unprocessable" aria-selected="false"><i class="far fa-exclamation-circle"></i> Unprocessable <span class="badge badge-pill badge-danger" id="total_unprocessable_order_data">0</span></a>
                                    <a class="nav-item nav-link" id="nav-processing-tab" data-toggle="tab" href="#nav-processing" role="tab" aria-controls="nav-processing" aria-selected="false"><i class="far fa-cogs"></i> Processing <span class="badge badge-pill badge-warning" id="total_processing_order_data">0</span></a>
                                    <a class="nav-item nav-link" id="nav-ready-ship-tab" data-toggle="tab" href="#nav-ready-ship" role="tab" aria-controls="nav-ready-ship" aria-selected="false"><i class="far fa-dolly"></i> Ready to Ship <span class="badge badge-pill badge-success" id="total_ready_to_ship_data">0</span></a>
                                    <a class="nav-item nav-link" id="nav-manifest-tab" data-toggle="tab" href="#nav-manifest" role="tab" aria-controls="nav-manifest" aria-selected="false"><i class="far fa-file-invoice"></i> Manifest <span class="badge badge-pill badge-success" id="total_manifest">0</span></a>
                                    <a class="nav-item nav-link" id="nav-return-tab" data-toggle="tab" href="#nav-return" role="tab" aria-controls="nav-contact" aria-selected="false"><i class="far fa-undo-alt"></i> Returns <span class="badge badge-pill badge-success" id="total_return_order_data">0</span></a>
                                </div>
                            </nav>
                            <span class="float-right">
                                <p class="mb-0 h6 f-14">Showing <span class="order_display_limit">{{$limit_order}}</span> of <span id="order_count"></span></p>
                                <p class="mb-0 h6 f-14">Selected <span class="total_order_selected">0</span> out of <span class="order_display_limit">{{$limit_order}}</span></p>
                            </span>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-all_orders" role="tabpanel" aria-labelledby="nav-all_orders-tab">
                                <div class="table-responsive" style="min-height: 400px;" >
                                    <table class="table table-hover mb-0" id="example1">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                                <th width="10%">Order Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_DateFilterModal" role="button" aria-expanded="false" aria-controls="A_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_DateFilterModal">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="A_DateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Start Date</label>
                                                                            <input type="date" class="form-control" name="start_date" placeholder="Min Amount" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>End Date</label>
                                                                            <input type="date" class="form-control" name="end_date" placeholder="Max Amount" required>
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
                                                </th>
                                                <th>Channel
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="A_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_ChannelFilterModal">
                                                            <label>Filter by Channel</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="A_channelForm">
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
                                                                    <label class="custom-control-label pt-1" for="A_{{$c->channel}}">{{$c->channel_name}}</label>
                                                                </div>
                                                                @endforeach

                                                                <button type="reset" data-id="filter_order" data-key="channel" data-modal="A_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_ChannelFilterModal" data-form="A_channelForm" data-id="filter_order" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Order Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_NumberFilterModal" role="button" aria-expanded="false" aria-controls="A_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="A_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_number">
                                                                <input type="hidden" name="filter_status" value="all_order">
                                                                <div class="form-group">
                                                                    <label for="A_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="A_searchByOrderId" placeholder="Enter Order Number Here">
                                                                </div>
                                                                <button type="reset" data-id="filter_order" data-key="order_number" data-modal="A_NumberFilterModal"" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_NumberFilterModal" data-form="A_orderIdForm" data-id="filter_order" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br> Status
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_StatusFilterModal" role="button" aria-expanded="false" aria-controls="A_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_StatusFilterModal">
                                                            <label>Filter by Status</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="A_StatusForm">
                                                                @csrf
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
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="cancelled" id="A_cancelled">
                                                                    <label class="custom-control-label pt-1" for="A_cancelled">Cancelled</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="delivered" id="A_delivered">
                                                                    <label class="custom-control-label pt-1" for="A_delivered">Delivered</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="hold" id="A_hold">
                                                                    <label class="custom-control-label pt-1" for="A_hold">Hold</label>
                                                                </div>
                                                                <button type="reset" data-id="filter_order" data-key="order_status" data-modal="A_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_StatusFilterModal" data-form="A_StatusForm" data-id="filter_order" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Amount
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_AmountFilterModal" role="button" aria-expanded="false" aria-controls="A_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_AmountFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="A_OrderAmountForm">
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
                                                    <br>Payment
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="A_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_FilterbyPaymentModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="A_PaymentTypeForm">
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
                                                </th>
                                                <th>Product Details
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="A_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_FilterbyProductModal">
                                                        <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="A_ProductFormFilter">
                                                                @csrf
                                                                <input type="hidden" name="key" value="product">
                                                                <input type="hidden" name="filter_status" value="all_order">

                                                                <div class="form-group">
                                                                    <label for="A_searchByProduct">Search by Product
                                                                        Name/SKU</label>
                                                                    <input type="text" class="form-control" name="value" id="A_searchByProduct" placeholder="Product Name / SKU">
                                                                </div>
                                                                <button type="reset" data-id="filter_order" data-key="product" data-modal="A_FilterbyProductModal"" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_FilterbyProductModal" data-form="A_ProductFormFilter" data-id="filter_order" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Customer Details</th>
                                                <th>Pickup Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="A_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_FilterbyPAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="A_PickupAddressForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="pickup_address">
                                                                <input type="hidden" name="filter_status" value="all_order">

                                                                <div class="form-group">
                                                                    <label for="A_searchByPAddress">Search Pickup Address</label>
                                                                    <input type="text" class="form-control" name="value" id=A_searchByPAddress" placeholder="Pickup Address">
                                                                </div>
                                                                <button type="reset" data-id="filter_order" data-key="pickup_address" data-modal="A_FilterbyPAddress"" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="A_FilterbyPAddress" data-form="A_PickupAddressForm" data-id="filter_order" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Delivery Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#A_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="A_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="A_FilterbyDAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="A_DeliveryAddressForm">
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
                                                </th>
                                                <th>Dimension</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="filter_order">
                                            @if(count($order)!=0)
                                            @php($cnt=1)
                                            <input type="hidden" value="{{count($order)}}" id="total_order_count">
                                            @foreach($order as $o)
                                            <tr id="row{{$o->id}}">
                                                <td>
                                                 @if($o->status != 'shipped')
                                                <input type="checkbox" class="selectedCheck" data-status="{{$o->status}}" value="{{$o->id}}">
                                                @endif
                                                </td>
                                                <?php
                                                $date = date('d/m/Y', strtotime($o->inserted));
                                                $time = date('h:i A', strtotime($o->inserted));
                                                ?>
                                                <td>Date : {{$date}} <br> Time : {{$time}}</td>
                                                <td>{{$o->channel==''?"Custom" : "$o->channel"}}</td>
                                                <td><a href='{{url("/view-order/$o->id")}}' target="_blank">{{$o->order_number}}</a><br>
                                                    @if($o->status=='pending')
                                                    <span class="text-danger font-weight-bold">{{$o->status}}</span>
                                                    @elseif($o->status == 'shipped')
                                                    <span class="text-success font-weight-bold">{{$o->status}}</span>
                                                    @else
                                                    <span class="text-primary font-weight-bold">{{$o->status}}</span>
                                                    @endif<br>
                                                    <span class="badge {{$o->o_type =='forward'?'badge-success':'badge-danger'}}">{{$o->o_type}}</span>
                                                </td>
                                                <td><span class="{{$o->invoice_amount == '' ? 'text-danger font-weight-bold' : ''}}">Amount </span> : {{$o->invoice_amount}}<br><span class="badge badge-success">{{$o->order_type}}</span></td>
                                                <td>
                                                    <?php
                                                    $product = \App\Models\Product::where('order_id', $o->id)->first();
                                                    $qty = \App\Models\Product::where('order_id', $o->id)->sum('product_qty');
                                                    ?>
                                                    Name : {{$product->product_name}}<br>
                                                    SKU : {{$product->product_sku}} <br>
                                                    Qty : {{$qty}} &nbsp;<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Name : @foreach(explode(',', $o->product_name) as $name) {{$name}} @endforeach SKU : @foreach(explode(',', $o->product_sku) as $sku) {{$sku}} @endforeach QTY : {{$qty}}"><i class="fas fa-eye text-primary"></i></a>

                                                </td>
                                                <td>
                                                <span class="{{$o->b_customer_name == '' ? 'text-danger font-weight-bold' : ''}}">Name</span> :{{$o->b_customer_name}}<br>
                                                <span class="{{$o->b_contact == '' ? 'text-danger font-weight-bold' : ''}}">Contact</span> :{{$o->b_contact}}
                                                </td>
                                                <td>{{$o->p_address_line1}} <br>
                                                    {{$o->p_address_line2}} <br>
                                                    {{$o->p_city}} <br>
                                                    {{$o->p_pincode}}</td>
                                                <td>{{$o->b_address_line1}} <br>
                                                   {{$o->b_address_line2}} <br>
                                                    {{$o->b_city}} <br>
                                                    {{$o->b_pincode}}</td>
                                                <td>
                                                    <div>
                                                        <span class="{{$o->weight == '' ? 'text-danger font-weight-bold' : ''}}">Weight</span> : {{$o->weight != '' ? $o->weight / 1000 : ''}}
                                                        @if($o->status == 'pending')
                                                        <a class="dimensiondata ml-1" data-type="all_order" data-placement="top" data-toggle="tooltip" data-original-title="Edit Dimension" data-id="{{$o->id}}"> <i class="fas fa-edit fa-lg text-primary"></i></a>
                                                        @endif
                                                        <br>
                                                        <span class="{{$o->height == '' ? 'text-danger font-weight-bold' : ''}}">Height</span> : {{$o->height}}<br>
                                                        <span class="{{$o->length == '' ? 'text-danger font-weight-bold' : ''}}">Length</span> : {{$o->length}}<br>
                                                        <span class="{{$o->breadth == '' ? 'text-danger font-weight-bold' : ''}}">Breadth</span> : {{$o->breadth}}<br>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($o->status != 'delivered')
                                                    @if($o->status != 'shipped')
                                                    <button type="button" title="Cancel Order" class="btn btn-danger cancelOrderButton btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order"> <i class="fas fa-times"></i> </button>
                                                    <button type="button" title="Edit Order" class="btn btn-primary modify_data btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Edit Order"> <i class="fas fa-pencil"></i> </button>
                                                    <a href="javascript:;" title="Delete Order" data-id="{{$o->id}}" class="btn btn-danger btn-sm remove_data mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Delete Order"><i class="fa fa-trash"></i></a>
                                                    <button type="button" title="Clone Order" class="btn btn-warning clone_data btn-sm mx-0" data-number="{{$o->order_number}}" data-placement="top" data-toggle="tooltip" data-original-title="Clone Order"> <i class="fas fa-clone"></i> </button>
                                                    <button type="button" title="Reverse Order" class="btn btn-info reverse_data btn-sm mx-0" data-number="{{$o->order_number}}" data-placement="top" data-toggle="tooltip" data-original-title="Reverse Order"> <i class="fas fa-undo-alt"></i> </button>
                                                    @else
                                                    <button type="button" title="Cancel Order" class="btn btn-danger cancelOrderButton btn-sm mx-0" data-id="{{$o->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order"> <i class="fas fa-times"></i> </button>
                                                    <button type="button" title="Clone Order" class="btn btn-warning clone_data btn-sm mx-0" data-number="{{$o->order_number}}" data-placement="top" data-toggle="tooltip" data-original-title="Clone Order"> <i class="fas fa-clone"></i> </button>
                                                    <button type="button" title="Reverse Order" class="btn btn-info reverse_data btn-sm mx-0" data-number="{{$o->order_number}}" data-placement="top" data-toggle="tooltip" data-original-title="Reverse Order"> <i class="fas fa-undo-alt"></i> </button>
                                                    @endif
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="11">No Order found</td>
                                            </tr>
                                            @endif
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
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                </select>
                                            Per Page</a>
                                        </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-unprocessable" role="tabpanel" aria-labelledby="nav-unprocessable-tab">
                                <div class="table-responsive" style="min-height: 400px;">
                                    <table class="table table-hover mb-0" id="example1">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                                <th>Order Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_DateFilterModal" role="button" aria-expanded="false" aria-controls="U_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_DateFilterModal">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="U_DateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
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

                                                                <button type="reset" data-id="unprocessable_order_data" data-key="start_date,end_date" data-modal="U_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="U_DateFilterModal" data-form="U_DateFilterForm" data-id="unprocessable_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Channel
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="U_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_ChannelFilterModal">
                                                            <label>Filter by Channel</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="U_channelForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="channel">
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="U_custom">
                                                                    <label class="custom-control-label pt-1" for="U_custom">Custom</label>
                                                                </div>
                                                                @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                     <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="U_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="U_{{$c->channel}}">{{$c->channel_name}}</label>
                                                                </div>
                                                                @endforeach
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="channel" data-modal="U_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_ChannelFilterModal" data-form="U_channelForm" data-id="unprocessable_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Order Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_NumberFilterModal" role="button" aria-expanded="false" aria-controls="U_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="U_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_number">
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                                <div class="form-group">
                                                                    <label for="U_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="U_searchByOrderId" placeholder="Enter Order Id Here">
                                                                </div>
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="order_number" data-modal="U_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_NumberFilterModal" data-form="U_orderIdForm" data-id="unprocessable_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br> Status
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_StatusFilterModal" role="button" aria-expanded="false" aria-controls="U_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_StatusFilterModal">
                                                            <label>Filter by Status</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="U_StatusForm">
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
                                                </th>
                                                <th>Amount
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_AmountFilterModal" role="button" aria-expanded="false" aria-controls="U_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_AmountFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="U_OrderAmountForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
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

                                                                <button type="reset" data-id="unprocessable_order_data" data-key="min_value,max_value" data-modal="U_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="U_AmountFilterModal" data-form="U_OrderAmountForm" data-id="unprocessable_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br>Payment
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="U_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_FilterbyPaymentModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="U_PaymentTypeForm">
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
                                                </th>
                                                <th>Product Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#U_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="U_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_FilterbyProductModal">
                                                        <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="U_ProductFormFilter">
                                                                @csrf
                                                                <input type="hidden" name="key" value="product">
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                                <div class="form-group">
                                                                    <label for="U_searchByProduct">Search by Product
                                                                        Name/SKU</label>
                                                                    <input type="text" class="form-control" name="value" id="U_searchByProduct" placeholder="Product Name / SKU">
                                                                </div>
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="product" data-modal="U_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_FilterbyProductModal" data-form="U_ProductFormFilter" data-id="unprocessable_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Customer Details</th>
                                                <th>Pickup Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="U_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_FilterbyPAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="U_PickupAddressForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="pickup_address">
                                                                <input type="hidden" name="filter_status" value="unprocessable_order_data">
                                                                <div class="form-group">
                                                                    <label for="U_searchByPAddress">Search Pickup Address</label>
                                                                    <input type="text" class="form-control" name="value" id=U_searchByPAddress" placeholder="Pickup Address">
                                                                </div>
                                                                <button type="reset" data-id="unprocessable_order_data" data-key="pickup_address" data-modal="U_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="U_FilterbyPAddress" data-form="U_PickupAddressForm" data-id="unprocessable_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Delivery Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#U_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="U_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="U_FilterbyDAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="U_DeliveryAddressForm">
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
                                                </th>
                                                <th>Dimension</th>
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
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                </select>
                                            Per Page</a>
                                        </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-processing" role="tabpanel" aria-labelledby="nav-processing-tab">
                                <div class="table-responsive" style="min-height: 400px;">
                                    <table class="table table-hover mb-0" id="example1">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                                <th>Order Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_DateFilterModal" role="button" aria-expanded="false" aria-controls="P_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_DateFilterModal">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="P_DateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
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

                                                                <button type="reset" data-id="processing_order_data" data-key="start_date,end_date" data-modal="P_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="P_DateFilterModal" data-form="P_DateFilterForm" data-id="processing_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Channel
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="P_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_ChannelFilterModal">
                                                            <label>Filter by Channel</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="P_channelForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="channel">
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="P_custom">
                                                                    <label class="custom-control-label pt-1" for="P_custom">Custom</label>
                                                                </div>
                                                                @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                     <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="P_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="P_{{$c->channel}}">{{$c->channel_name}}</label>
                                                                </div>
                                                                @endforeach
                                                                <button type="reset" data-id="processing_order_data" data-key="channel" data-modal="P_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_ChannelFilterModal" data-form="P_channelForm" data-id="processing_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Order Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_NumberFilterModal" role="button" aria-expanded="false" aria-controls="P_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="P_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_number">
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
                                                                <div class="form-group">
                                                                    <label for="P_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="P_searchByOrderId" placeholder="Enter Order Id Here">
                                                                </div>
                                                                <button type="reset" data-id="processing_order_data" data-key="order_number" data-modal="P_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_NumberFilterModal" data-form="P_orderIdForm" data-id="processing_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br> Status
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_StatusFilterModal" role="button" aria-expanded="false" aria-controls="P_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_StatusFilterModal">
                                                            <label>Filter by Status</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="P_StatusForm">
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
                                                </th>
                                                <th>Amount
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_AmountFilterModal" role="button" aria-expanded="false" aria-controls="P_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_AmountFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="P_OrderAmountForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
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

                                                                <button type="reset" data-id="processing_order_data" data-key="min_value,max_value" data-modal="P_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="P_AmountFilterModal" data-form="P_OrderAmountForm" data-id="processing_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br>Payment
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="P_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_FilterbyPaymentModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="P_PaymentTypeForm">
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
                                                </th>
                                                <th>Product Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#P_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="P_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_FilterbyProductModal">
                                                        <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="P_ProductFormFilter">
                                                                @csrf
                                                                <input type="hidden" name="key" value="product">
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
                                                                <div class="form-group">
                                                                    <label for="P_searchByProduct">Search by Product
                                                                        Name/SKU</label>
                                                                    <input type="text" class="form-control" name="value" id="P_searchByProduct" placeholder="Product Name / SKU">
                                                                </div>
                                                                <button type="reset" data-id="processing_order_data" data-key="product" data-modal="P_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_FilterbyProductModal" data-form="P_ProductFormFilter" data-id="processing_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Customer Details</th>
                                                <th>Pickup Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="P_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_FilterbyPAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="P_PickupAddressForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="pickup_address">
                                                                <input type="hidden" name="filter_status" value="processing_order_data">
                                                                <div class="form-group">
                                                                    <label for="P_searchByPAddress">Search Pickup Address</label>
                                                                    <input type="text" class="form-control" name="value" id=P_searchByPAddress" placeholder="Pickup Address">
                                                                </div>
                                                                <button type="reset" data-id="processing_order_data" data-key="pickup_address" data-modal="P_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="P_FilterbyPAddress" data-form="P_PickupAddressForm" data-id="processing_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Delivery Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#P_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="P_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="P_FilterbyDAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="P_DeliveryAddressForm">
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
                                                </th>
                                                <th>Dimension</th>
                                                <th>Action</th>
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
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                </select>
                                            Per Page</a>
                                        </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-ready-ship" role="tabpanel" aria-labelledby="nav-ready-tab">
                                <div class="table-responsive" style="min-height: 400px;">
                                    <table class="table table-hover mb-0" id="example1">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkManifestButton" value="y"></th>
                                                <th>Order Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_DateFilterModal" role="button" aria-expanded="false" aria-controls="R_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_DateFilterModal">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="R_DateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
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

                                                                <button type="reset" data-id="ready_to_ship_data" data-key="start_date,end_date" data-modal="R_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="R_DateFilterModal" data-form="R_DateFilterForm" data-id="ready_to_ship_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Channel
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="R_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_ChannelFilterModal">
                                                            <label>Filter by Channel</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="R_channelForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="channel">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="R_custom">
                                                                    <label class="custom-control-label pt-1" for="R_custom">Custom</label>
                                                                </div>
                                                                @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                     <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="R_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="R_{{$c->channel}}">{{$c->channel_name}}</label>
                                                                </div>
                                                                @endforeach
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="channel" data-modal="R_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_ChannelFilterModal" data-form="R_channelForm" data-id="ready_to_ship_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Order Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_NumberFilterModal" role="button" aria-expanded="false" aria-controls="R_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_number">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <div class="form-group">
                                                                    <label for="R_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="R_searchByOrderId" placeholder="Enter Order Id Here">
                                                                </div>
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="order_number" data-modal="R_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_NumberFilterModal" data-form="R_orderIdForm" data-id="ready_to_ship_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br> Status
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_StatusFilterModal" role="button" aria-expanded="false" aria-controls="R_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_StatusFilterModal">
                                                            <label>Filter by Status</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="R_StatusForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_status">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">

                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="shipped" id="R_shipped">
                                                                    <label class="custom-control-label pt-1" for="R_shipped">Shipped</label>
                                                                </div>
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="order_status" data-modal="R_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_StatusFilterModal" data-form="R_StatusForm" data-id="ready_to_ship_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Amount
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_AmountFilterModal" role="button" aria-expanded="false" aria-controls="R_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_AmountFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="R_OrderAmountForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
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

                                                                <button type="reset" data-id="ready_to_ship_data" data-key="min_value,max_value" data-modal="R_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="R_AmountFilterModal" data-form="R_OrderAmountForm" data-id="ready_to_ship_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br>Payment
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="R_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_FilterbyPaymentModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="R_PaymentTypeForm">
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
                                                </th>
                                                <th>Product Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#R_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="R_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_FilterbyProductModal">
                                                        <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_ProductFormFilter">
                                                                @csrf
                                                                <input type="hidden" name="key" value="product">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <div class="form-group">
                                                                    <label for="R_searchByProduct">Search by Product
                                                                        Name/SKU</label>
                                                                    <input type="text" class="form-control" name="value" id="R_searchByProduct" placeholder="Product Name / SKU">
                                                                </div>
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="product" data-modal="R_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_FilterbyProductModal" data-form="R_ProductFormFilter" data-id="ready_to_ship_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Customer Details</th>
                                                <th>Pickup Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="R_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_FilterbyPAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_PickupAddressForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="pickup_address">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <div class="form-group">
                                                                    <label for="R_searchByPAddress">Search Pickup Address</label>
                                                                    <input type="text" class="form-control" name="value" id=R_searchByPAddress" placeholder="Pickup Address">
                                                                </div>
                                                                <button type="reset" data-id="filter_order" data-key="delivery_address" data-modal="a_filterbydaddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_FilterbyPAddress" data-form="R_PickupAddressForm" data-id="ready_to_ship_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Delivery Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#R_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="R_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_FilterbyDAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_DeliveryAddressForm">
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
                                                </th>
                                                <th>Shipping Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#R_AWBNumberFilterModal" role="button" aria-expanded="false" aria-controls="R_AWBNumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="R_AWBNumberFilterModal" style="width: 230px !important;">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="R_AWBNumberForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="awb_number">
                                                                <input type="hidden" name="filter_status" value="ready_to_ship_data">
                                                                <div class="form-group">
                                                                    <label for="R_searchByOrderId">Search by AWB Number</label>
                                                                    <input type="text" class="form-control" name="value" id="R_searchByOrderId" placeholder="AWB Number">
                                                                </div>
                                                                <button type="reset" data-id="ready_to_ship_data" data-key="awb_number" data-modal="R_AWBNumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="R_AWBNumberFilterModal" data-form="R_AWBNumberForm" data-id="ready_to_ship_data" class="applyFilterAWBNumber submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
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
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                </select>
                                            Per Page</a>
                                        </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="nav-manifest" role="tabpanel" aria-labelledby="nav-manifest-tab">
                                <div id="manifest_order_data">
                                </div>
                                <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                        <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                        <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                </select>
                                            Per Page</a>
                                        </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-return" role="tabpanel" aria-labelledby="nav-return-tab">
                                <div class="table-responsive" style="min-height: 400px;">
                                    <table class="table table-hover mb-0" id="example1">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                                <th>Order Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_DateFilterModal" role="button" aria-expanded="false" aria-controls="RE_DateFilterModal"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_DateFilterModal">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="RE_DateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="return_order_data">
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

                                                                <button type="reset" data-id="return_order_data" data-key="start_date,end_date" data-modal="RE_DateFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_DateFilterModal" data-form="RE_DateFilterForm" data-id="return_order_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Channel
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_ChannelFilterModal" role="button" aria-expanded="false" aria-controls="RE_ChannelFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_ChannelFilterModal">
                                                            <label>Filter by Channel</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="RE_channelForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <input type="hidden" name="key" value="channel">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="custom" id="RE_custom">
                                                                    <label class="custom-control-label pt-1" for="RE_custom">Custom</label>
                                                                </div>
                                                                @foreach($channel as $c)
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                     <input type="checkbox" class="custom-control-input" name="value[]" value="{{$c->channel}}" id="RE_{{$c->channel}}">
                                                                    <label class="custom-control-label pt-1" for="RE_{{$c->channel}}">{{$c->channel_name}}</label>
                                                                </div>
                                                                @endforeach
                                                                <button type="reset" data-id="return_order_data" data-key="channel" data-modal="RE_ChannelFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_ChannelFilterModal" data-form="RE_channelForm" data-id="return_order_data" class="submit btn btn-primary btm-sm mt-2 ml-0 applyChannelFilter">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Order Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_NumberFilterModal" role="button" aria-expanded="false" aria-controls="RE_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="RE_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <input type="hidden" name="key" value="order_number">
                                                                <div class="form-group">
                                                                    <label for="RE_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="RE_searchByOrderId" placeholder="Enter Order Id Here">
                                                                </div>
                                                                <button type="reset" data-id="return_order_data" data-key="order_number" data-modal="RE_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_NumberFilterModal" data-form="RE_orderIdForm" data-id="return_order_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br> Status
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_StatusFilterModal" role="button" aria-expanded="false" aria-controls="RE_StatusFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_StatusFilterModal">
                                                            <label>Filter by Status</label>
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="RE_StatusForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="order_status">
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="pending" id="RE_pending">
                                                                    <label class="custom-control-label pt-1" for="RE_pending">Pending</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="shipped" id="RE_shipped">
                                                                    <label class="custom-control-label pt-1" for="RE_shipped">Shipped</label>
                                                                </div>
                                                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                    <input type="checkbox" class="custom-control-input" name="value[]" value="cancelled" id="RE_cancelled">
                                                                    <label class="custom-control-label pt-1" for="RE_cancelled">Cancelled</label>
                                                                </div>
                                                                <button type="reset" data-id="return_order_data" data-key="order_status" data-modal="RE_StatusFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_StatusFilterModal" data-form="RE_StatusForm" data-id="return_order_data" class="applyStatusFilter submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Amount
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_AmountFilterModal" role="button" aria-expanded="false" aria-controls="RE_AmountFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_AmountFilterModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="RE_OrderAmountForm">
                                                                @csrf
                                                                <input type="hidden" name="filter_status" value="return_order_data">
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

                                                                <button type="reset" data-id="return_order_data" data-key="min_value,max_value" data-modal="RE_AmountFilterModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_AmountFilterModal" data-form="RE_OrderAmountForm" data-id="return_order_data" class="applyFilterAmount btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br>Payment
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_FilterbyPaymentModal" role="button" aria-expanded="false" aria-controls="RE_FilterbyPaymentModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_FilterbyPaymentModal">
                                                            <form action="{{route('seller.set_filter')}}" method="post" id="RE_PaymentTypeForm">
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
                                                </th>
                                                <th>Product Details
                                                <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_FilterbyProductModal" role="button" aria-expanded="false" aria-controls="RE_FilterbyProductModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_FilterbyProductModal">
                                                        <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="RE_ProductFormFilter">
                                                                @csrf
                                                                <input type="hidden" name="key" value="product">
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <div class="form-group">
                                                                    <label for="RE_searchByProduct">Search by Product
                                                                        Name/SKU</label>
                                                                    <input type="text" class="form-control" name="value" id="RE_searchByProduct" placeholder="Product Name / SKU">
                                                                </div>
                                                                <button type="reset" data-id="return_order_data" data-key="product" data-modal="RE_FilterbyProductModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_FilterbyProductModal" data-form="RE_ProductFormFilter" data-id="return_order_data" class="applyFilterProduct submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Customer Details</th>
                                                <th>Pickup Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_FilterbyPAddress" role="button" aria-expanded="false" aria-controls="RE_FilterbyPAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_FilterbyPAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="RE_PickupAddressForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="pickup_address">
                                                                <input type="hidden" name="filter_status" value="return_order_data">
                                                                <div class="form-group">
                                                                    <label for="RE_searchByPAddress">Search Pickup Address</label>
                                                                    <input type="text" class="form-control" name="value" id=RE_searchByPAddress" placeholder="Pickup Address">
                                                                </div>
                                                                <button type="reset" data-id="return_order_data" data-key="pickup_address" data-modal="RE_FilterbyPAddress" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="RE_FilterbyPAddress" data-form="RE_PickupAddressForm" data-id="return_order_data" class="applyFilterPickupAddress submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th>Delivery Address
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#RE_FilterbyDAddress" role="button" aria-expanded="false" aria-controls="RE_FilterbyDAddress"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="RE_FilterbyDAddress">
                                                            <form action="{{route('seller.set_filter')}}" class=" mt-0" method="post" id="RE_DeliveryAddressForm">
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
                                                </th>
                                                <th>Dimension</th>
                                                <th>Action</th>
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
                                        <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" id="txtPageCount" style="width: 4%; text-align:center"></a>
                                        <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                                        <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                                        <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                                        <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                                        <div class="float-right">
                                            <a>Show
                                                <select name="per_page_record" class="perPageRecord">
                                                    <option value="20">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
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
                <form id="order_form" class="needs-validation" novalidate method="post" action="{{route('seller.add_order')}}">
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
                                        <label>Order Number</label>
                                        <input type="text" class="form-control" placeholder="Order Id" id="order_number" name="order_number" value="{{'1000' + $total_order+1}}" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Type</label>
                                        <div class="input-group mb-3">
                                            <select class="custom-select" id="order_type" name="order_type" required>
                                                <!-- <option value="" disabled>Payment Type</option> -->
                                                <option value="cod" selected id="type_cod">Cash on Delivery</option>
                                                <option value="prepaid" id="type_prepaid">Prepaid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="mb-3">Order Type</label><br>
                                        <input type="radio" name="o_type" value="forward" checked id="o_type_forward"> Forward
                                        <input type="radio" name="o_type" value="reverse" class="ml-3" id="o_type_reverse"> Reverse
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary pull-right" id="orderTabButton" type="button">Next</button>
                        </div>
                    </div>
                    <div class="card mb-1">
                        <div class="card-header">
                            <h5>Shipping Information</h5>
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
                                        <input type="text" class="form-control" placeholder="Country" id="txtCountry" name="country" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" class="form-control" placeholder="State" id="state" name="state" required readonly>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control" placeholder="City" id="city" name="city" required readonly>
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
                                <table class="table table-hover" id="item_table">
                                    <thead>
                                        <tr>
                                            <th>SKU (Optional)</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Field</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product_details">
                                        <tr>
                                            <td><input type="text" data-id="1" id="product_sku1" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU" /></td>
                                            <td><input type="text" data-id="1" id="product_name1" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name" /></td>
                                            <td><input type="text" data-id="1" id="product_qty1" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" /></td>
                                            </td>
                                            <td>
                                                <button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                        <input type="number" class="form-control" placeholder="Length" id="length" name="length" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="breadth">Breadth (cm)</label>
                                        <input type="number" class="form-control" placeholder="Breadth" id="breadth" name="breadth" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height">Height (cm)</label>
                                        <input type="number" class="form-control" placeholder="Height" id="height" name="height" required>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shipping Charges</label>
                                        <input type="number" class="form-control" placeholder="Shiping Charges" id="shipping_charges" name="shipping_charges">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>COD charges</label>
                                        <input type="number" class="form-control" placeholder="Cod Charges" id="cod_charges" name="cod_charges">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Discount</label>
                                        <input type="number" class="form-control" placeholder="Discount" id="discount" name="discount">
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
                            <h5>Warehouse Information</h5>
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
                <form method="post" action="{{route('seller.import_csv_order')}}" enctype="multipart/form-data" id="bulkimportform">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 pb-10 mb-2">
                                Download sample order upload file : <a class="text-info" href="{{url('public/assets/seller/Twinnship.csv')}}">Download</a>
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
                <form action="{{route('seller.single_ship_order')}}" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="order_id_single">
                        @csrf
                        <input type="hidden" id="shipping_charge_single" name="shipping_charge_single">
                        <input type="hidden" id="cod_charge_ship" name="cod_charge_single">
                        <input type="hidden" id="early_cod_charge" name="early_cod_charge" value="">
                        <input type="hidden" id="gst_charge" name="gst_charge" value="">
                        <input type="hidden" id="rto_charge_single" name="rto_charge" value="">
                        <input type="hidden" id="total_charge" name="total_charge" value="">
                        <input type="hidden" id="order_zone" name="order_zone">

                        <input type="hidden" id="session_rto_charge" value="{{Session()->get('MySeller')->rto_charge}}">
                        <input type="hidden" id="session_early_cod" value="{{Session()->get('MySeller')->early_cod_charge}}">


                        <div class="row" id="partners">
                            <div class="col-md-12">
                                <div class="form-row pt-3">
                                    <div class="custom-control custom-radio col-sm-12">
                                        @foreach($partners as $key=>$p)
                                        <div class="card mb-2 p-4">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input type="radio" required="" id="partner_{{$p->id}}" name="partner" data-id="{{$p->id}}" class="ml-2 custom-control-input partner_select" value="{{$p->keyword}}" {{$key=='0'?'checked':'none'}}>
                                                    <label class="custom-control-label h6 mb-2" for="partner_{{$p->id}}">{{$p->title}}</label><br>
                                                    <img src="{{asset($p->image)}}" style="height: 100px;border-radius:5px;">
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="set-size charts-container">
                                                                <div class="pie-wrapper progress-75 style-2">
                                                                    <span class="label">4.5</span>
                                                                    <div class="pie">
                                                                        <div class="left-side half-circle"></div>
                                                                        <div class="right-side half-circle"></div>
                                                                    </div>
                                                                    <div class="shadow">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="f-11 text-center">Pickup Performance</p>
                                                        </div>
                                                        <div class="col">
                                                            <div class="set-size charts-container">
                                                                <div class="pie-wrapper progress-75 style-2">
                                                                    <span class="label">4.1</span>
                                                                    <div class="pie">
                                                                        <div class="left-side half-circle"></div>
                                                                        <div class="right-side half-circle"></div>
                                                                    </div>
                                                                    <div class="shadow">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="f-11 text-center">Delivery Performance</p>
                                                        </div>
                                                        <div class="col">
                                                            <div class="set-size charts-container">
                                                                <div class="pie-wrapper progress-75 style-2">
                                                                    <span class="label">4.2</span>
                                                                    <div class="pie">
                                                                        <div class="left-side half-circle"></div>
                                                                        <div class="right-side half-circle"></div>
                                                                    </div>
                                                                    <div class="shadow">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="f-11 text-center">NDR Performance</p>
                                                        </div>
                                                        <div class="col">
                                                            <div class="set-size charts-container">
                                                                <div class="pie-wrapper progress-75 style-2">
                                                                    <span class="label">4.2</span>
                                                                    <div class="pie">
                                                                        <div class="left-side half-circle"></div>
                                                                        <div class="right-side half-circle"></div>
                                                                    </div>
                                                                    <div class="shadow">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="f-11 text-center">RTO Performance</p>
                                                        </div>
                                                        <div class="col">
                                                            <div class="set-size charts-container">
                                                                <div class="pie-wrapper progress-75 style-2">
                                                                    <span class="label">4.2</span>
                                                                    <div class="pie">
                                                                        <div class="left-side half-circle"></div>
                                                                        <div class="right-side half-circle"></div>
                                                                    </div>
                                                                    <div class="shadow">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="f-11 text-center">Overall Rating</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="grey-light">
                                                        <p class="mb-0 h-6 font-weight-bold" style="font-size: 21px;">₹
                                                        <span id="total_charge_{{$p->id}}"></span>
                                                        <button type="submit" class="btn btn-info btn-sm float-right ShipOrderBtn" style="margin-top:-8px;">
                                                            Ship
                                                        </button></p>
                                                        <p class="mb-0 f-14">Freight Charges: <b>₹
                                                            <span id="shipping_charge_{{$p->id}}"></span></b></p>
                                                        <p class="mb-0 f-14">+ COD Charges: <b>₹
                                                            <span id="cod_charge_{{$p->id}}"></span></b></p>

                                                        <p class="mb-0 f-14">+ Early COD Charges: <b>₹ <span id="early_cod_charge_{{$p->id}}"></span></b>
                                                        </p>
                                                        <p style="display:none;" class="mb-0 f-14">+ GST Charges(18%): <b>₹ <span id="gst_charge_{{$p->id}}"></span></b>
                                                        </p>
                                                        <p class="mb-0 f-14">RTO Charges: <b>₹ <span id="rto_charge_{{$p->id}}"></span></b>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- Total Order Modal -->
    <div class="modal fade" id="allOrderDetail" tabindex="-1" role="dialog" aria-labelledby="allOrderDetail" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Shipment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <h6>No of Orders : <span id="total_selected_order"></span></h6>
                        <h6>Total Freight Charge : <span id="total_freight_charge"></span></h6>
                        <hr>
                        <h6>Your Total Balance is : <span id="seller_balance"></span></h6>
                        <h6>Your Available Shipment Balance is : <span id="available_balance"></span></h6>
                        <small id="error_message" class="text-danger">Insufficient Balance to Ship Order</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm MultiShipButton" disabled>Ship</button>
            </div>
            </div>
        </div>
    </div>

    @include('seller.pages.scripts')


<script type="text/javascript">

    $('[data-toggle="popover"]').popover();

    var pageCount=1,currentTab='all_order',isFilter = false,selectedTab='{{isset($_GET['tab'])?$_GET['tab']:"all_orders"}}',totalRecord = 0,perpageLimit = 0;
    $(document).click(function(event) {
    //if you click on anything except the modal itself or the "open modal" link, close the modal
    if (!$(event.target).closest(".filter,.filter-collapse").length) {
        $("body").find(".filter-collapse").removeClass("show");
    }
    });
    var del_ids = [], cnt = 1;



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
                $('#'+divId).html(response);
                // $('#total_'+divId).html($('#total_ajax').val());
                $('#order_count').html($('#total_ajax').val());
                hideOverlay();
            }
        });
    }

    // $('#total_filter_order').html($('#total_order_count').val());

    $(document).ready(function () {

        perpageLimit=$('.perPageRecord').val();

        $('#o_type_reverse').click(function(){
                $('#type_prepaid').prop("selected", true);
                $('#type_cod').prop("disabled", true);
        });

        $('#o_type_forward').click(function(){
                $('#type_cod').prop("disabled", false);
        });


        //for pagination page number searching
        $('#nav-tabContent').on('keyup', '#txtPageCount', function (e) {
        // $('#txtPageCount').keyup(function(e){
            if(e.keyCode == 13){
                if(parseInt($(this).val().trim()) <= parseInt($('.totalPage').html()) ){
                    showOverlay();
                    pageCount = parseInt($(this).val().trim());
                    fetch_orders();
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
            showOverlay();
        });

        $('#courier_partner_select').on('click', '.ShipOrderBtn', function () {
            showOverlay();
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

        $('#data_div').on('click', '.FetchOrder', function () {
            showOverlay();
                $.ajax({
                    url: '{{route('seller.fetch_all_orders')}}',
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

        $('#nav-tabContent').on('click', '.clone_data', function () {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/clone_order/"}}' + $(this).data('number'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#customer_name').val(info.order.b_customer_name);
                    $('#contact').val(info.order.b_contact);
                    $('#address').val(info.order.b_address_line1);
                    $('#address2').val(info.order.b_address_line2);
                    $('#pincode').val(info.order.b_pincode);
                    $('#txtCountry').val(info.order.b_country);
                    $('#state').val(info.order.b_state);
                    $('#city').val(info.order.b_city);
                    $('#weight').val(info.order.weight/1000);
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

                    $('#product_details').html('');
                    for (var i = 0; i < info.product.length; i++) {
                        add_row_update(i);
                    }

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
                    $('#discount').val(info.order.discount);
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

        $('#nav-tabContent').on('click', '.reverse_data', function () {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/clone_order/"}}' + $(this).data('number'),
                success: function (response) {
                    var info = JSON.parse(response);
                    $('#customer_name').val(info.order.b_customer_name);
                    $('#contact').val(info.order.b_contact);
                    $('#address').val(info.order.b_address_line1);
                    $('#address2').val(info.order.b_address_line2);
                    $('#pincode').val(info.order.b_pincode);
                    $('#txtCountry').val(info.order.b_country);
                    $('#state').val(info.order.b_state);
                    $('#city').val(info.order.b_city);
                    $('#weight').val(info.order.weight/1000);
                    $('#height').val(info.order.height);
                    $('#length').val(info.order.length);
                    $('#breadth').val(info.order.breadth);
                    $('#type_cod').prop('disabled', true);
                    $('#type_prepaid').prop('selected', true);
                    $('#o_type_forward').prop('disabled', true);
                    $('#o_type_reverse').prop('checked', true);

                    $('#product_details').html('');
                    for (var i = 0; i < info.product.length; i++) {
                        add_row_update(i);
                    }

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
                    $('#discount').val(info.order.discount);
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
                if(isFilter){
                get_value_filter();
                }else{
                    all_order();
                }
            }
            $('.currentPage').val(pageCount);
        }

        $('.firstPageButton').click(function(){
            if(pageCount > 1){
                pageCount = 1 ;
                showOverlay();
                fetch_orders();
            }

        });

        $('.previousPageButton').click(function(){
            if(pageCount > 1){
                pageCount--;
                showOverlay();
                fetch_orders();
            }
        });

        $('.nextPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount++;
                showOverlay();
                fetch_orders();
            }
        });

        $('.lastPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount = $('.totalPage').html();
            showOverlay();
            fetch_orders();
            }
        });


        $('#nav-tabContent').on('change', '.perPageRecord', function () {
            showOverlay();
            var page = $(this).val();
            $.ajax({
                url: '{{url('/')."/per_page_record/"}}' +page,
                success: function (response) {
                    // console.log('done');
                    hideOverlay();
                    if(page >= parseInt($('#order_count').html()))
                        $('.order_display_limit').html(parseInt($('#order_count').html()));
                    else
                        $('.order_display_limit').html(page);
                    fetch_orders();
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='unprocessable';
             showOverlay();
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#cancelSelectButton').hide();
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
                    // console.log(response);
                    $('#unprocessable_order_data').html(response);
                    $('#order_count').html($('#total_unproccessed').val());
                    $('.totalPage').html(Math.ceil($('#total_unproccessed').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='all_order';
             showOverlay();
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#cancelSelectButton').hide();
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
                    $('#order_count').html($('#total_order_count').val());
                    $('.totalPage').html(Math.ceil($('#total_order_count').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='processing';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#cancelSelectButton').hide();
            processing_order();
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
                    $('#order_count').html($('#total_process_order').val());
                    $('.totalPage').html(Math.ceil($('#total_process_order').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='ready_to_ship';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#cancelSelectButton').hide();
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
                    $('#order_count').html($('#total_shipped_order').val());
                    $('.totalPage').html(Math.ceil($('#total_shipped_order').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='manifest';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
            //hide ship button
            $('#removeAllButton').hide();
            $('#shipAllButton').hide();
            $('#cancelSelectButton').hide();
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
                    $('#order_count').html($('#total_manifest_order').val());
                    $('.totalPage').html(Math.ceil($('#total_manifest_order').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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
            pageCount=1;
            $('.currentPage').val(pageCount);
            currentTab='return';
            sel_ids = [];
            $(".selectedCheck").prop('checked', false);
                //hide ship button
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#cancelSelectButton').hide();
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
                    $('#order_count').html($('#total_return_order').val());
                    $('.totalPage').html(Math.ceil($('#total_return_order').val() / $('.order_display_limit').html()));
                    var pageCounts=parseInt($('.order_display_limit').html());
                    if(parseInt($('#total_unproccessed').val()) < pageCounts)
                        $('.order_display_limit').html(parseInt($('#total_unproccessed').val()));
                    else
                        $('.order_display_limit').html(perpageLimit);
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


        //Fetch  dimension data using weight
        $('#form_div').on('blur', '.product_sku', function () {
            showOverlay();
            var that = $(this);

            $.ajax({
                url: '{{url('/')."/fetch_product_sku/"}}' + that.val(),
                success: function (response) {
                    hideOverlay();
                    if(response != '0'){
                        var info = JSON.parse(response);
                        $('#product_name'+that.data('id')).val(info.product_name);
                        $('#product_unitprice'+that.data('id')).val(info.product_price);
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
        });

        //Fetch  dimension data using weight
        $('#form_div').on('blur', '.weightfield', function () {
            showOverlay();
            var that = $(this);

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
                    $.notify(" Success... Dimension Updated Successfully", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "success",
                        icon: "check"
                    });
                    $('#dimensionModal').modal('hide');
                    $('#filter_order').html(response);
                    $('#unprocessable_order_data').html(response);
                    $('#ready_to_ship_data').html(response);
                    $('#processing_order_data').html(response);
                    $('#shipped_order_data').html(response);
                    $('#return_order_data').html(response);
                    countOrder();
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
                    $('#order_number').val(info.order.order_number);
                    $('#customer_name').val(info.order.b_customer_name);
                    // $('#country').val(info.order.b_contact_code);
                    $('#contact').val(info.order.b_contact);
                    $('#address').val(info.order.b_address_line1);
                    $('#address2').val(info.order.b_address_line2);
                    $('#pincode').val(info.order.b_pincode);
                    $('#txtCountry').val(info.order.b_country);
                    $('#state').val(info.order.b_state);
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

                    $('#product_details').html('');
                    for (var i = 0; i < info.product.length; i++) {
                        add_row_update(i);
                    }

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
            $('.all_tabs').slideUp();
            $('#shipping_tab').slideDown();
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
            $('input[name="weight"]').valid();
            $('input[name="length"]').valid();
            $('input[name="height"]').valid();
            $('input[name="breadth"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#other_tab').slideDown();
            }
        });
        $('#WarehouseTabButton').click(function () {
            $('input[name="invoice_amount"]').valid();
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
            ++cnt;
            add_row(cnt)
        });

        function add_row(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            $('#item_table').append(html);
        }

        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity"/></td>';
            if(cnt == 0){
                html += '<td><button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button></td></tr>';
            }else{
                html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            }
            $('#item_table').append(html);
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
        });

        $('.BackButton').click(function() {
            // $('.card-body').show();
            $('#data_div').show();
            $('#form_div').hide();
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

        $('#partners').on('click', '.partner_select', function () {
            var that = $(this);
            $('#shipping_charge_single').val($('#shipping_charge_' + that.data('id')).html());
            $('#cod_charge_ship').val($('#cod_charge_' + that.data('id')).html());
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
                        $.notify(" Order has been deleted.", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                        $('#row' + that.data('id')).remove();
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
            var seller_rto = $('#session_rto_charge').val();
            var seller_early_cod = $('#session_early_cod').val();
            if (that.data('status') == 'pending') {
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
                            $.notify(" Oops... Please add Deafult Warehouse!!", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }else{
                            var cod_charge=0,early_cod=0;
                            $('#courier_partner_select').modal('show');
                            for (var i = 0; i < response.rates.length; i++) {
                                if(response.order_type.toLowerCase() === 'cod'){
                                    cod_charge=(response.invoice_amount * response.rates[i].cod_maintenance ) / 100;
                                    if(cod_charge < response.rates[i].cod_charge)
                                        cod_charge = response.rates[i].cod_charge;
                                    cod_charge = cod_charge + (cod_charge * 18 / 100);
                                    early_cod = (response.invoice_amount * seller_early_cod) / 100;
                                    early_cod = early_cod + (early_cod * 18 / 100);
                                }
                                // alert(cod_charge);
                                var shipping_charge = response.rates[i].price ;
                                shipping_charge += shipping_charge * 18 / 100;
                                // console.log(shipping_charge);
                                var gst_charge = (shipping_charge + cod_charge + early_cod) * 18 / 100;
                                var rto_charge = (shipping_charge + cod_charge + early_cod ) * seller_rto / 100;
                                $('#shipping_charge_' + response.rates[i].partner_id).html(Math.round(shipping_charge).toFixed(2));
                                $('#cod_charge_' + response.rates[i].partner_id).html(cod_charge);
                                $('#total_charge_' + response.rates[i].partner_id).html(Math.round(shipping_charge + cod_charge + early_cod).toFixed(2));
                                $('#rto_charge_' + response.rates[i].partner_id).html(Math.round(rto_charge).toFixed(2));
                                $('#early_cod_charge_' + response.rates[i].partner_id).html(early_cod);
                                $('#gst_charge_' + response.rates[i].partner_id).html(Math.round(gst_charge).toFixed(2));

                                if (i == 0) {
                                    //alert(shipping_charge + " " + cod_charge + " " + early_cod);
                                    $('#shipping_charge_single').val(Math.round(shipping_charge).toFixed(2));
                                    $('#cod_charge_ship').val(Math.round(cod_charge).toFixed(2));
                                    $('#early_cod_charge').val(Math.round(early_cod).toFixed(2));
                                    $('#gst_charge').val(Math.round(gst_charge).toFixed(2));
                                    $('#rto_charge_single').val(Math.round(rto_charge).toFixed(2));
                                    $('#total_charge').val(Math.round(shipping_charge + cod_charge + early_cod).toFixed(2));
                                }

                            }
                            $('#order_id_single').val(that.data('id'));
                            $('#order_zone').val(response.zone);
                            hideOverlay();
                        }
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something want wrong!", {
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
                $.notify(" Oops... Something want wrong", {
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


        $('#nav-tabContent').on('click', '#checkAllButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                // $('.selectedCheck').trigger("click");
                $('.selectedCheck').prop('checked', true);

                $('#removeAllButton').fadeIn();
                $('#shipAllButton').fadeIn();
                $('#cancelSelectButton').fadeIn();
                $('.total_order_selected').html($('.total_order_display').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#cancelSelectButton').hide();
                $('.total_order_selected').html(0);
            }
            updateSelectedCounter();
        });

        $('#nav-tabContent').on('click', '#checkManifestButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                $('.ManifestCheck').prop('checked', true);
                $('#ManifestSelectButton').fadeIn();
                $('#LabelSelectButton').fadeIn();
                $('#InvoiceSelectButton').fadeIn();
                $('.total_order_selected').html($('.total_order_display').html());
            } else {
                $('.ManifestCheck').prop('checked', false);
                $('#ManifestSelectButton').hide();
                $('#LabelSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('.total_order_selected').html(0);
            }
            updateSelectedCounterManifest();
        });

        $('#nav-tabContent').on('click', '.selectedCheck', function () {
            var cnt = 0;
            var that = $(this);
            $('.selectedCheck').each(function () {
                if ($(this).prop('checked'))
                    cnt++;
            });
            if (cnt > 0) {
                if(that.data('status') == 'cancelled'){
                  $('#shipAllButton').hide();
                  $('#removeAllButton').fadeIn();
                }else{
                    $('#removeAllButton').fadeIn();
                    $('#shipAllButton').fadeIn();
                    $('#cancelSelectButton').fadeIn();
                }
            } else {
                $('#removeAllButton').hide();
                $('#shipAllButton').hide();
                $('#cancelSelectButton').hide();
            }
            // console.log(cnt);
            $('.total_order_selected').html(cnt);
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
            } else {
                $('#ManifestSelectButton').hide();
                $('#InvoiceSelectButton').hide();
                $('#LabelSelectButton').hide();
            }
            $('.total_order_selected').html(cnt);
        });


        //MANIFEST GENERATE
        $('#ManifestSelectButton').click(function () {
            order_ids = [];
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                order_ids.push($(this).val());
            });
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

        //Download Selected Invoice
        $('#InvoiceSelectButton').click(function () {
            order_ids = [];
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                order_ids.push($(this).val());
            });
            $('#multiinvoice_id').val(order_ids);
            $('#MultiInvoiceForm').submit();
        });

        //Download Selected Label
        $('#LabelSelectButton').click(function () {
            order_ids = [];
            $('.ManifestCheck').each(function () {
                if ($(this).prop('checked'))
                order_ids.push($(this).val());
            });
            $('#multilable_id').val(order_ids);
            $('#MultilabelForm').submit();
        });


        $('#removeAllButton').click(function () {
            del_ids = [];
            $('.selectedCheck').each(function () {
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
                    url: '{{url('/')."/remove-selected-order"}}',
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

        $('#cancelSelectButton').click(function () {
            cancel_ids = [];
            $('.selectedCheck').each(function () {
                if ($(this).prop('checked'))
                    cancel_ids.push($(this).val());
            });
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
                        hideOverlay();
                        // $.notify("Order has been Cancelled.", {blur: 0.2, delay: 0});
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


        $('#shipAllButton').click(function () {
            sel_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked') && $(this).data('status') == 'pending') {
                    sel_ids.push($(this).val());
                }
            });
            if (window.confirm("Are you sure want to Ship selected Order?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': sel_ids
                    },
                    url: '{{url('/')."/total-selected-order"}}',
                    success: function (response) {
                    if(response == 0){
                        $.notify(" Oops... Please Select Order First!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }else if (response == 1){
                        $.notify(" Oops...Please Add Default Courier Partner..!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }else if (response == 2){
                        $.notify(" Oops...Please Add Proper Dimension..!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }else{
                        var required_balance = response.seller_balance - response.minimum_balance
                        $('#allOrderDetail').modal('show');
                        $('#total_selected_order').html(response.total_order);
                        $('#total_freight_charge').html(response.all_order_charge);
                        $('#seller_balance').html(parseFloat(response.seller_balance).toFixed(2));
                        $('#available_balance').html(required_balance);
                        if(response.all_order_charge <= required_balance){
                            $('#error_message').html('');
                            $('.MultiShipButton').prop('disabled',false);
                        }
                    }
                        hideOverlay();
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

          $('.MultiShipButton').click(function () {
            sel_ids = [];
            $('.selectedCheck').each(function () {
                if ($(this).prop('checked') && $(this).data('status') == 'pending') {
                    sel_ids.push($(this).val());
                }
            });
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
                        if(response == 'false'){
                            $.notify(" Oops... Insufficient Balance!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                             });
                        }else if(response == 0){
                            $.notify(" Oops... Wow Express not able to Shipped Order Please add Proper Order Details!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                             });
                        }else{
                             location.reload();
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
            "product_name[]": {
                required: true
            },
            weight: {
                required: true
            },
            length: {
                required: true
            },
            breadth: {
                required: true
            },
            height: {
                required: true
            },
            invoice_amount: {
                required: true
            },
            warehouse: {
                required: true
            },
        },
        messages: {
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
        switch(selectedTab){
            case 'new_order':
                $('.addInfoButton ').click();
                break;
            case 'add_reverse_order':
                $('.addInfoButton ').click();
                $('#o_type_reverse').prop('checked', true);
                $('#type_prepaid').prop("selected", true);
                $('#type_cod').prop("disabled", true);
                break;
            case 'unprocessable':
                $('#nav-unprocessable-tab').click();
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
                break;
            case 'return_orders':
                $('#nav-return-tab').click();
                break;
            case 'reverse_orders':
                all_reverse_order();
                break;
            default:
                $('#nav-all_orders-tab').click();

                break;
        }
    });

    function all_reverse_order(){
            showOverlay()
            $.ajax({
                method : 'get',
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.all_reverse_order')}}',
                success: function (response) {
                    $('#filter_order').html(response);
                    $('#total_filter_order').html($('#total_reverse_order').val());
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

</script>
</body>
</html>
