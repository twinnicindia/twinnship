<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <title>More On Order | {{$config->title}}</title>
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
                        <a class="nav-buttons active" data-tab="reassign" href="javascript:" type="button">Reassign</a>
                    </div>
                    <!-- <div class="me-2" role="presentation">
                        <a class="" type="button" href="unprocessing.html">Unprocessing</a>
                    </div> -->
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons split" data-tab="split" type="button" href="javascript:">Split</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons merge" data-tab="merge" href="javascript:" type="button">Merge</a>
                    </div>
                </div>
{{--                <div class="text-end d-flex justify-content-end align-items-center">--}}
{{--                    <a type="button" href="{{route('seller.createOrder')}}" class="btn btn-primary text-white fw-semibold me-2" >--}}
{{--                        <i class="ri-add-line"></i>Create Order--}}
{{--                    </a>--}}
{{--                </div>--}}
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">
                        <div class="mb-3 mt-3 me-2">
                            <div>
                                <form class="src-form position-relative" >
                                    <input type="text" class="form-control" placeholder="Search here AWB...." style="border: 1px solid; color: black;" id="awb_list">
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
                                        <button type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#bulkupload" title="Bulk Import Orders">
                                            <i class="ri-stack-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Bulk Order Update"></i>
                                        </button>
                                        <!-- <a type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report"> <i class="ri-download-line"></i>
                                        </a> -->
                                        <button type="button" class="btn btn btn-primary text-white fw-semibold me-2 exportOrderButton"  title="Export Orders">
                                            <i class="ri-upload-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Orders"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="bulkupload" tabindex="-1" aria-labelledby="bulkupload" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content justify-content-center">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Bulk Update New
                                                Orders via CSV</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <a href="{{asset('assets/sellers/twinnship.csv')}}" style="color: blue;"><i class="ri-download-line"></i>Download Sample CSV
                                                File</a>
                                        </div>
                                        <form method="post" action="{{route('seller.import_csv_order')}}" enctype="multipart/form-data" id="bulkimportform">
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
                                                    <input type="date" class="form-control text-dark ps-5 h-58" id="start_date">
                                                    <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="label text-dark">End Date</label>
                                                <div class="form-group position-relative">
                                                    <input type="date" class="form-control text-dark ps-5 h-58" id="end_date">
                                                    <i class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark" for="example-multiselect">Order Status</label>
                                        <select id="filterOrderStatus" multiple="multiple" class="form-control multiSelectDropDown">
                                            <option value="pending">Pending</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="pickup_scheduled">Pickup Scheduled</option>
                                            <option value="in_transit">In Transit</option>
                                            <option value="delivered">Delivered</option>
                                        </select>
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
                                    <button type="button" id="applyFilter" class="btn btn-primary text-white fw-semibold py-2 px-2 px-sm-3">
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
                                        <option value="100000">All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
          <!-- mode -->
        <div class="modal fade" id="courier_partner_select" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Shipping Partner</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('seller.single_ship_order')}}" method="post" name="singleForm" id="singleForm">
                        <div class="modal-body" id="partner_details_ship">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- mode -->
    </div>
</div>


    @include('seller.pages.scripts')
    <script type="text/javascript">
        let selectedTab = 'reassign', currentPage=1, pageSize=20, totalPage = 1;
        $(document).ready(function (){
            loadPageData();
            @if(($_GET['type'] ?? "") != "")
                selectedTab = '{{$_GET['type']}}';
                $('.nav-buttons').removeClass('active');
                $('.{{$_GET['type']}}').addClass('active');
                currentPage = 1;
                pageSize = 20;
                loadPageData();
            @endif
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
            $('#order-content-div').on('click','#selectAllCheckBox',function (){
                let that = $(this);
                if(that.prop('checked')){
                    $('#order-content-div .selectCheckBoxes').prop('checked',true);
                }else{
                    $('#order-content-div .selectCheckBoxes').prop('checked',false);
                }
            });
            $('.exportOrderButton').click(function (){
                let selectedIds = [];
                $('#order-content-div .selectCheckBoxes').each(function() {
                    let that = $(this);
                    if (that.prop('checked')) {
                        selectedIds.push(that.val());
                    }
                });
                $.ajax({
                    url: '{{ route('seller.export-order-data') }}',
                    type: 'POST',
                    data: {
                        'selected_tab': selectedTab,
                        'selected_ids': selectedIds,
                        'filters': [],
                        '_token': '{{ csrf_token() }}'
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        var blob = new Blob([data], { type: 'text/csv' });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'order-export.csv';
                        link.click();
                    },
                    error: function(data) {
                        console.error('Error:', data);
                    }
                });
            });
            $('.rows-per-page').change(function (){
                let that = $(this);
                pageSize = that.val();
                currentPage = 1;
                loadPageData();
            });

            $('#order-content-div').on('click','.shipOrderButton',function (){
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
            $('#applyFilter').click(function (){
                console.log($('#filterOrderStatus').val());
            });
        });
        function loadPageData(){
            switch(selectedTab){
                case 'reassign':
                    loadAllOrders();
                    break;
                case 'merge':
                    loadAllMerge();
                    break;
                case 'split':
                    loadAllSplit();
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
                url : '{{route('seller.load-all-moreonorder')}}',
                success : function (response){
                    $('#order-content-div').html(response.content);
                    setupPaginationData(response);
                }
            });
        }
        function loadAllSplit(){
            $.ajax({
                type : 'post',
                data : {
                    '_token': '{{csrf_token()}}',
                    'page': currentPage,
                    'tab': selectedTab,
                    'pageSize': pageSize
                },
                url : '{{route('seller.load-all-split')}}',
                success : function (response){
                    $('#order-content-div').html(response.content);
                    setupPaginationData(response);
                }
            });
        }

        function loadAllMerge(){
            $.ajax({
                type : 'post',
                data : {
                    '_token': '{{csrf_token()}}',
                    'page': currentPage,
                    'tab': selectedTab,
                    'pageSize': pageSize
                },
                url : '{{route('seller.load-all-merge')}}',
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
