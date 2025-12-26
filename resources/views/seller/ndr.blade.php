<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <title>All Order | {{$config->title}}</title>
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
                        <a class="nav-buttons active" data-tab="action_required" href="javascript:" type="button">Action Required</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="action_requested" type="button" href="javascript:">Action Requested</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="delivered" href="javascript:" type="button">Delivered</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons" data-tab="rto" href="javascript:" type="button">RTO</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">
                    <div class="mb-3 mt-3 me-2">
                        <div>
                            <form class="src-form position-relative" >
                                <input type="text" class="form-control" placeholder="Search here AWB...." style="border: 1px solid; color: black;" >
                                <button type="submit"
                                        class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0">
                                    <i data-feather="search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <button type="button" class=" p-2 btn btn btn-primary text-white fw-semibold me-2">
                            <i class="ri-filter-off-line"></i>Clear Filter
                        </button>
                    </div>
                    <div class="mb-3 mt-3">
                        <button type="button" class=" p-2 btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                            <i class="ri-filter-line me-2"></i>More Filter
                        </button>
                    </div>
                    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">
                        <div class="icon transition me-5 mt-3">
                            <div class="text ptext">
                                <div class="d-flex justify-content-between">
                                    <!-- <button type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#bulkupload">
                                        <i class="ri-stack-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Bulk Order Update"></i>
                                    </button> -->
                                    <a type="button" class="btn btn btn-primary text-white fw-semibold" id="export_ndr_order" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report"> <i class="ri-download-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="bulkupload" tabindex="-1" aria-labelledby="bulkupload" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content justify-content-center">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Bulk NDR Action</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <a href="{{asset('assets/sellers/NDR_Action_Import.csv')}}" style="color: blue;"><i class="ri-download-line"></i>Download sample NDR Action file :</a>
                                    </div>
                                    <form method="post" action="{{route('seller.bulkNDRAction')}}" enctype="multipart/form-data" id="bulkimportform">
                                        @csrf
                                        <div class="form-group  text-center">
                                            <div class="form-control h-100 w-80 text-center position-relative p-5 p-lg-5">
                                                <div class="product-upload">
                                                    <label for="inputGroupFile02" class="file-upload mb-0">
                                                        <i class="ri-upload-cloud-2-line fs-2 text-gray-light"></i>
                                                        <span class="d-block fw-semibold text-body">Drop files
                                                                here or click to upload.</span>
                                                    </label>
                                                    <input type="file" id="inputGroupFile02" name="importFile" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary text-white" id="BulkImportSubmitButton">Upload
                                                File</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- end Modal -->
                    </div>
                </div>

                <!-- Filter Modal -->
                <div class="card bg-white border-0 rounded-10 mb-4">
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
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
                                                    <input type="date" class="form-control text-dark ps-5 h-58">
                                                    <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="label text-dark">End Date</label>
                                                <div class="form-group position-relative">
                                                    <input type="date" class="form-control text-dark ps-5 h-58">
                                                    <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Order Status</label>
                                        <div class="dropdown" id="dropdown_order_status">
                                            <div class="dropdown-toggle" id="dropdownToggle_order_status">
                                                <div class="selected-items" id="selectedItems_order_status"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_order_status">
                                                <div class="dropdown-list-item" data-value="pending">Pending</div>
                                                <div class="dropdown-list-item" data-value="shipped">Shipped</div>
                                                <div class="dropdown-list-item" data-value="cancelled">Cancelled</div>
                                                <div class="dropdown-list-item" data-value="declined">Declined</div>
                                                <div class="dropdown-list-item" data-value="disputed">Disputed</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Order Source</label>
                                        <div class="dropdown" id="dropdown_order_source">
                                            <div class="dropdown-toggle" id="dropdownToggle_order_source">
                                                <div class="selected-items" id="selectedItems_order_source"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_order_source">
                                                <div class="dropdown-list-item" data-value="amazon">Amazon</div>
                                                <div class="dropdown-list-item" data-value="coustom">Coustom</div>
                                                <div class="dropdown-list-item" data-value="shopify">Shopify</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Courier Partner</label>
                                        <div class="dropdown" id="dropdown_courier_partner">
                                            <div class="dropdown-toggle" id="dropdownToggle_courier_partner">
                                                <div class="selected-items" id="selectedItems_courier_partner"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_courier_partner">
                                                <div class="dropdown-list-item" data-value="smartr">Smartr</div>
                                                <div class="dropdown-list-item" data-value="ekart">Ekart</div>
                                                <div class="dropdown-list-item" data-value="bluedart">Bluedart</div>
                                                <div class="dropdown-list-item" data-value="delhivery">Delhivery</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Pickup Address</label>
                                        <div class="dropdown" id="dropdown_pickup_address">
                                            <div class="dropdown-toggle" id="dropdownToggle_pickup_address">
                                                <div class="selected-items" id="selectedItems_pickup_address"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_pickup_address">
                                                <div class="dropdown-list-item" data-value="exluses1">Exluses1</div>
                                                <div class="dropdown-list-item" data-value="ware@1">ware@1</div>
                                                <div class="dropdown-list-item" data-value="BMU">BMU</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Order Tag</label>
                                        <div class="dropdown" id="dropdown_order_tag">
                                            <div class="dropdown-toggle" id="dropdownToggle_order_tag">
                                                <div class="selected-items" id="selectedItems_order_tag"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_order_tag">
                                                <div class="dropdown-list-item" data-value="test_1">test 1</div>
                                                <div class="dropdown-list-item" data-value="test_2">test 2</div>
                                                <div class="dropdown-list-item" data-value="test_3">test 3</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark">Payment Option</label>
                                        <div class="dropdown" id="dropdown_payment_option">
                                            <div class="dropdown-toggle" id="dropdownToggle_payment_option">
                                                <div class="selected-items" id="selectedItems_payment_option"></div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_payment_option">
                                                <div class="dropdown-list-item" data-value="credit_card">Credit Card</div>
                                                <div class="dropdown-list-item" data-value="paypal">PayPal</div>
                                                <div class="dropdown-list-item" data-value="bank_transfer">Bank Transfer</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="label text-dark">SKU</label>
                                    <input type="number" class="form-control text-dark" placeholder="sku">
                                </div>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                        <label class="form-check-label text-dark" for="inlineRadio1">Single SKU</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                        <label class="form-check-label text-dark" for="inlineRadio2"> Multi SKU</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option3">
                                        <label class="form-check-label text-dark" for="inlineRadio2"> Match Exact</label>
                                    </div>
                                </div> <br>
                                <div class="form-group mb-4">
                                    <label class="label text-dark">Search Multiple Order Ids</label>
                                    <input type="number" class="form-control text-dark" placeholder="Search Multiple Order Ids">
                                </div>

                                <div class="form-group d-flex gap-3">
                                    <button class="btn btn-primary text-white fw-semibold py-2 px-2 px-sm-3">
                                        <span class="py-sm-1 d-block">
                                            <span class="text-light">Apply Filter</span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- end Modal -->

                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="table-responsive scroll-bar active" id="order-content-div">

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


