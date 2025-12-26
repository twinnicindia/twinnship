<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MIS Report | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }
    </style>
</head>

<body>
<div class="container-fluid ">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="main-content d-flex flex-column">
    <div class="content-wrapper">
        <div class="content-inner" id="data_div">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">MIS Report</h3>
                    <form action="{{route('seller.ajaxReportData')}}" method="post" id="reportForm">
                        @csrf
                        <input type="text" name="page" id="page" hidden/>
                        <div class="row pt-sm order-search">
                            <div class="col-md-2">
                                <label>Type</label>
                                <select class="form-control" onchange="on_field_change(this.value)" name="report_type" id="report_type">
                                    <option value="orders" selected>Orders</option>
                                    <option value="shipments">Shipments</option>
                                    <option value="billing">Billing</option>
                                    <option value="returns">Returns</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Sub Type</label>
                                <select class="form-control" name="report_subtype" id="report_subtype"  onchange="on_subfield_change(this.value)">
                                    <option value="">Select Order Type</option>
                                    <option value="all_order">All Orders</option>
                                    <option value="process_order">Processing Order</option>
                                    <option value="shipped_order">Shipped Order</option>
                                    <option value="manifest_order">Manifested Order</option>
                                    <option value="delivered_order">Delivered Order</option>
                                    <option value="picked_orders">Picked Orders</option>
                                    <option value="archive_orders">Archive Orders</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" name="from_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" value="{{date('Y-m-d',strtotime('-7 days'))}}" id="from_date" class="form-control" max="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" name="to_date" id="to_date" min="{{date('Y-m-d',strtotime('-90 days'))}}" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-2 pt-2">
                                <br>
                                <button class="btn btn-primary" id="searchReportButton" type="button">Search</button>
                                <button class="btn btn-primary" title="Export Data" id="downloadReport" style="display:none;" type="button">Export</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="example1">
                            <thead>
                                <tr id="tr_data">
                                    <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                    <th>Sr.No</th>
                                    <th width="10%">Order Date</th>
                                    <th>Channel</th>
                                    <th>Order Number<br> Status</th>
                                    <th>Amount<br>Payment</th>
                                    <th>Product Details</th>
                                    <th>Customer Details</th>
                                    <th>Pickup Address</th>
                                    <th>Delivery Address</th>
                                    <th>Dimension</th>
                                    <th>Shipment Details</th>
                                </tr>
                            </thead>
                            <tbody id="report_data">
                                <tr>
                                    <td colspan="15">No Reord Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="table-footer">
                    <div class="my-2">
                        <div class="pagination-container">
                            <div class="d-flex align-items-center gap-3">
                                <div class="pagination">
                                    <a class="prev-page firstPageButton"><img src="{{url('/')}}/assets/sellers/images/1.svg" alt=""></a>
                                    <a class="prev-page previousPageButton"><img src="{{url('/')}}/assets/sellers/images/2.svg" alt=""></a>
                                    <span class="page-info text-dark currentPage">1 </span> 
                                    <span class="page-info text-dark">of</span>
                                    <span class="page-info text-dark totalPage"></span>
                                    <a class="next-page nextPageButton"><img src="{{url('/')}}/assets/sellers/images/3.svg" alt=""></a>
                                    <a class="next-page lastPageButton"><img src="{{url('/')}}/assets/sellers/images/4.svg" alt=""></a>
                                </div>
                                <div class="go-to-page text-dark">Go to page: <input type="number" min="1"
                                        max="73" value=""><button class="go-btn">Go</button>
                                </div>
                            </div>
                            <div class="result-count text-dark">Showing <span class="mis_display_limit"></span> of <span id="mis_count"> records.</div>
                            <div class="items-per-page-dropdown text-dark">Rows per page:
                                <select name="per_page_record" class="perPageRecord">
                                    <option value="20">20</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="All">All</option>
                                </select>
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

