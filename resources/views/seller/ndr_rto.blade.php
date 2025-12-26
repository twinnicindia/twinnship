<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    {{--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
    <title>NDR </title>
</head>

<body>

@include('seller.pages.header')
@include('seller.pages.sidebar')

<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="nav-scroll scroll-bar active card mb-4 col-12">
            <div class="tablist mt-3" id="pills-tab" role="tablist">
                <div class="me-2" role="presentation">
                    <a class="" href="{{route('seller.ndr_orders')}}" type="button">Action Required</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="" href="{{route('seller.ndr_action_requested')}}" type="button">Action Requested</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="" type="button" href="{{route('seller.ndr_delivered')}}">Delivered</a>
                </div>
                <div class="me-2" role="presentation">
                    <a class="active" href="{{route('seller.ndr_rto')}}" type="button">RTO</a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex card-row p-2 justify-content-between align-items-center">

                    <div class="mb-3 mt-3">
                        <button type="button" class=" p-2 btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                            <i class="ri-filter-line me-2"></i>More Filter
                        </button>
                    </div>
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
                    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">
                        <div class="icon transition me-5 mt-3">
                            <div class="text ptext">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#exampleModal4">
                                        <i class="ri-stack-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Bulk Order Update"></i>
                                    </button>
                                    <a type="button" class="btn btn btn-primary text-white fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report"> <i class="ri-download-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content justify-content-center">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Bulk Update New
                                            Orders via CSV</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <a href="#"><i class="ri-download-line"></i>Download Sample CSV
                                            File</a>
                                    </div>
                                    <div class="form-group  text-center">
                                        <div class="form-control h-100 w-80 text-center position-relative p-5 p-lg-5">
                                            <div class="product-upload">
                                                <label for="file-upload" class="file-upload mb-0">
                                                    <i class="ri-upload-cloud-2-line fs-2 text-gray-light"></i>
                                                    <span class="d-block fw-semibold text-body">Drop files
                                                            here or click to upload.</span>
                                                </label>
                                                <input id="file-upload" type="file">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancle</button>
                                        <button type="button" class="btn btn-primary text-white">Upload
                                            File</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end Modal -->
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
                                                    <i
                                                        class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
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
                    <div class="col-12">
                        <div class="card-body">
                            <div class="table-responsive  scroll-bar active">
                                <table class="table">
                                    <thead class="sticky-header">
                                    <tr class="text-center rounded-10">
                                        <th style="width: 40px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="selectAll">
                                            </div>
                                        </th>
                                        <th>Order Details</th>
                                        <th>NDR Reason</th>
                                        <th>Package Details</th>
                                        <th>Customer details</th>
                                        <th>Tracking Detail</th>
                                        <th>Delivery Address</th>
                                        <th>Escalation Information</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($ndr_data as $n)
                                        <tr id="row{{$n->id}}">
                                            <td><input type="checkbox" class="selectedCheck" value="{{$n->id}}"></td>
                                            <td>
                                                {{date('d-m-Y',strtotime($n->ndr_raised_time))}}<br>
                                                <a href="{{url("/view-order/$n->id")}}" target="_blank">
                                                    <span class="text-primary">{{$n->customer_order_number}}</span>
                                                </a>
                                                <span class="text-primary font-weight-bold text-capitalize">Action Required</span><br>
                                                <span class="badge {{$n->o_type =='forward'?'badge-success':'badge-danger'}}">{{$n->o_type}}</span>
                                                <span class="badge badge-success">{{$n->order_type}}</span>
                                                @if($n->shipment_type == 'mps')<span class="badge badge-primary pl-2">MPS</span>@endif
                                            </td>
                                            <td>
                                                {{count($n->ndrattempts) == 0 ? 1 : count($n->ndrattempts)}} Attempts &nbsp;<i class="fas fa-eye text-primary ndrHistory" data-id="1" data-id="{{$n->id}}"></i><br>
                                                Status : <span class="text-capitalize">{{$n->ndr_action}}</span><br>
                                                Last NDR : {{strlen($n->reason_for_ndr) > 15 ? substr("$n->reason_for_ndr",0,15)."..." : $n->reason_for_ndr}}
                                            </td>
                                            <td>
                                                Name : {{strlen($n->product_name) > 15 ? substr("$n->product_name",0,15)."..." : $n->product_name}}<br>
                                                SKU : {{strlen($n->product_sku) > 15 ? substr("$n->product_sku",0,15)."..." : $n->product_sku}} <br>
                                                Qty : {{$n->product_qty ?? 1}} &nbsp;<a href="javascript:;" class=" mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="Name : @foreach(explode(',', $n->product_name) as $name) {{$name}} @endforeach <br> SKU : @foreach(explode(',', $n->product_sku) as $sku) {{$sku}} @endforeach <br> QTY : {{$n->product_qty ?? 1}}"><i class="fas fa-eye text-primary"></i></a>
                                            </td>
                                            <td>
                                                {{$n->s_customer_name}}<bR>
                                                {{$n->s_contact}}<bR>
                                            </td>
                                            <td>
                                                {{$PartnerName[$n->courier_partner] ?? ""}}<bR>
                                                <span class="text-primary"><a href='{{url("track-order/$n->awb_number")}}' target="_blank">{{$n->awb_number}}</a></span><bR>
                                            </td>
                                            <td>
                                                {{$n->s_state}} <br>
                                                {{$n->s_city}} <br>
                                                {{$n->s_pincode}} <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$n->s_address_line1}} <br> {{$n->s_address_line2}} <br> {{$n->s_city}} {{$n->s_state}} {{$n->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
                                            </td>
                                            <td>
                                                N/A
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="row1">
                                            <td><input type="checkbox" class="selectedCheck" value="1"></td>
                                            <td>
                                                15-06-2024<br>
                                                <a href="https://example.com/view-order/1" target="_blank">
                                                    <span class="text-primary">ORD123456</span>
                                                </a>
                                                <span class="text-primary font-weight-bold text-capitalize">Action Required</span><br>
                                            </td>
                                            <td>
                                                2 Attempts &nbsp;<i class="fas fa-eye text-primary ndrHistory" data-id="1"></i><br>
                                                Status : <span class="text-capitalize">Pending</span><br>
                                                Last NDR : Address not found...
                                            </td>
                                            <td>
                                                Name: Product A<br>
                                                SKU: SKU123<br>
                                                Qty: 1 &nbsp;<a href="javascript:;" class="mx-0" data-placement="top" data-toggle="tooltip" data-html="true" data-original-title="Name: Product A<br>SKU: SKU123<br>Qty: 1" style="display: inline"><i class="fas fa-eye text-primary"></i></a>
                                            </td>
                                            <td>
                                                John Doe<br>
                                                +1234567890<br>
                                            </td>
                                            <td>
                                                CourierX<br>
                                                98009767778878
                                            </td>
                                            <td>
                                                New York<br>
                                                Manhattan<br>
                                                10001 &nbsp;<a href="javascript:;" class="mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="1234 Broadway<br>Suite 567<br>Manhattan, NY, 10001" style="display: inline"><i class="fas fa-eye text-primary"></i></a>
                                            </td>
                                            <td>
                                                N/A
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-footer">
                        <div class="my-2">
                            <div class="pagination-container">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="pagination">
                                        <a class="prev-page"><img src="{{url('/')}}/assets/sellers/images/1.svg" alt=""></a>
                                        <a class="prev-page"><img src="{{url('/')}}/assets/sellers/images/2.svg" alt=""></a>
                                        <span class="page-info text-dark">1 </span>
                                        <span class="page-info text-dark">of</span>
                                        <span class="page-info text-dark">2</span>
                                        <a class="next-page"><img src="{{url('/')}}/assets/sellers/images/3.svg" alt=""></a>
                                        <a class="next-page"><img src="{{url('/')}}/assets/sellers/images/4.svg" alt=""></a>
                                    </div>
                                    <div class="go-to-page text-dark">Go to page: <input type="number" min="1"
                                                                                         max="73" value=""><button class="go-btn">Go</button>
                                    </div>
                                </div>
                                <div class="result-count text-dark">Showing 5 of 6 records.</div>
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

        <!-- 	NDR Reason Modal -->
        <div class="modal fade" id="ndrreason" tabindex="-1" aria-labelledby="ndrreasonLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">NDR Attempt History</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                            <tr class="text-center rounded-10">
                                <th>Raised Date</th>
                                <th>Action By</th>
                                <th>Reason</th>
                                <th>Remark</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End NDR Reason Modal -->
    </div>
</div>
<!-- Escalate Modal -->
<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="Escalate"
         aria-labelledby="EscalateLabel">
        <div class="offcanvas-header border-bottom p-4">
            <h5 class="offcanvas-title fs-18 mb-0" id="EscalateLabel">Escalate</h5>
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
                                    <i
                                        class="ri-calendar-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
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

<!-- Recharge Modal -->
<div class="modal fade" id="Rechargemodel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Upgrade Your Shipping Limit</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="payment_option">
                    <div class="row mb-3">
                        <div class="col">
                            <span class="fs-3 fw-semibold">₹0.00</span>
                            <h5 class="text-primary me-2" style="white-space: nowrap;">Available Balance</h5>
                        </div>
                        <div class="col">
                            <span class="fs-3 fw-semibold">₹0.00</span>
                            <h5 class="text-danger">Hold Balance</h5>
                        </div>
                        <div class="col">
                            <span class="fs-3 fw-semibold">₹0.00</span>
                            <h5 class="text-primary">Usable Amount</h5>
                        </div>
                    </div>

                    <h5 class="text-primary fw-semibold mb-4 mt-2"><b>Your wallet has been migrated to Twinnship Dashboard</b></h5>
                    <div class="card-row border-0 bg-light-primary">
                        <p class="label">Enter the amount for your recharge </p>
                        <div class="form-group row" id="data_amount">
                            <label for="inputPassword" class="col-sm-3 text-right label">Amount :</label>
                            <div class="col-sm-9">
                                <input  type="number" autocomplete="off" name="filter" class="form-control bg-white border-0 text-dark rounded-pill"  id="recharge_wallet_amount" placeholder="Enter Amount" value="500">
                            </div>
                            <span class="label mt-3">Or Select amount for quick recharge</span>
                            <div class="col-sm-12 text-center ">
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="500">500</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="1000">1000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="2000">2000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="5000">5000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="10000">10000</button>
                            </div>
                            <span class="label mt-3">Have a coupon? Enter code to validate</span>
                            <div class="form-group mb-4 position-relative">
                                <input type="text" class="form-control bg-white border-0 text-dark rounded-pill" placeholder="Enter Coupon">
                                <button type="submit" class="position-absolute top-50 end-0 translate-middle-y bg-primary p-0 border-0 text-center text-white rounded-pill px-3 py-2 me-2 fw-semibold">
                                    Validate
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger text-white"data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary text-white">Recharge</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end model -->

@include('seller.pages.scripts')
</body>

</html>
