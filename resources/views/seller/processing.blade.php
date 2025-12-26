<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <title>Processing | {{$config->title}}</title>
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
                        <a class="" href="{{route('seller.all_order')}}" type="button">All Orders</a>
                    </div>
                    <!-- <div class="me-2" role="presentation">
                        <a class="" type="button" href="unprocessing.html">Unprocessing</a>
                    </div> -->
                    <div class="me-2" role="presentation">
                        <a class="active" type="button" href="{{route('seller.order_processing')}}">Processing</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Ready To Ship</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Manifest</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Delivered</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Returned</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Lost & Damaged</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="" href="#" type="button">Cancelled</a>
                    </div>
                </div>
                <div class="text-end d-flex justify-content-end align-items-center">
                    <a type="button" href="{{route('seller.createOrder')}}" class="btn btn-primary text-white fw-semibold me-2" >
                        <i class="ri-add-line"></i>Create Order
                    </a>
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
                                        <button type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#bulkupload">
                                            <i class="ri-stack-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Bulk Order Update"></i>
                                        </button>
                                        <a type="button" class="btn btn btn-primary text-white fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report"> <i class="ri-download-line"></i>
                                        </a>
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
                            <div class="table-responsive scroll-bar active">
                                <table class="table">
                                    <thead class="sticky-header">
                                        <tr class="text-center rounded-10">
                                            <th style="width: 40px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                                </div>
                                            </th>
{{--                                            <th class="text-center">Tracking Details</th>--}}
                                            <th class="text-center">Shipment Details</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Dimensional Details</th>
                                            <th class="text-center">Shipment Amount</th>
                                            <th class="text-center">Address Details</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order as $o)
                                        <tr class="text-center rounded-10  card-row">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="orderCheckbox1">
                                                    <label class="form-check-label" for="orderCheckbox1"></label>
                                                </div>
                                            </td>
{{--                                            <td>--}}
{{--                                                <div class="cell-inside-box ">--}}
{{--                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">X82125375</span> </p>--}}
{{--                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">DTDC </span></p>--}}
{{--                                                    <p> <span class="d-inline-flex align-items-center gap-1 ms-2"> 15 May 2023</span> </p>--}}
{{--                                                    <p> <span class="d-inline-flex align-items-center gap-1 ms-2">(10:18 am)</span> </p>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}

                                            <td class="text-center">
                                                <div class="cell-inside-box ">
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Order ID: {{$o->customer_order_number}}</span> </p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->product_name}} </span></p>
                                                    <p> <span class="d-inline-flex align-items-center gap-1 ms-2">(Quantity - {{$o->product_qty}})</span> </p>
                                                    <p> <span class="d-inline-flex align-items-center gap-1 ms-2">SKU - {{$o->product_sku}}</span> </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-inside-box ">
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 order-Status-box">pending</span> </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-inside-box ">
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Dimension - {{$o->length}} * {{$o->breadth}} * {{$o->height}}</span> </p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">Weight - {{$o->weight}} KG</span></p>
                                                    <p> <span class="d-inline-flex align-items-center gap-1 ms-2">Vol. Wt-0.20 KG</span> </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-inside-box ">
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$o->invoice_amount}}</span> </p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 fw-semibold text-success">{{ucfirst($o->order_type)}}</span></p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cell-inside-box ">
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$o->s_customer_name}}</span> </p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_address_line1}}, {{$o->s_address_line2}}</span></p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_city}}, {{$o->s_state}}</span></p>
                                                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_pincode}}</span></p>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <a type="button" class="btn btn-sm btn-warning fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="Ship Order">
                                                        <i class="ri-truck-line"></i>
                                                    </a>

                                                    <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Order">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-footer ">
                        <div class="my-2">
                            <div class="pagination-container">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="pagination">
                                        <a class="prev-page"><img src="{{asset('assets/sellers/')}}/images/1.svg" alt=""></a>
                                        <a class="prev-page"><img src="{{asset('assets/sellers/')}}/images/2.svg" alt=""></a>
                                         <span class="page-info text-dark">1 </span>
                                            <span class="page-info text-dark">of</span>
                                            <span class="page-info text-dark">2</span>
                                        <a class="next-page"><img src="{{asset('assets/sellers/')}}/images/3.svg" alt=""></a>
                                        <a class="next-page"><img src="{{asset('assets/sellers/')}}/images/4.svg" alt=""></a>
                                    </div>
                                    <div class="go-to-page text-dark">Go to page: <input type="number" min="1" max="73" value=""><button class="go-btn">Go</button>
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
                </div>
            </div>
        </div>
        <!-- mode -->
    </div>


    @include('seller.pages.scripts')
    <script>
        $(document).ready(function (){

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
        });
    </script>
</body>

</html>
