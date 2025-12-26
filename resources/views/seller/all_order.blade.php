<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <title>All Order | {{$config->title}}</title>
    <style type="text/css">
        .multiselect-container{
            left: 15% !important;
        }
    </style>
</head>

<body>

    @include('seller.pages.header')
    @include('seller.pages.side_links')

    <form action="{{route('seller.multipleLableDownload')}}" method="post" id="MultilabelForm">
        @csrf
        <input type="hidden" name="multilable_id" id="multilable_id">
    </form>

    <div id="create-order-div" style="display:none" class="container-fluid">
        <div class="main-content d-flex flex-column">
            <div class="row justify-content-center">
                <div class="col-xxl-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="content-inner col-12" id="form_div">
                            <form id="order_form" method="post" action="{{route('seller.add_order')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="order_id" id="oid">
                                <div class="card-row mb-2 order-form">
                                    <div class="card-header p-3">
                                        <h4>Add New Order</h4>
                                        <div class="float-right">
                                            <a href="javascript:" id="go_back"><button type="button" class="btn btn-primary btn-sm"><i class="fal fa-arrow-alt-left"></i> Go Back</button></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-row mb-2 order-form">
                                    <div class="card-header p-3">
                                        <h5>Order Information</h5>
                                    </div>
                                    <div class="card-body all_tabs" id="order_tab">
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-2 me-3">
                                                <div class="form-group">
                                                    <label class="label">Order Id</label>
                                                    <input type="text" class="form-control" placeholder="Order Id"
                                                           id="customer_order_number" name="customer_order_number" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 me-3">
                                                <div class="form-group">
                                                    <label class="label">Payment Type</label>
                                                    <div class="input-group mb-3">
                                                        <select class="custom-select" id="order_type" name="order_type"
                                                                required>
                                                            <option value="prepaid" selected id="type_prepaid">Prepaid</option>
                                                            <option value="cod" id="type_cod">Cash on Delivery
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 ">
                                                <div class="form-group">
                                                    <label class="mb-3 label">Order Type</label><br>
                                                    <input type="radio" name="o_type" value="forward" checked
                                                           id="o_type_forward"> <span class="text-dark me-2">Forward </span>
                                                    <input type="radio" name="o_type" value="reverse"
                                                           id="o_type_reverse"> <span class="text-dark">  Reverse </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-1 col-md-1 col-sm-1">
                                                <div class="form-group">
                                                    <label class="mb-3 label">MPS</label><br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="isMPS" id="mps_checkbox">
                                                        <span class="slider"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2" id="number_of_packets_div" style="display: none;">
                                                <div class="form-group">
                                                    <label class="label">Number of packets</label>
                                                    <input type="number" class="form-control numberonly" placeholder="Number of packets" id="number_of_packets" name="number_of_packets" min="1" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="label">Customer Name</label>
                                                    <input type="text" class="form-control" placeholder="Customer Name"
                                                           id="customer_name" name="customer_name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="label" for="country">Mobile Number</label>
                                                    <div class="input-group h-58 form-control p-0">
                                                        <div class="input-group-text rounded-10">
                                                            <select class="input-group-text rounded-10" id="country"
                                                                    name="country">
                                                                <option value="+91">+91</option>
                                                                <option value="+07">+07</option>
                                                                <option value="+01">+01</option>
                                                            </select>
                                                        </div>
                                                        <input type="number" class="form-control h-auto border-0 text-dark"
                                                               maxlength="10" placeholder="Phone Number" id="contact" name="contact" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group">
                                                    <label class="label">Address</label>
                                                    <textarea type="text" class="form-control" rows="3"
                                                              placeholder="Address 1" id="address" name="address"
                                                              required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group">
                                                    <label class="label">Address 2 (optional)</label>
                                                    <textarea type="text" class="form-control" rows="3"
                                                              placeholder="Address 2" id="address2"
                                                              name="address2"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="label">Pincode</label>
                                                    <input type="number" class="form-control" placeholder="Pincode"
                                                           id="pincode" name="pincode" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="label">City</label>
                                                    <input type="text" class="form-control" placeholder="City" id="city"
                                                           name="city" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="label">State</label>
                                                    <input type="text" class="form-control" placeholder="State"
                                                           id="state" name="state" required>

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="label">Country</label>
                                                    <input type="text" class="form-control" placeholder="Country"
                                                           id="txtCountry" name="country" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center mt-5">
                                            <button class="btn btn btn-primary text-white fw-semibold me-2 pull-right"
                                                    id="orderTabButton" type="button">Next</button>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-row mb-2 order-form">
                                    <div class="card-header p-3">
                                        <h5>Product Information</h5>
                                    </div>
                                    <div class="card-body all_tabs" id="product_tab" style="display:none;">
                                        <div class="table-responsive">
                                            <table class="table-hover" id="item_table">
                                                <thead>
                                                <tr>
                                                    <th class="text-dark">Product</th>
                                                    <th class="text-dark">SKU</th>
                                                    <th class="text-dark">Quantity</th>
                                                    <th class="text-dark">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="product_details">
                                                <tr>
                                                    <td><input type="text" data-id="1" id="product_name1"
                                                               name="product_name[]" class="form-control product_name product_requierd"
                                                               required="" placeholder="Product Name" /></td>
                                                    <td><input type="text" data-id="1" id="product_sku1"
                                                               name="product_sku[]" class="form-control product_sku product_requierd"
                                                               placeholder="Product SKU" /></td>
                                                    <td><input type="number" data-id="1" id="product_qty1"
                                                               name="product_qty[]" class="form-control product_qty product_requierd"
                                                               required="" value="1" placeholder="Product Quantity" />
                                                    </td>
                                                    <td>
                                                        <button type="button" name="add"
                                                                class="btn btn-info btn-sm add"><i
                                                                class="ri-add-line"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label" for="weight">Weight (kg)</label>
                                                    <input type="text" class="form-control weightfield"
                                                           placeholder="Weight (In Kg.)" id="weight" name="weight"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label" for="length">Length (cm)</label>
                                                    <input type="text" class="form-control" placeholder="Length"
                                                           id="length" name="length" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label" for="breadth">Breadth (cm)</label>
                                                    <input type="text" class="form-control" placeholder="Breadth"
                                                           id="breadth" name="breadth" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label" for="height">Height (cm)</label>
                                                    <input type="text" class="form-control" placeholder="Height"
                                                           id="height" name="height" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">Invoice Amount</label>
                                                    <input type="text" class="form-control" placeholder="Invoice Amount"
                                                           id="invoice_amount" name="invoice_amount">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">Shipping Charges</label>
                                                    <input type="number" class="form-control"
                                                           placeholder="Shiping Charges" id="shipping_charges"
                                                           name="shipping_charges">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">COD charges</label>
                                                    <input type="number" class="form-control" placeholder="Cod Charges"
                                                           id="cod_charges" name="cod_charges">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">Discount</label>
                                                    <input type="number" class="form-control" placeholder="Discount"
                                                           id="discount" name="discount">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">Reseller Name</label>
                                                    <input type="number" class="form-control" placeholder="Reseller Name"
                                                           id="Reseller Name" name="ResellerName">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2">
                                                <div class="form-group">
                                                    <label class="label">Collectable Amount</label>
                                                    <input type="number" class="form-control" placeholder="collectable_amount"
                                                           id="collectable_amount" name="collectable_amount">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mt-2" id="ewaybillDiv" style="display: none;">
                                                <div class="form-group">
                                                    <label class="label">E-Way Bill Number</label>
                                                    <input type="text" class="form-control" placeholder="E-Way Bill Number" id="ewaybill_number" name="ewaybill_number" value="" maxlength="30">
                                                </div>
                                            </div>
                                        </div>
                                        <div  class="text-end mt-3 d-flex flex-grow-1 justify-content-end align-items-center">
                                            <button class="btn btn btn-primary text-white fw-semibold me-2 pull-right"
                                                    id="PreviousTabButton2" type="button">Previous</button>
                                            <button class="btn btn btn-primary text-white fw-semibold me-2 pull-right"
                                                    id="ProductTabButton" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-row mb-2 order-form">
                                    <div class="card-header p-3">
                                        <h5>Warehouse Information <span class="h6"
                                                                        id="reverse_warehouse_message"></span></h5>
                                    </div>
                                    <div class="card-body warehouse_card all_tabs" id="warehouse_tab"
                                         style="display:none;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h6>Select Warehouse</h6>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" id="searchWarehouseKeyword" class="form-control" placeholder="Search Warehouse">
                                            </div>
                                            <div class="col-md-3 offset-3 align-content-end">
                                                <button type="button" class="btn btn-outline-info align-items-end createNewWarehouseButton">Create New Warehouse</button>
                                            </div>
                                        </div>
                                        <div class="row allWarehouseList m-1">

                                        </div>
                                        <div  class="text-end mt-3 d-flex flex-grow-1 justify-content-end align-items-center">
                                            <button class="btn btn-primary pull-right me-2" id="PreviousOtherTabButton"
                                                    type="button"> Previous
                                            </button>
                                            <button class="btn btn-primary pull-right" id="SubmitOrderData"
                                                    type="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div id="display-order-div" class="container-fluid">
        <div class="main-content d-flex flex-column">
        <div class="nav-scroll scroll-bar active card mb-4 col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tablist mt-3" id="pills-tab" role="tablist">
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons active" data-tab="all" href="javascript:" type="button">All Orders</a>
                    </div>
                    <!-- <div class="me-2" role="presentation">
                        <a class="" type="button" href="unprocessing.html">Unprocessing</a>
                    </div> -->
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons processing" data-tab="processing" type="button" href="javascript:">Processing</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons ready_to_ship" data-tab="ready_to_ship" href="javascript:" type="button">Ready To Ship</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons manifest" data-tab="manifest" href="javascript:" type="button">Manifest</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons live_orders" data-tab="live_orders" href="javascript:" type="button">Live Orders</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons delivered" data-tab="delivered" href="javascript:" type="button">Delivered</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons returns" data-tab="returns" href="javascript:" type="button">Returned</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons lost_damaged" data-tab="lost_damaged" href="javascript:" type="button">Lost & Damaged</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-buttons cancelled" data-tab="cancelled" href="javascript:" type="button">Cancelled</a>
                    </div>
                </div>
                <div class="text-end d-flex justify-content-end align-items-center">
                    <a type="button" href="javascript:" id="create-order-button" class="btn btn-primary text-white fw-semibold me-2" >
                        <i class="ri-add-line"></i>Create Order
                    </a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">
                        <div class="mb-3 mt-3 me-2">
                            <div>
                                <form class="src-form position-relative" >
                                    <input type="text" class="form-control" placeholder="Search here AWB...." style="border: 1px solid; color: black;" id="filterAWBList">
                                    <button type="button"
                                        class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0 applyFilterButton">
                                        <i data-feather="search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <button type="button" class="clearFilterButton p-2 btn btn btn-primary text-white fw-semibold me-2">
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
                                        <button style="display:none;" data-id="" type="button" class="btn btn-sm btn-warning fw-semibold me-2 bulk_ship_order" data-status="" data-bs-toggle="tooltip" data-bs-placement="top" >
                                            <i class="ri-truck-line"></i>
                                        </button>
                                        <button style="display:none;" type="button" class="btn btn-sm btn-danger fw-semibold me-2 bulk_cancel_order" data-number="" data-bs-toggle="tooltip"
                                            data-bs-placement="top" >
                                                <i class="ri-close-fill"></i>
                                        </button>
                                        <button style="display:none;" type="button" class="btn btn-sm btn-danger fw-semibold me-2 bulk_cancel_order" data-number="" data-bs-toggle="tooltip"
                                                data-bs-placement="top" >
                                            <i class="ri-close-fill"></i>
                                        </button>
                                        <button style="display:none;" type="button" class="btn btn-sm btn-primary fw-semibold me-2 bulkDownloadLabel" title="Download Label" data-number="" data-bs-toggle="tooltip"
                                            data-bs-placement="top" >
                                                <i class="ri-download-2-line"></i>
                                        </button>
                                        <button type="button" class="btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="modal" data-bs-target="#bulkupload" title="Bulk Import Orders">
                                            <i class="ri-stack-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Bulk Order Update"></i>
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
                                        <label class="label text-dark" for="filterOrderStatus">Order Status</label>
                                        <select id="filterOrderStatus" multiple="multiple" class="form-control multiSelectDropDown">
                                            <option value="pending">Pending</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="manifested">Manifested</option>
                                            <option value="pickup_scheduled">Pickup Scheduled</option>
                                            <option value="picked_up">Picked Up</option>
                                            <option value="in_transit">In Transit</option>
                                            <option value="out_for_delivery">Out For Delivery</option>
                                            <option value="ndr">NDR</option>
                                            <option value="rto_initiated">RTO Initiated</option>
                                            <option value="rto_in_transit">RTO In Transit</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="rto_delivered">RTO Delivered</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark" for="filterOrderSource">Order Source</label>
                                        <select id="filterOrderSource" multiple="multiple" class="form-control multiSelectDropDown">
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark" for="filterCourierPartner">Courier Partner</label>
                                        <select id="filterCourierPartner" multiple="multiple" class="form-control multiSelectDropDown">
                                            @foreach($partnerList as $p)
                                                <option value="{{$p->keyword}}">{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark" for="filterPickupAddress">Pickup Address</label>
                                        <select id="filterPickupAddress" multiple="multiple" class="form-control multiSelectDropDown">
                                            <option value="1">Warehouse 1</option>
                                            <option value="2">Warehouse 2</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="select-wrapper-new">
                                        <label class="label text-dark" for="filterPaymentType">Payment Type</label>
                                        <select id="filterPaymentType" multiple="multiple" class="form-control multiSelectDropDown">
                                            <option value="prepaid">Prepaid</option>
                                            <option value="cod">COD</option>
                                        </select>
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
                                    <input type="text" id="filterOrderNumber" class="form-control text-dark" placeholder="Search Multiple Order Ids">
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
        <div class="modal fade" id="courier_partner_select" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Shipping Partner</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="tab-content">
                                <form action="{{route('seller.single_ship_order')}}" method="post" name="singleForm" id="singleForm">
                                    <div class="modal-body" id="partner_details_ship">
                                    </div>
                                </form>
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
    <div class="modal fade" id="allOrderDetail" tabindex="-1" role="dialog" aria-labelledby="allOrderDetail" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h3" id="exampleModalLabel"><img src="{{asset($config->favicon)}}" style="height:30px;width:30px;"> {{$config->title}}</h3>
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
                    <button type="button" class="btn btn-secondary btn-sm closeBulkShipModalButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm" style="display: none;" id="rechargeButton" data-toggle="modal" data-target="#exampleModal" data-placement="top" data-original-title="Make a Recharge">Recharge Now</button>
                    <button type="button" class="btn btn-primary btn-sm MultiShipButton">Proceed to Ship</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Warehouse Create Modal -->
    <div class="modal fade" id="createWarehouseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Warehouse</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="warehouseForm" method="post" action="{{route('ajax.create-warehouse-order')}}">
                        @csrf
                        <div class="mb-3">
                            <label for="warehouse_name" class="col-form-label">Warehouse Name</label>
                            <input type="text" class="form-control" id="warehouse_name" name="warehouse_name">
                        </div>
                        <div class="mb-3">
                            <label for="contact_person" class="col-form-label">Contact Person Name</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_contact_number" class="col-form-label">Contact Number</label>
                            <input type="number" class="form-control" id="warehouse_contact_number" name="contact_number">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_gst_number" class="col-form-label">GST Number</label>
                            <input type="text" class="form-control" id="warehouse_gst_number" name="gst_number">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_address1" class="col-form-label">Address 1</label>
                            <textarea class="form-control" id="warehouse_address1" name="address1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_address2" class="col-form-label">Address 2</label>
                            <textarea class="form-control" id="warehouse_address2" name="address2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_pincode" class="col-form-label">Pincode</label>
                            <input type="number" class="form-control" id="warehouse_pincode" name="pincode">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_city" class="col-form-label">City</label>
                            <input type="text" class="form-control" id="warehouse_city" name="city">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_state" class="col-form-label">State</label>
                            <input type="text" class="form-control" id="warehouse_state" name="state">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_country" class="col-form-label">Country</label>
                            <input type="text" class="form-control" id="warehouse_country" name="country">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_support_email" class="col-form-label">Support Email</label>
                            <input type="email" class="form-control" id="warehouse_support_email" name="support_email">
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_support_phone" class="col-form-label">Support Phone</label>
                            <input type="text" class="form-control" id="warehouse_support_phone" name="support_phone">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="createWarehouseButton" class="btn btn-primary">Create Warehouse</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    @include('seller.pages.scripts')
    <script type="text/javascript">
        let selectedTab = 'all', currentPage=1, pageSize=20, totalPage = 1, cnt = 1, ship_order_ids = [];
        $(document).ready(function (){
            loadAllWarehouse();
            @if(($_GET['type'] ?? "") != "")
                selectedTab = '{{$_GET['type']}}';
                $('.nav-buttons').removeClass('active');
                $('.{{$_GET['type']}}').addClass('active');
                currentPage = 1;
                pageSize = 20;
                loadPageData();
            @else
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
            $('.bulk_cancel_order').click(function () {
                let cancel_order_ids = [];
                $('.selectCheckBoxes').each(function(){
                    let that = $(this);
                    if(that.prop('checked') && that.data('status') !== 'pending' && that.data('status') !== 'delivered' && that.data('status') !== 'cancelled')
                        cancel_order_ids.push(that.val());
                });

                if (window.confirm("Are you sure want to Cancel selected Order?")) {
                    showOverlay();
                    $.ajax({
                        type: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'ids': cancel_order_ids
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
                            loadPageData();
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
            $('.bulk_ship_order').click(function(){
                ship_order_ids = [];
                $('.selectCheckBoxes').each(function(){
                    let that = $(this);
                    if(that.prop('checked') && that.data('status') === 'pending')
                        ship_order_ids.push(that.val());
                });
                if (window.confirm("Are you sure want to Ship selected Order?")) {
                    $('#allOrderDetail').modal('show');
                    $('#total_selected_order').html(ship_order_ids.length);
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
            $('#order-content-div').on('click', '#selectAllCheckBox', function() {
                let that = $(this);
                if (that.prop('checked')) {
                    $('#order-content-div .selectCheckBoxes').prop('checked', true);
                } else {
                    $('#order-content-div .selectCheckBoxes').prop('checked', false);
                }
                toggleBulkActionButtons();
            });

            $('#order-content-div').on('change', '.selectCheckBoxes', function() {
                toggleBulkActionButtons();
            });

            $('.MultiShipButton').click(function () {
                var that = $(this);
                that.prop('disabled',true);
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': ship_order_ids
                    },
                    url: '{{url('/')."/ship-selected-order"}}',
                    success: function (response) {
                        hideOverlay();
                        $('#allOrderDetail').modal('hide');
                        that.prop('disabled',false);
                        let info=JSON.parse(response);
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

            function toggleBulkActionButtons() {
                if ($('#order-content-div .selectCheckBoxes:checked').length > 0) {
                    $('.bulk_ship_order').show();
                    $('.bulk_delete_data').show();
                    $('.bulk_cancel_order').show();
                    $('.bulkDownloadLabel').show();
                } else {
                    $('.bulk_ship_order').hide();
                    $('.bulk_delete_data').hide();
                    $('.bulk_cancel_order').hide();
                    $('.bulkDownloadLabel').hide();
                }
            }
            $('.closeBulkShipModalButton').click(function (){
                $('#allOrderDetail').modal('hide');
            });
            $('.createNewWarehouseButton').click(function (){
                $('#createWarehouseModal').modal('show');
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
                        link.download = 'order-export.csv';
                        link.click();
                    },
                    error: function(data) {
                        console.error('Error:', data);
                    }
                });
            });

            $('.bulkDownloadLabel').click(function () {
                let isAllowed = true;
                order_ids = [];
                $('.selectCheckBoxes:visible').each(function () {
                    if ($(this).prop('checked'))
                    {
                        order_ids.push($(this).val());
                        if($(this).data('status') === 'pending' || $(this).data('status') === 'cancelled')
                            isAllowed = false;
                    }
                });
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

            $('.rows-per-page').change(function (){
                let that = $(this);
                pageSize = that.val();
                currentPage = 1;
                loadPageData();
            });

            $('#order-content-div').on('click','.shipOrderButton',function (){
                let that = $(this);
                $('#courier_partner_select #shipping_partner').val(that.data('keyword'));
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
            $('#searchWarehouseKeyword').keyup(function (){
                let search = $(this).val();
                $('.allWarehouseList .allWarehouseListData').each(function (){
                    let that = $(this);
                    let content = that.data('text');
                    if(content.toLowerCase().includes(search.toLowerCase()))
                        that.show();
                    else
                        that.hide();
                });
            });
            $('#courier_partner_select').on('click', '.ShipOrderBtn', function () {
                $(this).prop('disabled',true);
                var id = $(this).data('id');
                $('#partner_' + id).trigger('click');
                $('#shipping_partner').val($(this).data('keyword'));
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
                            loadAllOrders();
                            RefreshSellerBalance();
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
            $('#filterAWBList').keyup(function (event){
                if (event.keyCode === 13) {
                    loadPageData();
                }
            });
            $('.applyFilterButton').click(function (){
                loadPageData();
                $('#offcanvasRight').offcanvas('hide');
            });
            $('.clearFilterButton').click(function (){
                $('#filterAWBList').val('');
                $('#filterStartDate').val('');
                $('#filterEndDate').val('');
                $('#filterOrderStatus').val([]).trigger('change').multiselect('refresh');
                $('#filterOrderSource').val([]).trigger('change').multiselect('refresh');
                $('#filterCourierPartner').val([]).trigger('change').multiselect('refresh');
                $('#filterPickupAddress').val([]).trigger('change').multiselect('refresh');
                $('#filterPaymentType').val([]).trigger('change').multiselect('refresh');
                $('#filterOrderNumber').val('');
                loadPageData();
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

            // Create order flow goes from here

            $('#create-order-button').click(function () {
                $('#create-order-div').show();
                showOrderInfo();
                $('#display-order-div').hide();
            });

            $('#go_back').click(function () {
                $('#create-order-div').hide();
                $('#display-order-div').show();
                $('#order_form').trigger('reset');
                $('input').removeClass('is-invalid');
                $('textarea').removeClass('is-invalid');
                $('select').removeClass('is-invalid');

            });

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
            $('#orderTabButton').click(function () {
                $('input[name="customer_order_number"]').valid();
                $('input[name="customer_name"]').valid();
                $('input[name="contact"]').valid();
                $('textarea[name="address"]').valid();
                $('input[name="pincode"]').valid();
                $('input[name="state"]').valid();
                $('input[name="city"]').valid();
                $('input[name="country"]').valid();
                if ($('#order_form').valid()) {
                    // $('#iec_code').attr('required',false);
                    // $('#ioss').attr('required',false);
                    // $('#ad_code').attr('required',false);
                    // $('#eori').attr('required',false);
                    $('#pincode').attr('maxlength',6);
                    $('.all_tabs').slideUp();
                    $('#product_tab').slideDown();
                }
            });

            $('#ProductTabButton').click(function () {
                $(".product_requierd").valid();
                $('input[name="invoice_amount"]').valid();
                if ($('#order_form').valid() && $(".product_requierd").valid()) {
                    $('.all_tabs').slideUp();
                    $('#warehouse_tab').slideDown();
                }
            });
            $('#WarehouseTabButton').click(function () {
                $('.all_tabs').slideUp();
                $('.warehouse_card').show();
                $('#warehouse_tab').slideDown();
            });

            $('#SubmitOrderData').click(function () {
                if ($('input[name="warehouse"]:checked').length === 0) {
                    showError('Please Select a warehouse or Create a New One !!');
                    return false;
                }else{
                    $('input[name="warehouse"]').valid();
                    if ($('#order_form').valid()) {
                        $('.all_tabs').slideUp();
                    }
                    showOverlay();
                }
            });

            $('#PreviousTabButton2').click(function () {
                $('.all_tabs').slideUp();
                $('#order_tab').slideDown();
            });

            $('#PreviousTabButton').click(function () {
                $('.all_tabs').slideUp();
                $('#order_tab').slideDown();
            });

            $('#PreviousOtherTabButton').click(function () {
                $('.all_tabs').slideUp();
                $('#product_tab').slideDown();
            });
            $('#create-order-div').on('click', '.add', function () {
                add_row(cnt);
            });

            $('#create-order-div').on('click', '.remove', function () {
                var id = $(this).data('id');
                $('#total_amount' + id).val('');
                $(this).closest('tr').remove();
            });

            document.getElementById('mps_checkbox').addEventListener('change', function() {
                checkCheckBox();
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
                }
                else{
                    if (that.val().trim().length === 5)
                        that.removeClass('invalid');
                    else
                        that.addClass('invalid');
                }
            });

            $('#warehouse_pincode').blur(function () {
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
                                $('#warehouse_city').val(info.city);
                                $('#warehouse_state').val(info.state);
                                $('#warehouse_country').val(info.country);
                            } else {
                                showError('Invalid Pincode');
                                that.val('');
                            }
                        },
                        error: function (response) {
                            hideOverlay();
                        }
                    });
                }
                else{
                    if (that.val().trim().length === 5)
                        that.removeClass('invalid');
                    else
                        that.addClass('invalid');
                }
            });

            $('#order-content-div').on('click', '.modify_data', function () {
                showOverlay();
                var that = $(this);
                $.ajax({
                    url: '{{url('/')."/modify-order/"}}' + that.data('id'),
                    success: function (response) {
                        showOrderInfo();
                        $('#create-order-div').show();
                        $('#display-order-div').hide();
                        var info = JSON.parse(response);
                        $('#order_form').prop('action', '{{route('seller.update_order')}}');
                        $('#oid').val(info.order.id);
                        $('#customer_order_number').val(info.order.customer_order_number);
                        $('#order_number').val(info.order.order_number);
                        if (info.order.shipment_type != null && info.order.shipment_type.toLowerCase() == 'mps') {
                            $('#mps_checkbox').prop('checked', true);
                            checkCheckBox();
                            $('#number_of_packets').val(info.order.number_of_packets);
                        } else {
                            $('#mps_checkbox').prop('checked', false);
                            checkCheckBox();
                        }
                        $('#customer_name').val(info.order.s_customer_name);
                        // $('#country').val(info.order.b_contact_code.includes("+") ? info.order.b_contact_code : "+"+ info.order.b_contact_code );
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
                        $('#product_details').empty();
                        for (var i = 0; i < info.product.length; i++) {
                            add_row_update(i);
                        }
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
                        $('#collectable_amount').val(info.order.collectable_amount);
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

            $('#order-content-div').on('click', '.clone_data', function () {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/clone_order/"}}' + $(this).data('number'),
                    success: function (response) {
                        showOrderInfo();
                        $('#create-order-div').show();
                        $('#display-order-div').hide();
                        var info = JSON.parse(response);
                        if (info.order.shipment_type == 'mps') {
                            $('#mps_checkbox').prop('checked', true);
                            checkCheckBox();
                            $('#number_of_packets').val(info.order.number_of_packets);
                        } else {
                            // $('#shipment_type_single').prop('checked', true);
                            $('#mps_checkbox').prop('checked', false);
                            checkCheckBox();
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

                        $('#product_details').empty();
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
                        $('#reseller_name').val(info.order.reseller_name);
                        $('#collectable_amount').val(info.order.collectable_amount);
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

            $('#order-content-div').on('click', '.delete_data', function () {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/delete-order/"}}' + $(this).data('number'),
                    success: function (response) {
                        $('#order-content-div #row'+$(this).data('number')).remove();
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

            $('#order-content-div').on('click', '.cancel_order', function () {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/cancel-order/"}}' + $(this).data('number'),
                    success: function (response) {
                        loadAllOrders();
                        hideOverlay();
                        showSuccess('Order cancelled successfully');
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

            $('#order-content-div').on('click', '.reverse_data', function () {
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
                            $('#mps_checkbox').prop('checked', true);
                            checkCheckBox();
                            $('#number_of_packets').val(info.order.number_of_packets);
                            $('#number_of_packets').trigger('change');
                        } else {
                            $('#mps_checkbox').prop('checked', true);
                            checkCheckBox();
                        }

                        // $('#qc_enable_div').show();
                        $('#product_details').empty();
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
                        $('#reseller_name').val(info.order.reseller_name);
                        $('#collectable_amount').val(info.order.collectable_amount);
                        $('#reverse_ship_message').html('(Order will be Pickup from Here)');
                        $('#reverse_warehouse_message').html('(Order will be Delivered from Here)');
                        showOrderInfo();
                        $('#create-order-div').show();
                        $('#display-order-div').hide();
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

            // warehouse create validations
            $("#warehouseForm").validate({
                rules: {
                    warehouse_name: "required",
                    contact_person: "required",
                    contact_number: {
                        required: true,
                        digits: true
                    },
                    address1: "required",
                    pincode: {
                        required: true,
                        digits: true
                    },
                    city: "required",
                    state: "required",
                    country: "required",
                    support_email: {
                        required: true,
                        email: true
                    },
                    support_phone: "required"
                },
                messages: {
                    warehouse_name: "Please enter warehouse name",
                    contact_person: "Please enter contact person name",
                    contact_number: {
                        required: "Please enter contact number",
                        digits: "Please enter a valid contact number"
                    },
                    address1: "Please enter address 1",
                    pincode: {
                        required: "Please enter pincode",
                        digits: "Please enter a valid pincode"
                    },
                    city: "Please enter city",
                    state: "Please enter state",
                    country: "Please enter country",
                    support_email: {
                        required: "Please enter support email",
                        email: "Please enter a valid email address"
                    },
                    support_phone: "Please enter support phone"
                },
                submitHandler: function(form) {
                    $(form).ajaxSubmit({
                        success: function(response) {
                            // Handle the response from the server
                            if(response.status === true){
                                showSuccess(response.message);
                                loadAllWarehouse();
                            }
                            // You can add more actions here like closing the modal or showing a success message
                            $('#createWarehouseModal').modal('hide');
                        },
                        error: function(xhr) {
                            // Handle errors
                            console.error(xhr);
                            showError("Something went wrong please try again later!!!");
                            //$('#createWarehouseModal').modal('hide');
                        }
                    });
                    return false; // Prevent the form from submitting normally
                }
            });

            $("#createWarehouseButton").click(function() {
                $("#warehouseForm").submit();
            });
        });
        function loadPageData(){
            switch(selectedTab){
                case 'all':
                case 'processing':
                case 'ready_to_ship':
                case 'delivered':
                case 'lost_damaged':
                case 'cancelled':
                case 'returns':
                case 'manifest':
                case 'live_orders':
                    loadAllOrders();
                    break;
                // case 'manifest':
                //     loadAllManifest();
                //     break;
            }
        }
        function loadAllOrders(){
            $.ajax({
                type : 'post',
                data : {
                    '_token': '{{csrf_token()}}',
                    'page': currentPage,
                    'tab': selectedTab,
                    'filter': LoadFilterObject(),
                    'pageSize': pageSize
                },
                url : '{{route('seller.load-all-order')}}',
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
                url : '{{route('seller.load-all-manifest-order')}}',
                success : function (response){
                    $('#order-content-div').html(response.content);
                    setupPaginationData(response);
                }
            });
        }
        function loadAllWarehouse(){
            $.ajax({
                type : 'get',
                url : '{{route('ajax.load-all-warehouse')}}',
                success : function (response){
                    $('.allWarehouseList').html(response);
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

        function LoadFilterObject(){
            return {
                'filterAWBList': $('#filterAWBList').val(),
                'filterStartDate': $('#filterStartDate').val(),
                'filterEndDate': $('#filterEndDate').val(),
                'filterOrderStatus': $('#filterOrderStatus').val(),
                'filterOrderSource': $('#filterOrderSource').val(),
                'filterCourierPartner': $('#filterCourierPartner').val(),
                'filterPickupAddress': $('#filterPickupAddress').val(),
                'filterPaymentType': $('#filterPaymentType').val(),
                'filterOrderNumber': $('#filterOrderNumber').val()
            };
        }

        // Create order flow function
        function add_row(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name product_requierd" required placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku product_requierd" required placeholder="Product SKU"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty product_requierd" required value="1" placeholder="Product Quantity"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="ri-subtract-line"></span></button></td>';
            html += '</tr>';
            $('#product_details').append(html);
        }

        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name product_requierd" required placeholder="Product Name"/></td>'
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku product_requierd" required placeholder="Product SKU"/></td>';;
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty product_requierd" required value="1" placeholder="Product Quantity"/></td>';
            if (cnt === 0){
                html += '<td><button type="button" name="add" class="btn btn-info btn-sm add"><i class="ri-add-line"></i></button></td>';
            }
            else{
                html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="ri-subtract-line"></span></button></td>';
            }
            html += '</tr>';
            $('#product_details').append(html);
        }
        function checkCheckBox(){
            let that = document.getElementById('mps_checkbox');
            var packetsDiv = document.getElementById('number_of_packets_div');
            if (that.checked) {
                packetsDiv.style.display = 'block';
            } else {
                packetsDiv.style.display = 'none';
            }
        }
        function showOrderInfo(){
            $('.all_tabs').slideUp();
            $('#order_tab').slideDown();
        }

        $.validator.addMethod("notOnlyZero", function (value, element, param) {
            return this.optional(element) || parseInt(value) > 0;
        });
    </script>
</body>

</html>
