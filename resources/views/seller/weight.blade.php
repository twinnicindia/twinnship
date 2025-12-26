<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Weight Reconciliation | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>

<body>

@include('seller.pages.header')
@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="nav-scroll scroll-bar active card mb-4 col-12">
            <div class="tablist mt-3" id="pills-tab" role="tablist">
                <div class="me-2" role="presentation">
                    <a class="nav-buttons active" href="javascript:" data-tab="weight" type="button">Weight Reconciliation</a>
                </div>
{{--                <div class="me-2" role="presentation">--}}
{{--                    <a class="nav-buttons" href="javascript:" type="button" data-tab="on-hold">On-Hold Reconciliation</a>--}}
{{--                </div>--}}
{{--                <div class="me-2" role="presentation">--}}
{{--                    <a class="nav-buttons" type="button" href="javascript:" data-tab="settled">Settled Reconciliation</a>--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex card-row p-2 justify-content-between align-items-center">
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
                                    <a type="button" class="btn btn btn-primary text-white fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report"> <i class="ri-download-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Modal -->
                <div class="card bg-white border-0 rounded-10 mb-4">
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                         aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header border-bottom p-4">
                            <h5 class="offcanvas-title fs-18 mb-0" id="offcanvasRightLabel">Explore Additional
                                Filters</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
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
                                                    <i
                                                        class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
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
                                                <div class="dropdown-list-item" data-value="cancelled">Cancelled
                                                </div>
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
                                                <div class="selected-items" id="selectedItems_courier_partner">
                                                </div>
                                                <span class="dropdown-icon">&#9660;</span>
                                            </div>
                                            <div class="dropdown-list" id="dropdownList_courier_partner">
                                                <div class="dropdown-list-item" data-value="smartr">Smartr</div>
                                                <div class="dropdown-list-item" data-value="ekart">Ekart</div>
                                                <div class="dropdown-list-item" data-value="bluedart">Bluedart</div>
                                                <div class="dropdown-list-item" data-value="delhivery">Delhivery
                                                </div>
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
                                                <div class="dropdown-list-item" data-value="credit_card">Credit Card
                                                </div>
                                                <div class="dropdown-list-item" data-value="paypal">PayPal</div>
                                                <div class="dropdown-list-item" data-value="bank_transfer">Bank
                                                    Transfer</div>
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
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                               id="inlineRadio1" value="option1">
                                        <label class="form-check-label text-dark" for="inlineRadio1">Single
                                            SKU</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                               id="inlineRadio2" value="option2">
                                        <label class="form-check-label text-dark" for="inlineRadio2"> Multi
                                            SKU</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                               id="inlineRadio2" value="option3">
                                        <label class="form-check-label text-dark" for="inlineRadio2"> Match
                                            Exact</label>
                                    </div>
                                </div> <br>
                                <div class="form-group mb-4">
                                    <label class="label text-dark">Search Multiple Order Ids</label>
                                    <input type="number" class="form-control text-dark"
                                           placeholder="Search Multiple Order Ids">
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
                    <div id="data-div">
                        <div class="col-12">
                            <div class="card-body">
                                <div class="table-responsive  scroll-bar active" id="content-div">

                                </div>
                            </div>
                        </div>
                        <div class="table-footer">
                            <div class="my-2">
                                <div class="pagination-container">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="pagination">
                                            <div class="pagination">
                                                <a class="first-page"><img src="{{asset('assets/sellers/')}}/images/1.svg" alt=""></a>
                                                <a class="prev-page"><img src="{{asset('assets/sellers/')}}/images/2.svg" alt=""></a>
                                                <span class="page-info text-dark" id="currentPageLabel">1</span>
                                                <span class="page-info text-dark">of</span>
                                                <span class="page-info text-dark" id="totalPageLabel">2</span>
                                                <a class="next-page"><img src="{{asset('assets/sellers/')}}/images/3.svg" alt=""></a>
                                                <a class="last-page"><img src="{{asset('assets/sellers/')}}/images/4.svg" alt=""></a>
                                            </div>
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

                    <div class="col-12" id="data_div_weight" style="display: none;">
                    </div>
                </div>

            </div>
        </div>


        <!-- 	NDR Reason Modal -->
        <div class="modal fade" id="exampleModalxl" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <table class="table">
                        <thead class="sticky-header">
                        <tr class="text-center rounded-10">

                            <th>Weight Discrepancy Date</th>
                            <th>Status</th>
                            <th>Charged Weight (KG)</th>
                            <th>Action Taken by	</th>
                            <th>Applied Weight</th>
                            <th>Remark  </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- End NDR Reason Modal -->

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
                                        <input type="text" class="form-control" placeholder="Remark" id="remark" name="remark" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Images</label>
                                        <input type="file" class="form-control" id="dispute_images" name="dispute_images[]" multiple required accept="image/*">
                                    </div>
                                </div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-info btn-sm">Dispute</button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="disputeModalCloseBtn" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('seller.pages.scripts')
<script>
    let selectedTab = 'weight', currentPage=1, pageSize=20, totalPage = 1;
    $(document).ready(function () {
        loadWeightData();
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

        $('#content-div').on('click', '.DisputeButton', function() {
            $('#disputeOrder').modal('show');
            $('#weight_rec_id').val($(this).data('id'));
        });

        $('#content-div').on('click', '.AcceptButton', function() {
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


        $('#disputeModalCloseBtn').click(function() {
            $('#disputeOrder').modal('hide');
        });

        $('#data_div_weight').on('click', '#closeHistoryBtn', function() {
            $('#AddCommentWeight').modal('hide');
        });

        $('#content-div').on('click', '.ViewHistory', function() {
            showOverlay();
            var that = $(this);

            $.ajax({
                url: '{{url('/')."/get_history_weight_reconciliation/"}}' + that.data('id'),
                success: function(response) {
                    $('#data-div').hide();
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

        $('#data_div_weight').on('click', '.BackButton', function() {
            $('#data_div_weight').hide();
            $('#data-div').show();
        });

        $('#data_div_weight').on('click', '.AddComment', function() {
            $('#AddCommentWeight').modal('show');
        });

    });

    function loadPageData(){
        switch(selectedTab){
            case 'weight':
            case 'on-hold':
                loadWeightData();
                break;
            case 'settled':
                loadSettledData();
                break;
        }
    }

    function loadWeightData(){
        $.ajax({
            type : 'post',
            data : {
                '_token': '{{csrf_token()}}',
                'page': currentPage,
                'tab': selectedTab,
                'pageSize': pageSize
            },
            url : '{{route('seller.billing_weight_reconciliation')}}',
            success : function (response){
                $('#content-div').html(response.content);
                setupPaginationData(response);
            }
        });
    }

    function loadSettledData(){

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