<script type="text/javascript">
    function on_field_change(value = false) {
        var options = '';
        var thead_data = '';
        // $('#tr_data').html('');
        $('#downloadReport').hide();
        $('#report_data').html('');
        switch (value) {
            case 'orders':
                options = '<option value="all_order" selected>All Orders</option><option value="process_order">Processing Order</option><option value="shipped_order">Shipped Order</option><option value="manifest_order">Manifested Order</option><option value="delivered_order">Delivered Order</option><option value="picked_orders">Picked Orders</option><option value="archive_orders">Archive Orders</option>';
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Sr.No</th><th width="10%">Order Date</th><th>Channel</th><th>Order Number</div><br> Status</th><th>Amount<br>Payment</th><th>Product Details</th><th>Customer Details</th><th>Pickup Address</th><th>Delivery Address</th><th>Dimensions</th><th>Shipment Details</th>'
                break;
            case 'shipments':
                options = '<option value="" readonly>Select Option</option><option value="all_ndr">All NDR</option><option value="ndr_delivered">NDR Delivered</option><option value="rto_report">RTO Report</option>';
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>NDR Raised At</th><th>NDR Reason</th><th>Order Details</th><th>Customer Details</th><th>Delivery Address</th><th>Shipment Details</th><th>Escalation <br>Information</th><th>Last Action <br>Taken By</th>'
                break;
            case 'billing':
                options = '<option value="" readonly>Select Option</option><option value="shipping_charges">Shipping Charges</option><option value="weight_reconciliation">Weight Reconciliation</option><option value="remittance_logs">Remittance Logs</option><option value="onhold_reconciliation">Onhold Reconciliation</option><option value="invoices">Invoices</option>';
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Order Id</th><th>AWB Number</th><th>Courier</th><th>Shipment Status</th><th>AWB Assigned Date</th><th>Applied Weight Charges</th><th>Excess Weight Charges</th><th>On Hold Amount</th><th>Total Frieght Charges</th><th>Entered Weight & Dimensions</th><th>Charged Weight & Dimensions</th>'
                break;
            case 'returns':
                options = '<option value="" readonly>Select Option</option><option value="return_order">All Return Order</option><option value="reverse_order">All Reverse Order</option>';
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Sr.No</th><th>Order Date</th><th>Channel</th><th>Order Number</div><br> Status</th><th>Amount<br>Payment</th><th>Product Details</th><th>Customer Details</th><th>Pickup Address</th><th>Delivery Address</th><th>Dimension</th>'
                break;
            default:
                options = '<option value="all_order" selected>All Orders</option><option value="process_order">Process Order Reports</option><option value="shipped_order">Shipped Order Report</option><option value="manifest_order">Manifest Order Report</option>';
        }
        $('#report_subtype').html(options);
        $('#tr_data').html(thead_data);
        $('#report_subtype').trigger('change');
    }

    function on_subfield_change(value = false) {
        var options = '';
        var thead_data = '';
        $('#report_data').html('');
        $('#downloadReport').hide();
        switch (value) {
            case 'shipped_order':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Sr.No</th><th>Order Date</th><th>Channel</th><th>Order Number</div><br> Status</th><th>Amount<br>Payment</th><th>Product Details</th><th>Customer Details</th><th>Pickup Address</th><th>Delivery Address</th><th>Dimensions<th>Shipment Details</th>'
                break;
            case 'manifest_order':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Sr.No</th><th>Order Date</th><th>Channel</th><th>Order Number</div><br> Status</th><th>Amount<br>Payment</th><th>Product Details</th><th>Customer Details</th><th>Pickup Address</th><th>Delivery Address</th><th>Dimensions<th><th>Shipment Details</th>'
                break;
            case 'weight_reconciliation':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Order Id<br>Date</th><th>Channel</th><th>Order Details</th><th>Order Total</th><th>Shipping Details</th><th>Entered Weight & Dimension</th><th>Charged Weight & Dimensions</th><th>Status</th><th>Status</th>'
                break;
            case 'remittance_logs':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Date</th><th>CRF Id</th><th>UTR</th><th>Freight Charges <br>From COD</th><th>Early COD Charges</th><th>RTO Reversal Amount</th><th>Remmitance Amount</th><th>Status</th><th>Description</th>'
                break;
            case 'onhold_reconciliation':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>AWB Assigned Date</th><th>Order Id</th><th>AWB Number</th><th>Courier</th><th>Intital Amount<br>Charged</th><th>On Hold Amount</th>'
                break;
            case 'invoices':
                thead_data  = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Invoice Id</th><th>Invoice Date</th><th>Due Date</th><th>Total</th><th>Status</th>'
                break;
            default:
                //options = '<option value="all_order" selected>All Orders</option><option value="process_order">Process Order Reports</option><option value="shipped_order">Shipped Order Report</option>';
                //thead_data = '<th><input type="checkbox" id="checkAllButton" value="y"></th><th>Sr.No</th><th width="10%">Order Date</th><th>Channel</th><th>Order Number</div><br> Status</th><th>Amount<br>Payment</th><th>Product Details</th><th>Customer Details</th><th>Pickup Address</th><th>Delivery Address</th><th>Dimension</th>';
        }
        if(thead_data !== '')
            $('#tr_data').html(thead_data);
    }

    $('#report_subtype').change(function () {
        if($('#report_subtype').val() === "archive_orders") {
            $('#to_date').attr('max', '{{date('Y-m-d',strtotime("-90 days"))}}');
            $('#to_date').val('{{date('Y-m-d',strtotime("-90 days"))}}');
            $('#to_date').attr('min','{{date('Y-m-d',strtotime("-270 days"))}}');
            $('#from_date').attr('min', '{{date('Y-m-d',strtotime("-270 days"))}}');
            $('#from_date').val('{{date('Y-m-d',strtotime("-270 days"))}}');
            $('#from_date').attr('max', '{{date('Y-m-d',strtotime("-90 days"))}}');
        }
        else{
            $('#to_date').attr('max', '{{date('Y-m-d')}}');
            $('#to_date').val('{{date('Y-m-d')}}');
            $('#from_date').attr('min', '{{date('Y-m-d',strtotime("-90 days"))}}');
            $('#from_date').val('{{date('Y-m-d',strtotime("-90 days"))}}');
            $('#from_date').attr('max', '{{date('Y-m-d')}}');
        }
    });

    // Pagination
    var totalPage=0, pageCount=1;
    $('.currentPage').text(pageCount);
    $('.mis_display_limit').html(0);
    $('#mis_count').html(totalPage);
    $('.totalPage').text(totalPage);


    $('.firstPageButton').click(function(){
        if(pageCount > 1){
            pageCount = 1 ;
            $("#page").val(pageCount);
            fetchData();
        }
    });

    $('.previousPageButton').click(function(){
        if(pageCount > 1){
            pageCount--;
            $("#page").val(pageCount);
            fetchData();
        }
    });

    $('.nextPageButton').click(function(){
        var totalPage = parseInt($('.totalPage').text());
        if(pageCount < totalPage){
            pageCount++;
            $("#page").val(pageCount);
            fetchData();
        }
    });

    $('.lastPageButton').click(function(){
        var totalPage = parseInt($('.totalPage').text());
        if(pageCount < totalPage){
            pageCount = totalPage;
            $("#page").val(pageCount);
            fetchData();
        }
    });

    $('.perPageRecord').change( function () {
        showOverlay();
        cnt = 0;
        $('.total_mis_selected').html(cnt);
        var page = $(this).val();
        $.ajax({
            url: '{{url('/')."/per_page_record/"}}' +page,
            success: function (response) {
                $('.perPageRecord').val(page);
                fetchData();
                hideOverlay();
            },
            error: function (response) {
                hideOverlay();
            }
        });
    });

    $('#data_div').on('click', '#searchReportButton', function () {
        $("#page").val(1);
        pageCount = 1;
        fetchData();
    });
    function fetchData() {
        showOverlay();
        $('#reportForm').ajaxSubmit({
            success: function (response) {
                $('#report_data').html(response);
                var totalPage = parseInt($('#total_page').val()) || 0;
                pageCount = parseInt($("#page").val());
                $(".currentPage").text(pageCount);
                var perPage = parseInt($('.perPageRecord').val());
                if(totalPage < perPage) {
                    $('.mis_display_limit').text(totalPage);
                } else {
                    $('.mis_display_limit').text(perPage);
                }
                $('#mis_count').text(totalPage);
                var totalPages = Math.ceil(totalPage / perPage);
                $('.totalPage').text(isNaN(totalPages) ? 1 : totalPages);
                hideOverlay();
                $('#downloadReport').show();
            },
            error: function (response){
                hideOverlay();
            }
        });
    }


    $('#example1').on('click', '#checkAllButton', function() {
        var that = $(this);
        if (that.prop('checked')) {
            $('.selectedCheck').prop('checked', true);
            $('.total_mis_selected').html($('.mis_display_limit').html());
        } else {
            $('.selectedCheck').prop('checked', false);
            $('.total_mis_selected').html(0);
        }
    });

    $('#example1').on('click', '.selectedCheck', function() {
        var cnt = 0;
        $('.selectedCheck:visible').each(function() {
            if ($(this).prop('checked'))
                cnt++;
        });
        $('.total_mis_selected').html(cnt);
    });

    $('#downloadReport').click(function () {
        var ids = [];
        // var that = $(this);
        $('.selectedCheck:visible').each(function () {
            if ($(this).prop('checked'))
                ids.push($(this).val());
        });
        if(ids.length > 0) {
            location.href = "{{route('seller.export_report_data')}}?ids="+ids;
        } else {
            location.href = "{{route('seller.export_report_data')}}?ids=";
        }
    });
</script>
</body>

</html>
