<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <style type="text/css">
        .multiselect-container{
            left: 15% !important;
        }
    </style>
    <title>Billings | {{$config->title}}</title>
</head>

<body>

@include('seller.pages.header')
@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="nav-scroll scroll-bar active card mb-4 col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="tablist mt-3" id="pills-tab" role="tablist">
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons active" data-tab="shipping_charges" href="javascript:" type="button">Shipping Charges</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="remittance_logs" type="button" href="javascript:">Remittance Logs</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="recharge_logs" href="javascript:" type="button">Recharge Logs</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="invoices" href="javascript:" type="button">Invoices</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="passbook" href="javascript:" type="button">Passbook</a>
                    </div>
{{--                    <div class="me-2" role="presentation">--}}
{{--                        <a class="nav-buttons" data-tab="credit_receipt" href="javascript:" type="button">Credit Receipt</a>--}}
{{--                    </div>--}}
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="wallet" href="javascript:" type="button">Wallet</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="row">
                    <div class="col-12">
                        <div class="" id="order-content-div">
                            <div class="table-responsive scroll-bar active" >

                            </div>
                        </div>
                    </div>
                    <div class="table-footer ">
                        <div class="my-2">
                            <div class="pagination-container">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="pagination">
                                        <a class="first-page"><img src="{{asset('assets/sellers/')}}/images/1.svg" alt=""></a>
                                        <a class="prev-page"><img src="{{asset('assets/sellers/')}}/images/2.svg" alt=""></a>
                                        <span class="page-info text-dark" id="currentPageLabel">1</span>
                                        <span class="page-info text-dark">of</span>
                                        <span class="page-info text-dark" id="totalPageLabel">2</span>
                                        <a class="next-page"><img src="{{asset('assets/sellers/')}}/images/3.svg" alt=""></a>
                                        <a class="last-page"><img src="{{asset('assets/sellers/')}}/images/4.svg" alt=""></a>
                                    </div>
                                    <div class="go-to-page text-dark">Go to page: <input type="number" min="1" max="73" value="">
                                        <button class="go-btn">Go</button>
                                    </div>
                                </div>
                                <div class="result-count text-dark">Showing <span id="currentPageRecordLabel"></span> of <span id="totalRecordLabel"></span> records.</div>
                                <div class="items-per-page-dropdown text-dark">Rows per page:
                                    <select class="rows-per-page">
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
        <!-- mode -->
        <div class="modal fade" id="shipModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Shipping Partner</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 col-md-6 mb-4">
                            <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                                <div class="row align-items-center">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="icon transition">
                                                    <img class="rounded-circle mb-3" style="height: 100px; width: 100px;"
                                                         src="{{asset('assets/sellers/')}}/images/ekkart.png" alt="admin">
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <p class="text-dark mb-3">ekart 5kg</p>
                                                    <p class="text-dark mb-3">RTO Charges: 20</p>
                                                    <p class="text-dark mb-3">Delivering Excellence, Every Mile</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column">
                                                <p class="text-dark mb-3"> <b>₹47.20 </b><small>(include all text)</small>
                                                </p>
                                                <p class="text-dark mb-3">Charge <small> <b>₹47.20 </b></small></p>
                                                <p class="text-dark mb-3">+Cod Charge<small> <b>₹47.20 </b></small></p>
                                                <p class="text-dark">+Cod Charge<small> <b>₹47.20 </b></small></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <button type="button"
                                                        class="btn btn-primary text-white fw-semibold me-2">Download Manifest</button>
                                                <p class="text-dark">+Cod Charge<small>₹47.20</small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6 mb-4">
                            <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                                <div class="row align-items-center">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="icon transition">
                                                    <img class="rounded-circle mb-3" style="height: 100px; width: 100px;"
                                                         src="{{asset('assets/sellers/')}}/images/ekkart.png" alt="admin">
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <p class="text-dark mb-3">ekart 5kg</p>
                                                    <p class="text-dark mb-3">RTO Charges: 20</p>
                                                    <p class="text-dark mb-3">Delivering Excellence, Every Mile</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column">
                                                <p class="text-dark mb-3"> <b>₹47.20 </b><small>(include all text)</small>
                                                </p>
                                                <p class="text-dark mb-3">Charge <small> <b>₹47.20 </b></small></p>
                                                <p class="text-dark mb-3">+Cod Charge<small> <b>₹47.20 </b></small></p>
                                                <p class="text-dark">+Cod Charge<small> <b>₹47.20 </b></small></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <button type="button"
                                                        class="btn btn-primary text-white fw-semibold me-2">Download Manifest</button>
                                                <p class="text-dark">EDD<small> <b>24 April 2024 </b></small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!-- mode -->

        <div class="card bg-white border-0 rounded-10 mb-4">
            <div class="offcanvas offcanvas-end" tabindex="-1" id="shipping_filter" aria-labelledby="shipping_filter">
                <div class="offcanvas-header border-bottom p-4">
                    <h5 class="offcanvas-title fs-18 mb-0" id="offcanvasRightLabel">Explore Additional Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-4">
                    <form>
                        <div class="form-group mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="label text-dark">Start Date</label>
                                        <div class="form-group position-relative">
                                            <input type="date" class="form-control text-dark ps-5 h-58" id="filterStartDate">
                                            <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="label text-dark">End Date</label>
                                        <div class="form-group position-relative">
                                            <input type="date" class="form-control text-dark ps-5 h-58" id="filterEndDate">
                                            <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <div class="select-wrapper-new">
                                <label class="label text-dark" for="filterCourierPartner">Courier Partner</label>
                                <select id="filterCourierPartner" multiple="multiple" class="form-control multiSelectDropDown">
                                    @foreach($partners as $p)
                                        <option value="{{$p->keyword}}">{{$p->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-4" id="remitt_mode" style="display: none">
                            <div class="select-wrapper-new">
                                <label class="label text-dark" for="filterCourierPartner">Remit Mode</label>
                                <select id="filterRemitMode" multiple="multiple" class="form-control multiSelectDropDown">
                                    <option value="wallet">Wallet</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group d-flex gap-3">
                            <button type="button" class="btn btn-primary text-white fw-semibold py-2 px-2 px-sm-3 applyFilterButton">
                                <span class="py-sm-1 d-block">
                                    <span class="text-light">Apply Filter</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="reAttemptModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5>Enter Re-Attempt Remarks</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="ReAttemptForm" action ="{{url('/ndr-reattempt-order')}}" method="post">
                    @csrf
                    <input type="hidden" id="reattempt_ids" name="id">
                    <div class="form-group">
                        <label for="remarkdata">Enter Remarks</label>
                        <textarea class="form-control" rows="5" name="remark" id="remarkdata" placeholder="Enter reattempt instruction"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="SubmitReattemptFormButton" >Re-Attempt</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="RTOModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5>Enter RTO Remarks</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="RTOForm" action ="{{route('seller.ndr_rto_order')}}" method="post">
                    @csrf
                    <input type="hidden" id="rto_ids" name="id">
                    <div class="form-group">
                        <label for="remarkdata">Enter Remarks</label>
                        <textarea class="form-control" rows="5" name="remark" id="rtoRemark" placeholder="Enter RTO Remark"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="SubmitRTOFormButton" >RTO Selected</button>
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
                <button type="button" class="close" id="close_modal" data-dismiss="modal" aria-label="Close">
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
    let selectedTab = 'shipping_charges', currentPage=1, pageSize=20, totalPage = 1;
    $(document).ready(function (){
        loadPageData();
        $('.nav-buttons').click(function (){
            let that = $(this);
            selectedTab = that.data('tab');
            $('.nav-buttons').removeClass('active');
            that.addClass('active');
            currentPage = 1;
            pageSize = 20;
            loadPageData();
        });
        $('.next-page').click(function (){
            if(currentPage < totalPage)
                currentPage++;
            loadPageData();
        });
        $('.prev-page').click(function (){
            if(currentPage > 1)
                currentPage--;
            loadPageData();
        });
        $('.first-page').click(function (){
            currentPage = 1;
            loadPageData();
        });
        $('.last-page').click(function (){
            currentPage = totalPage;
            loadPageData();
        });

        $('#order-content-div').on('click', '.reattempt_btn', function () {
            var that = $(this);
            $('#reattempt_ids').val(that.data('id'));
            $('#reAttemptModal').modal('show');
        });

        $('#order-content-div').on('click', '.rto_btn', function () {
            var that = $(this);
            $('#rto_ids').val(that.data('id'));
            $('#RTOModal').modal('show');
        });

        $('#order-content-div').on('click', '.escalate_btn', function () {
            var that = $(this);

            if (window.confirm("Are you sure want to Esacalate Order?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/ndr-escalate-order"}}/' + that.data('id'),
                    success: function (response) {
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
                        countOrderNdr();
                        fetch_ndr_orders();
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

        //get dimensi data in modal
        $('#order-content-div').on('click', '.view_transaction', function () {
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


        $('#SubmitReattemptFormButton').click(function() {
            $('#ReAttemptForm').ajaxSubmit({
                success: function(response) {
                    showSuccess('Order Marked for Re-Attempt Successfully');
                    $("#reAttemptModal").modal('hide');
                    loadPageData();
                },
            });
        });
        $('#SubmitRTOFormButton').click(function() {
            $('#RTOForm').ajaxSubmit({
                success: function(response) {
                    $("#RTOModal").modal('hide');
                    showSuccess('Order Successfully Marked as RTO');
                    loadPageData();
                },
            });
        });

        $('#order-content-div').on('click', '#checkAllButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                $('.selectedCheck').prop('checked', true);
                $('.total_ndr_selected').html($('.ndr_display_limit').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                $('.total_ndr_selected').html(0);
            }
        });
        $('.rows-per-page').change(function (){
            let that = $(this);
            pageSize = that.val();
            currentPage = 1;
            loadPageData();
        });
        $('#nav-tabContent').on('click', '.selectedCheck', function () {
            var cnt = 0;
            $('.selectedCheck').each(function () {
                if ($(this).prop('checked'))
                    cnt++;
            });
            $('.total_ndr_selected').html(cnt);
        });

        $('.nav-buttons').on('click', function() {
            var tab = $(this).data('tab');

            if (tab === 'wallet') {
                $('.table-footer ').hide();
            } else {
                $('.table-footer ').show();
            }
        });

        $('#order-content-div').on('click', '.set_recharge_amount', function () {

            var amount = $(this).data('amount');

            $('#recharge_wallet_amount').val(amount);
        });

        $('#close_modal').click(function() {
            $('#view_transaction').modal('hide');
        });

        $('#order-content-div').on('click', '.applyFilterButton', function (){
            loadPageData();
            $('#offcanvasRight').offcanvas('hide');
        });

        $('.applyFilterButton').click(function (){
            loadPageData();
            $('#offcanvasRight').offcanvas('hide');
        });

        $('#order-content-div').on('click', '.clearFilterButton', function (){
            $('#filterAWBList').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            $('#filterCourierPartner').val([]).trigger('change').multiselect('refresh');
            loadPageData();
        });

        $('#order-content-div').on('click','.exportShippingButton', function (){
            let selectedIds = [];
            $('#order-content-div .selectCheckBoxes').each(function() {
                let that = $(this);
                if (that.prop('checked')) {
                    selectedIds.push(that.val());
                }
            });
            $.ajax({
                url: '{{ route('seller.export_shipping_details') }}',
                type: 'POST',
                data: {
                    'selected_tab': selectedTab,
                    'selected_ids': selectedIds,
                    'filter': LoadFilterObject(),
                    '_token': '{{ csrf_token() }}'
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var blob = new Blob([data], { type: 'text/csv' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'shipping-export.csv';
                    link.click();
                },
                error: function(data) {
                    console.error('Error:', data);
                }
            });
        });

        $('#order-content-div').on('click','.exportPassbookButton', function (){
            let selectedIds = [];
            $('#order-content-div .selectCheckBoxes').each(function() {
                let that = $(this);
                if (that.prop('checked')) {
                    selectedIds.push(that.val());
                }
            });
            $.ajax({
                url: '{{ route('seller.export_passbook_details') }}',
                type: 'POST',
                data: {
                    'selected_tab': selectedTab,
                    'selected_ids': selectedIds,
                    'filter': LoadFilterObject(),
                    '_token': '{{ csrf_token() }}'
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var blob = new Blob([data], { type: 'text/csv' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'passbook-export.csv';
                    link.click();
                },
                error: function(data) {
                    console.error('Error:', data);
                }
            });
        });
    });

    function loadPageData(){
        switch(selectedTab){
            case 'shipping_charges':
                loadAllShippingCharges();
                break;
            case 'remittance_logs':
                loadAllRemitance();
                break;
            case 'recharge_logs':
                loadAllRecharge();
                break;
            case 'invoices':
                loadAllInvoice();
                break;
            case 'passbook':
                loadAllPassbook();
                break;
            case 'credit_receipt':
                loadAllReceipt();
                break;
            case 'wallet':
                loadAllWallet();
                break;
        }
    }
    function loadAllShippingCharges(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-billing')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllRemitance(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-remitance')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllRecharge(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-recharge')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllInvoice(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-invoice')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllPassbook(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-passbook')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllReceipt(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-receipt')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadAllWallet(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'filter': LoadFilterObject(),
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-wallet')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function LoadFilterObject(){
        return {
            'filterAWBList': $('#filterAWBList').val(),
            'filterStartDate': $('#filterStartDate').val(),
            'filterEndDate': $('#filterEndDate').val(),
            'filterCourierPartner': $('#filterCourierPartner').val(),
        };
    }

    function setupPaginationData(response){
        totalPage = response.page.lastPage;
        $('#currentPageLabel').html(response.page.currentPage);
        $('#totalPageLabel').html(response.page.lastPage);
        $('#totalRecordLabel').html(response.page.totalRecord);
        $('#currentPageRecordLabel').html(response.page.current_count);
    }

    function showRemitMode(){
        $('#remitt_mode').show();
    }

    function hideRemitMode(){
        $('#remitt_mode').show();
    }
</script>

</body>

</html>
