<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Billing | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }

        .badge-pill:hover {
            font-size: 12px;
        }
        .mycard {
            background: #093a5a;
            color: white;
            border-radius: 5px;
            padding: 5px !important;
        }
        .nav-pills .nav-link.active, .nav-pills .show>.nav-link{
            background-color: #073D59;
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
                <div class="card-body">
                    <h3 class="h4">Weight Reconciliation
                        <div class="float-right">
                            <!-- <span data-toggle="modal" data-target="#earlyCodModal"><button type="button" class="btn btn-success btn-sm mx-0"><i class="fa fa-money-check"></i> Early COD</button></span> -->
                            <button type="button" class="btn btn-primary btn-sm mx-0 exportBtn" id="exportBtn" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                            <button type="button" class="btn btn-info btn-sm mx-0 MultipleAcceptButton" id="MultiAcceptButton" style="display: none;" data-placement="top" data-toggle="tooltip" data-original-title="Accept"><i class="fa fa-check-square"></i></button>
                        </div>
                    </h3>
                    <br>
                    <span class="float-right data-counter" style="display: inline-block;">
                            <p class="mb-0 h6 f-14">Showing <span class="billing_display_limit"></span> of <span id="billing_count"></span></p>
                            <p class="mb-0 h6 f-14">Selected <span class="total_billing_selected">0</span> out of <span class="billing_display_limit"></span></p>
                        </span>
                    <br>
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link" id="nav-weight-tab" data-toggle="tab" href="#nav-weight" role="tab" aria-controls="nav-weight" aria-selected="false"><i class="far fa-balance-scale-right"></i> Weight Reconciliation <span class="badge badge-pill badge-danger" id="weight_reconciliation_badge">0</span> </a>
                            <a class="nav-item nav-link" id="nav-on_hold-tab" data-toggle="tab" href="#nav-on_hold" role="tab" aria-controls="nav-on_hold" aria-selected="false"><i class="far fa-pause"></i> On-Hold Reconciliation <span class="badge badge-pill badge-danger" id="onhold_reconciliation_badge">0</span> </a>
                            <a class="nav-item nav-link" id="nav-settle-tab" data-toggle="tab" href="#nav-settle" role="tab" aria-controls="nav-weight" aria-selected="false"><i class="far fa-handshake-o"></i> Settle Reconciliation <span class="badge badge-pill badge-success" id="weight_reconciliation_badge">0</span> </a>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">

                        <div class="tab-pane fade show" id="nav-weight" role="tabpanel" aria-labelledby="nav-weight-tab">
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_NumberFilterModal" role="button" aria-expanded="false" aria-controls="W_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="W_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="W_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="b_order_number">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="form-group">
                                                                <label for="W_searchByOrderId">Search by Order Number</label>
                                                                <input type="text" class="form-control" name="value" id="W_searchByOrderId" placeholder="Enter Order Number Here">
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="b_order_number" data-modal="W_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_NumberFilterModal" data-form="W_orderIdForm" data-id="weight_reconcilication_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_NDRDateFilter" role="button" aria-expanded="false" aria-controls="W_NDRDateFilter"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_NDRDateFilter">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter_billing')}}" method="post" id="W_NDRDateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="billing_start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" name="billing_end_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="billing_start_date,billing_end_date" data-modal="W_NDRDateFilter" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="W_NDRDateFilter" data-form="W_NDRDateFilterForm" data-id="weight_reconcilication_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Channel</th>
                                        <th>Product Details</th>
                                        <th>Order Total</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Shipping Details
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_AWBNumberFilter" role="button" aria-expanded="false" aria-controls="W_AWBNumberFilter"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_AWBNumberFilter" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="W_awbnumberForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="b_awb_number">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="form-group">
                                                                <label for="W_searchByOrderId">Search by AWB Number</label>
                                                                <input type="text" class="form-control" name="value" id="W_searchByOrderId" placeholder="Enter AWB Number Here">
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="b_awb_number" data-modal="W_AWBNumberFilter" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_AWBNumberFilter" data-form="W_awbnumberForm" data-id="weight_reconcilication_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Courier Partner
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_CourierFilterModal" role="button" aria-expanded="false" aria-controls="W_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                        <label>Filter by Courier Partner</label>
                                                        <form action="{{route('seller.set_filter_billing')}}" class="filterForm mt-0" method="post" id="W_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                                @foreach($partners as $p)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="W_c_{{$p->keyword}}">
                                                                        <label class="custom-control-label pt-1" for="W_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="courier_partner" data-modal="W_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_CourierFilterModal" data-form="W_CourierForm" data-id="weight_reconcilication_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Entered Weight &<br> Dimensions (CM)</th>
                                        <th>Charged Weight &<br> Dimensions (CM)</th>
                                        <th>Settled Weight &<br> Dimensions (CM)</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_FilterbyStatusModal" role="button" aria-expanded="false" aria-controls="W_FilterbyStatusModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_FilterbyStatusModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" method="post" id="W_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="weight_rec_status">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <label>Filter Status</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="discrepancy" id="W_discrepancy">
                                                                <label class="custom-control-label pt-1" for="W_discrepancy">New Discrepancy</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="dispute_raised" id="W_raised">
                                                                <label class="custom-control-label pt-1" for="W_raised">Dispute Raised</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="accepted" id="W_discrepancy_accepted">
                                                                <label class="custom-control-label pt-1" for="W_discrepancy_accepted">Discrepancy Accepted</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="rejected_courier" id="W_rejected_courier">
                                                                <label class="custom-control-label pt-1" for="W_rejected_courier">Dispute Rejected by Courier</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="accepted_courier" id="W_accepted_courier">
                                                                <label class="custom-control-label pt-1" for="W_accepted_courier">Dispute Accepted by Courier</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="auto_accepted" id="W_auto_accept">
                                                                <label class="custom-control-label pt-1" for="W_auto_accept">Auto Accept</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="se_credit" id="W_se_credit">
                                                                <label class="custom-control-label pt-1" for="W_se_credit">SE Credit</label>
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="weight_rec_status" data-modal="W_FilterbyStatusModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="W_FilterbyStatusModal" data-form="W_StatusForm" data-id="weight_reconcilication_data" class="applyStatusFilter btn btn-primary mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="weight_reconcilication_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" disabled style="width: 4%; text-align:center"></a>
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


                        <div class="tab-pane fade show" id="nav-on_hold" role="tabpanel" aria-labelledby="nav-on_hold-tab">
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                            <th>
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                    AWB Assigned Date
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#O_NDRDateFilter" role="button" aria-expanded="false" aria-controls="O_NDRDateFilter"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                        <div class="collapse filter-collapse ml-5" id="O_NDRDateFilter">
                                                            <label>Filter by Date</label>
                                                            <form action="{{route('seller.set_filter_billing')}}" method="post" id="O_NDRDateFilterForm">
                                                                @csrf
                                                                <input type="hidden" name="billing_filter_type" value="onhold">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Start Date</label>
                                                                            <input type="date" class="form-control" name="w_start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>End Date</label>
                                                                            <input type="date" class="form-control" name="w_end_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <button type="reset" data-id="onhold_data" data-key="w_start_date,w_end_date" data-modal="O_NDRDateFilter" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                    Reset
                                                                </button>
                                                                <button type="button" data-modal="O_NDRDateFilter" data-form="O_NDRDateFilterForm" data-id="onhold_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                    Order Id
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#O_NumberFilterModal" role="button" aria-expanded="false" aria-controls="O_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="O_NumberFilterModal">
                                                            <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="O_orderIdForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="b_order_number">
                                                                <input type="hidden" name="billing_filter_type" value="onhold">
                                                                <div class="form-group">
                                                                    <label for="O_searchByOrderId">Search by Order Number</label>
                                                                    <input type="text" class="form-control" name="value" id="O_searchByOrderId" placeholder="Enter Order Number Here">
                                                                </div>
                                                                <button type="reset" data-id="onhold_data" data-key="b_order_number" data-modal="O_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="O_NumberFilterModal" data-form="O_orderIdForm" data-id="onhold_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                    AWB Number
                                                    <div class="filter">
                                                        <a data-toggle="collapse" href="#O_AWBNumberFilter" role="button" aria-expanded="false" aria-controls="O_AWBNumberFilter"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="O_AWBNumberFilter">
                                                            <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="O_awbnumberForm">
                                                                @csrf
                                                                <input type="hidden" name="key" value="b_awb_number">
                                                                <input type="hidden" name="billing_filter_type" value="onhold">
                                                                <div class="form-group">
                                                                    <label for="O_searchByOrderId">Search by AWB Number</label>
                                                                    <input type="text" class="form-control" name="value" id="O_searchByOrderId" placeholder="Enter AWB Number Here">
                                                                </div>
                                                                <button type="reset" data-id="onhold_data" data-key="b_awb_number" data-modal="O_AWBNumberFilter" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="O_AWBNumberFilter" data-form="O_awbnumberForm" data-id="onhold_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
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
                                                        <a data-toggle="collapse" href="#O_CourierFilterModal" role="button" aria-expanded="false" aria-controls="O_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                        <div class="collapse filter-collapse" id="O_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                            <label>Filter by Courier Partner</label>
                                                            <form action="{{route('seller.set_filter_billing')}}" class="filterForm mt-0" method="post" id="O_CourierForm">
                                                                @csrf
                                                                <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                    <input type="hidden" name="key" value="courier_partner">
                                                                    <input type="hidden" name="billing_filter_type" value="onhold">
                                                                    @foreach($partners as $p)
                                                                        <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                            <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="O_c_{{$p->keyword}}">
                                                                            <label class="custom-control-label pt-1" for="O_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button type="reset" data-id="onhold_data" data-key="courier_partner" data-modal="O_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                                </button>
                                                                <button type="button" data-modal="O_CourierFilterModal" data-form="O_CourierForm" data-id="onhold_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </th>
                                            <th>Extra Amount<br>
                                                Charged
                                            </th>
                                            <th>On Hold Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="onhold_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" disabled style="width: 4%; text-align:center"></a>
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

                        <div class="tab-pane fade show" id="nav-settle" role="tabpanel" aria-labelledby="nav-settle-tab">
                            <div class="table-responsive h-600" style="min-height: 400px;white-space: nowrap;">
                                <table class="table table-hover mb-0" id="example1">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Order Id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_NumberFilterModal" role="button" aria-expanded="false" aria-controls="W_NumberFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse ml-5" id="W_NumberFilterModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="W_orderIdForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="b_order_number">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="form-group">
                                                                <label for="W_searchByOrderId">Search by Order Number</label>
                                                                <input type="text" class="form-control" name="value" id="W_searchByOrderId" placeholder="Enter Order Number Here">
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="b_order_number" data-modal="W_NumberFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_NumberFilterModal" data-form="W_orderIdForm" data-id="weight_reconcilication_data" class="applyFilterOrder submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_NDRDateFilter" role="button" aria-expanded="false" aria-controls="W_NDRDateFilter"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_NDRDateFilter">
                                                        <label>Filter by Date</label>
                                                        <form action="{{route('seller.set_filter_billing')}}" method="post" id="W_NDRDateFilterForm">
                                                            @csrf
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>Start Date</label>
                                                                        <input type="date" class="form-control" name="billing_start_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label>End Date</label>
                                                                        <input type="date" class="form-control" name="billing_end_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" max="{{date('Y-m-d')}}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="billing_start_date,billing_end_date" data-modal="W_NDRDateFilter" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="W_NDRDateFilter" data-form="W_NDRDateFilterForm" data-id="weight_reconcilication_data" class="btn btn-primary btm-sm mt-2 ml-0 applyFilterDate">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Channel</th>
                                        <th>Product Details</th>
                                        <th>Order Total</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Shipping Details
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_AWBNumberFilter" role="button" aria-expanded="false" aria-controls="W_AWBNumberFilter"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_AWBNumberFilter" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" class=" mt-0" method="post" id="W_awbnumberForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="b_awb_number">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <div class="form-group">
                                                                <label for="W_searchByOrderId">Search by AWB Number</label>
                                                                <input type="text" class="form-control" name="value" id="W_searchByOrderId" placeholder="Enter AWB Number Here">
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="b_awb_number" data-modal="W_AWBNumberFilter" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_AWBNumberFilter" data-form="W_awbnumberForm" data-id="weight_reconcilication_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Courier Partner
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_CourierFilterModal" role="button" aria-expanded="false" aria-controls="W_CourierFilterModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_CourierFilterModal" style="width: 230px !important; z-index:1;">
                                                        <label>Filter by Courier Partner</label>
                                                        <form action="{{route('seller.set_filter_billing')}}" class="filterForm mt-0" method="post" id="W_CourierForm">
                                                            @csrf
                                                            <div class="pt-2 pb-1" style="max-height: 180px; white-space: nowrap; overflow-y: auto;">
                                                                <input type="hidden" name="key" value="courier_partner">
                                                                <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                                @foreach($partners as $p)
                                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" name="value[]" value="{{$p->keyword}}" id="W_c_{{$p->keyword}}">
                                                                        <label class="custom-control-label pt-1" for="W_c_{{$p->keyword}}">{{Str::ucfirst($p->title)}}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="courier_partner" data-modal="W_CourierFilterModal" class="reset_value clear btn btn-primary btm-sm mt-2 ml-0">Reset
                                                            </button>
                                                            <button type="button" data-modal="W_CourierFilterModal" data-form="W_CourierForm" data-id="weight_reconcilication_data" class="applyFilterAwb submit btn btn-primary btm-sm mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Entered Weight &<br> Dimensions (CM)</th>
                                        <th>Charged Weight &<br> Dimensions (CM)</th>
                                        <th>Settled Weight &<br> Dimensions (CM)</th>
                                        <th>
                                            <div class="d-flex align-items-end justify-content-left w-100 mb-1">
                                                Status
                                                <div class="filter">
                                                    <a data-toggle="collapse" href="#W_FilterbyStatusModal" role="button" aria-expanded="false" aria-controls="W_FilterbyStatusModal"><i class="fa fa-filter fa-xs"></i></a>
                                                    <div class="collapse filter-collapse" id="W_FilterbyStatusModal" style="z-index:1;">
                                                        <form action="{{route('seller.set_filter_billing')}}" method="post" id="W_StatusForm">
                                                            @csrf
                                                            <input type="hidden" name="key" value="weight_rec_status">
                                                            <input type="hidden" name="billing_filter_type" value="weight_reconciliation">
                                                            <label>Filter Status</label>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="discrepancy" id="W_discrepancy">
                                                                <label class="custom-control-label pt-1" for="W_discrepancy">New Discrepancy</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="raised" id="W_raised">
                                                                <label class="custom-control-label pt-1" for="w_raised">Dispute Raised</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="Discrepancy Accepted" id="W_discrepancy_accepted">
                                                                <label class="custom-control-label pt-1" for="W_discrepancy_accepted">Discrepancy Accepted</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="Dispute Rejected by Courier" id="W_rejected_courier">
                                                                <label class="custom-control-label pt-1" for="W_rejected_courier">Dispute Rejected by Courier</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="Dispute Accepted by Courier" id="W_accepted_courier">
                                                                <label class="custom-control-label pt-1" for="W_accepted_courier">Dispute Accepted by Courier</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="Auto Accept" id="W_auto_accept">
                                                                <label class="custom-control-label pt-1" for="W_auto_accept">Auto Accept</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                                                <input type="checkbox" class="custom-control-input" name="value[]" value="SE Credit" id="W_se_credit">
                                                                <label class="custom-control-label pt-1" for="W_se_credit">SE Credit</label>
                                                            </div>
                                                            <button type="reset" data-id="weight_reconcilication_data" data-key="weight_rec_status" data-modal="W_FilterbyStatusModal" class="reset_value btn btn-primary btm-sm mt-2 ml-0">
                                                                Reset
                                                            </button>
                                                            <button type="button" data-modal="W_FilterbyStatusModal" data-form="W_StatusForm" data-id="weight_reconcilication_data" class="applyStatusFilter btn btn-primary mt-2 ml-0">Apply
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="weight_reconcilication_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                                <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                                <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                                <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                                <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" disabled style="width: 4%; text-align:center"></a>
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
        </div>

        <div class="content-inner" id="data_div_weight" style="display: none;">
        </div>
    </div>

    <div class="modal fade" id="bulkupload" tabindex="-1" role="dialog" aria-labelledby="bulkuploadTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content" id="fulfillment_info">
                <form method="post" action="{{route('seller.import_ndr_order')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload NDR Orders</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 pb-10 mb-2">
                                Download sample order upload file : <a class="text-info" href="{{url('public/assets/seller/ndr_order.csv')}}">Download</a>
                            </div>
                            <div class="col-sm-12">
                                <div class="m-b-10">
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="importFile">
                                            <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-info btn-sm">Upload</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Modal -->
<div class="modal fade" id="view_transaction" tabindex="-1" role="dialog" aria-labelledby="view_transactionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="example1">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>AWB Code</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td id="t_date"></td>
                            <td id="t_awb_number"></td>
                            <td id="t_credit">0.00</td>
                            <td id="t_debit">0.00</td>
                            <td id="t_description"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disputeOrder" tabindex="-1" role="dialog" aria-labelledby="disputeOrderTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="{{route('seller.disputeOrder')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="weight_rec_id" id="weight_rec_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Dispute Orders</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remark" id="remark" name="remark" required">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Images</label>
                                <input type="file" class="form-control" id="dispute_images" name="dispute_images[]" multiple required>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-info btn-sm">Dispute</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('seller.pages.scripts')
<script>
    var selectedTab='{{isset($_GET['tab'])?$_GET['tab']:"charges"}}',pageCount=1,currentTab='shipping_charges',isFilter = false,totalPage = 1;
    if(currentTab == 'shipping_charges')
        totalPage = $('#pagecountShipping').val();
    $('.currentPage').val(pageCount);
    $('.totalPage').html(totalPage);

    $(document).on("pageload",function(){
        function shippingCharges() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.ajax_shipping_charges')}}',
                success: function(response) {
                    $('#b_shipping_charges').html(response);
                    // Pagination
                    var totalPage = $('#total_billing').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
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
        }
    });

    //for model hide while screen click
    $(document).click(function(event) {
        //if you click on anything except the modal itself or the "open modal" link, close the modal
        if (!$(event.target).closest(".filter,.filter-collapse").length) {
            $("body").find(".filter-collapse").removeClass("show");
        }
    });

    function get_value_filter(divId) {
        isFilter = true;
        showOverlay();
        $.ajax({
            method : 'get',
            data : {
                'page' : pageCount
            },
            url: "{{ route('seller.ajax_filter_billing')}}",
            success: function(response) {
                switch(currentTab){
                    case 'billing_passbook':
                        $('#passbook_data').html(response);
                        var totalPage = $('#total_passbook').val();
                        break;
                    case 'weight_reconciliation':
                        $('#weight_reconcilication_data').html(response);
                        var totalPage = $('#total_weight').val();
                        break;
                    case 'remittance_log':
                        $('#remittance_log_data').html(response);
                        var totalPage = $('#total_remittance_log').val();
                        break;
                    case 'recharge_log':
                        $('#recharge_log_data').html(response);
                        var totalPage = $('#total_recharge_log').val();
                        break;
                    case 'onholdData':
                        $('#onhold_data').html(response);
                        var totalPage = $('#total_onhold').val();
                        break;
                    case 'billing_receipt':
                        $('#receipt_data').html(response);
                        var totalPage = $('#total_receipt').val();
                        break;
                    default:
                        $('#b_shipping_charges').html(response);
                        var totalPage = $('#total_ajax_billing_data').val();
                }
                var perPage = $('.perPageRecord').val();
                if(parseInt(totalPage) < parseInt(perPage)) {
                    $('.billing_display_limit').html(totalPage);
                } else {
                    $('.billing_display_limit').html(perPage);
                }
                $('#billing_count').html(totalPage);
                var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                $('.currentPage').val(pageCount);
            }
        });
        hideOverlay();
    }

    function paginateButton(current,total){
        $('.totalPage').html(total);
    }

    $(document).ready(function() {

        function fetch_orders(){
            showOverlay();
            if(isFilter) {
                get_value_filter();
            } else {
                // showOverlay();
                switch(currentTab){
                    case 'shipping_charges':
                        shippingCharges();
                        break;
                    case 'billing_passbook':
                        billing_passbook();
                        break;
                    case 'weight_reconciliation':
                        weight_reconciliation();
                        break;
                    case 'remittance_log':
                        remittance_log();
                        break;
                    case 'recharge_log':
                        recharge_log();
                        break;
                    case 'remittance_log':
                        billing_passbook();
                        break;
                    case 'onholdData':
                        onholdData();
                        break;
                    case 'billing_receipt':
                        billing_receipt();
                        break;
                    default:
                        billing_passbook();
                }
            }
            $('.currentPage').val(pageCount);
            hideOverlay();
        }

        // Total count badges
        countBilling();
        function countBilling(){
            $.ajax({
                url: '{{route('seller.countBilling')}}',
                success: function (response) {
                    $('#shipping_charges_badge').html(response.total_billing);
                    $('#weight_reconciliation_badge').html(response.total_weight_reconciliation);
                    $('#remittance_log_badge').html(response.total_remittance_log);
                    $('#recharge_log_badge').html(response.total_recharge_log);
                    $('#invoice_log_badge').html(response.total_invoices);
                    $('#onhold_reconciliation_badge').html(response.total_onhold_reconciliation);
                    $('#passbook_badge').html(response.total_passbook);
                    $('#credit_receipt_badge').html(response.total_credit_receipt);
                },
            });
        }

        // Pagination
        $('.firstPageButton').click(function(){
            if(pageCount > 1){
                pageCount = 1 ;
                fetch_orders();
            }
        });

        $('.previousPageButton').click(function(){
            if(pageCount > 1){
                pageCount--;
                fetch_orders();
            }
        });

        $('.nextPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount++;
                fetch_orders();
            }
        });

        $('.lastPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount = $('.totalPage').html();
                fetch_orders();
            }
        });

        $('#nav-tabContent').on('change', '.perPageRecord', function () {
            showOverlay();
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

        // $('.previousPageButton').click(function(){
        //     if(pageCount > 1){
        //         pageCount--;
        //         showOverlay();
        //         fetch_orders();
        //     }
        // });

        // $('.nextPageButton').click(function(){
        //     if(pageCount < totalPage){
        //         pageCount++;
        //         showOverlay();
        //         fetch_orders();
        //      }
        // });

        $('#nav-tabContent').on('click', '#checkAllButton', function() {
            var that = $(this);
            if (that.prop('checked')) {
                $('.selectedCheck').prop('checked', true);
                if(currentTab == 'weight_reconciliation') {
                    $('#MultiAcceptButton').fadeIn();
                }
                $('.total_billing_selected').html($('.billing_display_limit').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                if(currentTab == 'weight_reconciliation') {
                    $('#MultiAcceptButton').hide();
                }
                $('.total_billing_selected').html(0);
            }
        });

        $('#nav-tabContent').on('click', '.selectedCheck', function() {
            var cnt = 0;
            $('.selectedCheck:visible').each(function() {
                if ($(this).prop('checked'))
                    cnt++;
            });
            $('.total_billing_selected').html(cnt);
            if(currentTab == 'weight_reconciliation') {
                if (cnt > 0) {
                    $('#MultiAcceptButton').fadeIn();
                } else {
                    $('#MultiAcceptButton').hide();
                }
            }
        });

        $('.exportBtn').click(function () {
            order_ids = [];
            // var that = $(this);
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    order_ids.push($(this).val());
            });
            if(order_ids.length > 0) {
                location.href = "{{route('seller.export_billing_details')}}?ids="+order_ids;
            } else {
                location.href = "{{route('seller.export_billing_details')}}?ids=";
            }
        });

        $('.MultipleAcceptButton').click(function () {
            order_ids = [];
            // var that = $(this);
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    order_ids.push($(this).val());
                console.log($(this).val());
            });

            if (window.confirm("Are you sure want to Accept this?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': order_ids
                    },
                    url: '{{url('/')."/weight_reconciliation_accept_order/multiple"}}',
                    success: function(response) {
                        hideOverlay();
                        location.reload();
                    },
                    error: function(response) {
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

        $("body").tooltip({
            selector: '[data-toggle=tooltip]',
            delay: {
                show: 1000
            }
        });

        //For Collapsing other model when one is open
        var $collapsing = $('#nav-tabContent');
        $collapsing.on('show.bs.collapse', '.collapse', function() {
            $collapsing.find('.collapse.show').collapse('hide');
        });

        $('#nav-tabContent').on('click', '.applyFilterOrder', function() {
            pageCount = 1 ;
            var that = $(this);
            var form = that.data('form');
            var modal = that.data('modal');
            $('#' + modal).collapse('hide');
            $('#' + form).ajaxSubmit({
                success: function(response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        $('#nav-tabContent').on('click', '.applyFilterDate', function() {
            pageCount = 1 ;
            var that = $(this);
            var form = that.data('form');
            var modal = that.data('modal');
            $('#' + modal).collapse('hide');
            $('#' + form).ajaxSubmit({
                success: function(response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        $('#nav-tabContent').on('click', '.applyStatusFilter', function() {
            pageCount = 1 ;
            var that = $(this);
            var form = that.data('form');
            var modal = that.data('modal');
            $('#' + modal).collapse('hide');
            $('#' + form).ajaxSubmit({
                success: function(response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        //Reset Key Filtering
        $('#nav-tabContent').on('click', '.reset_value', function () {
            pageCount = 1 ;
            var that=$(this);
            var key=that.data('key');
            var id=that.data('id');
            var modal=that.data('modal');
            $.ajax({
                type : 'get',
                url :  '{{url('/')."/reset_key_billing/"}}' + key,
                success : function(){
                    get_value_filter(that.data('id'));
                    $('#'+modal).collapse('hide');
                }

            });
        });

        $('#nav-tabContent').on('click', '.applyFilterProduct', function() {
            pageCount = 1 ;
            var that = $(this);
            var form = that.data('form');
            var modal = that.data('modal');
            $('#' + modal).collapse('hide');
            $('#' + form).ajaxSubmit({
                success: function(response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        $('#nav-tabContent').on('click', '.applyFilterAwb', function() {
            pageCount = 1 ;
            var that = $(this);
            var form = that.data('form');
            var modal = that.data('modal');
            $('#' + modal).collapse('hide');
            $('#' + form).ajaxSubmit({
                success: function(response) {
                    get_value_filter(that.data('id'));
                }
            });
        });

        $("#S_awbnumberForm").submit(function(e) {
            e.preventDefault();
            $(".applyFilterAwb").click();
        });

        //get dimensi data in modal
        $('#nav-tabContent').on('click', '.view_transaction', function () {
            showOverlay();
            var that = $(this);

            $.ajax({
                url: '{{url('/')."/view_transaction_data/"}}' + that.data('id'),
                success: function (response) {
                    if(response != 'null'){
                        var info = JSON.parse(response);
                        $('#t_date').html(info.datetime);
                        $('#t_awb_number').html(info.awb_number);
                        if(info.type == 'c')
                            $('#t_credit').html(info.amount);
                        if(info.type == 'd')
                            $('#t_debit').html(info.amount);
                        $('#t_description').html(info.description);
                        $('#view_transaction').modal('show');
                    }else{
                        $.notify(" Oops... No Transaction Details Found!", {
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
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

        //get data of Reacharge
        $('#nav-tab').on('click', '#nav-shipping_charge-tab', function() {
            currentTab = 'shipping_charges';
            $(".data-counter").fadeIn();
            showOverlay();
            $('.currentPage').val(1);
            pageCount = 1;
            shippingCharges();
        });

        function shippingCharges() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.ajax_shipping_charges')}}',
                success: function(response) {
                    $('#b_shipping_charges').html(response);
                    // Pagination
                    var totalPage = $('#total_billing').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / $('.billing_display_limit').html());
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of Reacharge
        $('#nav-tab').on('click', '#nav-weight-tab', function() {
            currentTab = 'weight_reconciliation';
            $(".data-counter").fadeIn();
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            weight_reconciliation();
        });

        function weight_reconciliation() {
            $.ajax({
                url: '{{route('seller.billing_weight_reconciliation')}}',
                data : {
                    'page' : pageCount
                },
                success: function(response) {
                    $('#weight_reconcilication_data').html(response);
                    // Pagination
                    var totalPage = $('#total_weight').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of Reacharge
        $('#nav-tab').on('click', '#nav-remittance_log-tab', function() {
            currentTab = 'remittance_log';
            $(".data-counter").fadeIn();
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            remittance_log();
        });

        function remittance_log() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_remittance_log')}}',
                success: function(response) {
                    $('#remittance_log_data').html(response);
                    // Pagination
                    var totalPage = $('#total_remittance_log').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of Reacharge
        $('#nav-tab').on('click', '#nav-recharge_log-tab', function() {
            currentTab = 'recharge_log';
            $(".data-counter").fadeIn();
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            recharge_log();

        });

        function recharge_log() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_recharge_log')}}',
                success: function(response) {
                    $('#recharge_log_data').html(response);
                    // Pagination
                    var totalPage = $('#total_recharge_log').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
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
        }

        //get data of Reacharge
        $('#nav-tab').on('click', '#nav-on_hold-tab', function() {
            currentTab = 'onholdData';
            $(".data-counter").fadeIn();
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            onholdData();
        });

        function onholdData() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_onhold')}}',
                success: function(response) {
                    $('#onhold_data').html(response);
                    // Pagination
                    var totalPage = $('#total_onhold').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    //console.log(Math.ceil(totalPage / parseInt($('.billing_display_limit').html())));
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of REcharge Passbook
        $('#nav-tab').on('click', '#nav-passbook-tab', function() {
            $(".data-counter").fadeIn();
            currentTab='billing_passbook';
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            billing_passbook();
        });

        function billing_passbook() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_passbook')}}',
                success: function(response) {
                    $('#passbook_data').html(response);
                    // Pagination
                    var totalPage = $('#total_passbook').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of REcharge Passbook
        $('#nav-tab').on('click', '#nav-receipt-tab', function() {
            $('.currentPage').val(1);
            pageCount = 1;
            $(".data-counter").fadeIn();
            currentTab='billing_receipt';
            showOverlay();
            billing_receipt();
        });

        function billing_receipt() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_receipt')}}',
                success: function(response) {
                    $('#receipt_data').html(response);
                    // Pagination
                    var totalPage = $('#total_receipt').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        //get data of REcharge Passbook
        $('#nav-tab').on('click', '#nav-invoice-tab', function() {
            $(".data-counter").fadeIn();
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            billing_invoice();
        });

        function billing_invoice() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_invoice')}}',
                success: function(response) {
                    $('#invoice_data').html(response);
                    // Pagination
                    var totalPage = $('#total_invoice').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }
        //get data of REcharge Passbook
        $('#nav-tabContent').on('click', '#pills-other_invoice-tab', function() {
            $('.currentPage').val(1);
            pageCount = 1;
            showOverlay();
            billing_other_invoice();
        });

        function billing_other_invoice() {
            $.ajax({
                data : {
                    'page' : pageCount
                },
                url: '{{route('seller.billing_other_invoice')}}',
                success: function(response) {
                    $('#all_other_invoice_data').html(response);
                    // Pagination
                    var totalPage = $('#total_invoice').val();
                    var perPage = $('.perPageRecord').val();
                    if(parseInt(totalPage) < parseInt(perPage)) {
                        $('.billing_display_limit').html(totalPage);
                    } else {
                        $('.billing_display_limit').html(perPage);
                    }
                    $('#billing_count').html(totalPage);
                    var totalPages = Math.ceil(totalPage / parseInt($('.billing_display_limit').html()));
                    $('.totalPage').html(isNaN(totalPages) ? 1 : totalPages);
                    hideOverlay();
                    reset_filters();
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
        }

        $('#nav-tabContent').on('click', '.reattempt_btn', function() {
            var that = $(this);

            if (window.confirm("Are you sure want to Reattempt Order?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/ndr-reattempt-order"}}/' + that.data('id'),
                    success: function(response) {
                        hideOverlay();
                        $.notify(" Order has been Reattempted.", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                    },
                    error: function(response) {
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

        $('#nav-tabContent').on('click', '.rto_btn', function() {
            var that = $(this);

            if (window.confirm("Are you sure want to Return Order?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/ndr-rto-order"}}/' + that.data('id'),
                    success: function(response) {
                        hideOverlay();
                        $.notify(" Order has been Returned.", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                    },
                    error: function(response) {
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

        $('#nav-tabContent').on('click', '.escalate_btn', function() {
            var that = $(this);

            if (window.confirm("Are you sure want to Esacalate Order?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/ndr-escalate-order"}}/' + that.data('id'),
                    success: function(response) {
                        hideOverlay();
                        $.notify(" Order has been Escalated.", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "success",
                            icon: "check"
                        });
                    },
                    error: function(response) {
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
    });

    $(document).ready(function(){
        switch(selectedTab){
            case 'reconciliation':
                $('#nav-weight-tab').click();
                break;
            case 'on-hold_reconcilation':
                $('#nav-on_hold-tab').click();
                break;
            case 'settle':
                $('#nav-settle-tab').click();
                break;
            default:
                $('#nav-all_orders-tab').click();
                break;
        }
    });

    function reset_filters(){
        $.ajax({
            type : 'get',
            url : '{{route('seller.reset_filters')}}',
            success : function(response){},
            error : function(response){}
        });
    }



    $('#data_div_weight').on('click', '.BackButton', function() {
        $('#data_div_weight').hide();
        $('#data_div').fadeIn();
    });

    $('#data_div_weight').on('click', '.AddComment', function() {
        $('#AddCommentWeight').modal();
    });

    $('#nav-tabContent').on('click', '.DisputeButton', function() {
        $('#disputeOrder').modal();
        $('#weight_rec_id').val($(this).data('id'));
    });

    $('#nav-tabContent').on('click', '.AcceptButton', function() {
        var that = $(this);

        if (window.confirm("Are you sure want to Accept this?")) {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/weight_reconciliation_accept_order"}}/' + that.data('id'),
                success: function(response) {
                    hideOverlay();
                    location.reload();
                },
                error: function(response) {
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


    //get dimensi data in modal
    $('#nav-tabContent').on('click', '.ViewHistory', function() {
        showOverlay();
        var that = $(this);

        $.ajax({
            url: '{{url('/')."/get_history_weight_reconciliation/"}}' + that.data('id'),
            success: function(response) {
                $('#data_div').hide();
                $('#data_div_weight').show();
                $('#data_div_weight').html(response);
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
</script>
</body>

</html>