@include('seller.pages.scripts')
<script>
    let selectedTab = 'action_required', currentPage=1, pageSize=20, totalPage = 1;
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
        $('#bulkupload').on('click', '#BulkImportSubmitButton', function () {
            if($('#bulkimportform').valid()){
                showOverlay();
                $('#bulkimportform').submit()
            }
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

        $('#export_ndr_order').click(function () {
            var ids = [];
            // var that = $(this);
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    ids.push($(this).val());
            });
            if(ids.length > 0) {
                location.href = "{{route('seller.export_ndr_order')}}?ids="+ids;
            } else {
                location.href = "{{route('seller.export_ndr_order')}}?ids=";
            }
        });

        $('#order-content-div').on('click', '#checkAllButton', function () {
            var that = $(this);
            if (that.prop('checked')) {
                $('.selectedCheck').prop('checked', true);
                $('#IVRAllButton').fadeIn();
                $('#ReattemptAllButton').fadeIn();
                $('#RTOAllOrderButton').fadeIn();
                $('.total_ndr_selected').html($('.ndr_display_limit').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                $('#IVRAllButton').hide();
                $('#ReattemptAllButton').hide();
                $('#RTOAllOrderButton').hide();
                $('.total_ndr_selected').html(0);
            }
        });

        $('#nav-tabContent').on('click', '.selectedCheck', function () {
            var cnt = 0;
            $('.selectedCheck').each(function () {
                if ($(this).prop('checked'))
                    cnt++;
            });
            if (cnt > 0) {
                $('#IVRAllButton').fadeIn();
                $('#ReattemptAllButton').fadeIn();
                $('#RTOAllOrderButton').fadeIn();

            } else {
                $('#IVRAllButton').hide();
                $('#ReattemptAllButton').hide();
                $('#RTOAllOrderButton').hide();
            }
            $('.total_ndr_selected').html(cnt);
        });

    });

    function loadPageData(){
        switch(selectedTab){
            case 'action_required':
                loadAllOrders();
                break;
            case 'action_requested':
                loadAllOrders();
                break;
            case 'delivered':
                loadAllOrders();
                break;
            case 'rto':
                loadAllOrders();
                break;
        }
    }
    function loadAllOrders(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-shipment')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }
    function loadAllManifest(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'pageSize': pageSize
            },
            url : '{{route('seller.load-all-shipment')}}',
            success : function (response){
                $('#order-content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }
    function setupPaginationData(response){
        totalPage = response.page.lastPage;
        $('#currentPageLabel').html(response.page.currentPage);
        $('#totalPageLabel').html(response.page.lastPage);
        $('#totalRecordLabel').html(response.page.totalRecord);
        $('#currentPageRecordLabel').html(response.page.current_count);
    }
</script>

</body>

</html>
