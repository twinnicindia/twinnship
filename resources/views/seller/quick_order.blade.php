<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Quick Order | {{$config->title}} </title>
    @include('seller.pages.styles')
    <link href="{{asset('public/assets/seller/')}}/css/progress.css" rel="stylesheet">
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }
        .font-medium{
            color: #073D59 !important;
        }
        .badge-pill:hover {
            font-size: 12px;
        }
        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{
            border-top : 2px solid #073D59 !important;
        }
    </style>
</head>

<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="form_div">
            <form id="order_form" method="post" action="{{ route('seller.ship_quick_order') }}">
                @csrf
                <input type="hidden" name="order_id" id="oid">
                <input type="hidden" name="partner" id="partner">
                <div class="card mb-2">
                    <div class="card-header">
                        <h4>Quick Order
                            <div class="float-right">
                                <button type="button" class="btn btn-primary BackButton btn-sm mx-0"><i class="fal fa-arrow-alt-left"></i> Go Back</button>
                            </div></h4>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-header">
                        <h5>Order Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="order_tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Customer Order Number</label>
                                    <input type="text" class="form-control" placeholder="Customer Order Number" id="customer_order_number" name="customer_order_number" maxlength="100" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <div class="input-group mb-3">
                                        <select class="custom-select" id="order_type" name="order_type" required>
                                            <option value="prepaid" id="type_prepaid" selected>Prepaid</option>
                                            <option value="cod" id="type_cod">Cash on Delivery</option>
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
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-header">
                        <h5>Shipping Information <span class="h6" id="reverse_ship_message"></span></h5>
                    </div>
                    <div class="card-body all_tabs" id="shipping_tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="text" class="form-control" placeholder="Customer Name" id="customer_name" name="customer_name" maxlength="100" required>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                                        <input type="text" class="form-control" maxlength="10" placeholder="Phone Number" id="contact" name="contact" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" rows="3" placeholder="Address 1" id="address" name="address" maxlength="500" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="text" class="form-control" placeholder="Pincode" id="pincode" name="pincode" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" class="form-control" placeholder="Country" id="txtCountry" name="country" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" class="form-control" placeholder="State" id="state" name="state" required>

                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" placeholder="City" id="city" name="city" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pickup From</label>
                                    <div class="input-group mb-3">
                                        <select class="custom-select" id="warehouse" name="warehouse" required>
                                            @forelse($wareHouse as $w)
                                                <option value="{{ $w->id }}">{{ $w->warehouse_name }} ({{$w->address_line1}}, {{$w->state}}, {{$w->city}}, {{$w->pincode}})</option>
                                            @empty
                                                <option value="">No Warehouse Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-header">
                        <h5>Product Information</h5>
                    </div>
                    <div class="card-body all_tabs" id="product_tab">
                        <div class="table-responsive">
                            <div id="single-packets">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="border: none;">SKU</th>
                                            <th style="border: none;">Product</th>
                                            <th style="border: none;">Quantity</th>
                                            <th style="border: none;">Field</th>
                                        </tr>
                                    </thead>
                                    <tbody id="single_shipment_product_details">
                                        <tr>
                                            <td><input type="text" data-id="1" id="product_sku1" name="product_sku[]" class="form-control product_sku" placeholder="Product SKU" required/></td>
                                            <td><input type="text" data-id="1" id="product_name1" name="product_name[]" class="form-control product_name" placeholder="Product Name" required/></td>
                                            <td><input type="text" data-id="1" id="product_qty1" name="product_qty[]" class="form-control product_qty" value="1" placeholder="Product Quantity" maxlength="4" required/></td>
                                            <td>
                                                <button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="mps-packets" style="display: none">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <!-- Dynamically create tab -->
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <!-- Dynamically create tab -->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="weight">Weight (kg)</label>
                                    <input type="text" class="form-control weightfield" placeholder="Weight (In Kg.)" id="weight" name="weight" maxlength="3" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="length">Length (cm)</label>
                                    <input type="text" class="form-control" placeholder="Length" id="length" name="length" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="breadth">Breadth (cm)</label>
                                    <input type="text" class="form-control" placeholder="Breadth" id="breadth" name="breadth" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="height">Height (cm)</label>
                                    <input type="text" class="form-control" placeholder="Height" id="height" name="height" maxlength="4" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Invoice Amount</label>
                                    <input type="text" class="form-control" placeholder="Invoice Amount" id="invoice_amount" name="invoice_amount" maxlength="7">
                                </div>
                            </div>
                            <div class="col-md-4" id="ewaybillDiv" style="display: none;">
                                <div class="form-group">
                                    <label>E-Way Bill Number</label>
                                    <input type="text" class="form-control" placeholder="E-Way Bill Number" id="ewaybill_number" name="ewaybill_number" value="" maxlength="30">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="SubmitOrderData" type="button" {{count($wareHouse) == 0 ? 'disabled' : ''}}>Quick Ship</button>
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
            <form action="{{route('seller.single_ship_order')}}" method="post" name="singleForm" id="singleForm">
                <div class="modal-body" id="partner_details_ship">
                </div>
            </form>
        </div>
    </div>
</div>

@include('seller.pages.scripts')


<script type="text/javascript">
    var totalWeight = 0,rowCounter=1;
    $('[data-toggle="popover"]').popover();

    $(document).ready(function () {

        $('#o_type_reverse').click(function(){
            $('#type_prepaid').prop("selected", true);
            $('#type_cod').prop("disabled", true);
        });

        $('#o_type_forward').click(function(){
            $('#type_cod').prop("disabled", false);
        });

        $(".shipment_type").change(function() {
            if($(this).val() == 'mps') {
                $("#number_of_packets").parent().parent().show();
            } else {
                $("#number_of_packets").parent().parent().hide();
            }
        });

        $('#courier_partner_select').on('click', '.ShipOrderBtn', function () {
            $(this).prop('disabled',true);
            $("#partner").val(partner = $(this).data('partner'));
            showOverlay();
            $("#order_form").submit();
        });


        //for pagination page number searching
        $('#nav-tabContent').on('keyup', '#txtPageCount', function (e) {
            // $('#txtPageCount').keyup(function(e){
            if(e.keyCode == 13){
                if(parseInt($(this).val().trim()) > 0){
                    if(parseInt($(this).val().trim()) <= parseInt($('.totalPage').html()) ){
                        showOverlay();
                        pageCount = parseInt($(this).val().trim());
                        fetch_orders();
                    }
                }
            }
        });

        $('#customer_name').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#txtCountry').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#state').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#city').keypress(function (e) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#contact').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#pincode').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('.table-responsive').on('keypress', '.product_qty', function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
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

        $('#length').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#breadth').keypress(function (e) {
            var regex = new RegExp("^[0-9\.]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });

        $('#height').keypress(function (e) {
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
        }).blur(function () {
            let invoiceAmount = parseInt($(this).val());
            if(!isNaN(invoiceAmount)){
                if(invoiceAmount >= 50000)
                    $('#ewaybillDiv').fadeIn();
                else
                    $('#ewaybillDiv').hide();
            }
        });

        $(document).on('keypress','#contact',function(e){
            if($(e.target).prop('value').length>=10){
                if(e.keyCode!=32)
                {return false}
            }
        });

        $(document).on('keypress','.product_qty',function(e){
            if($(e.target).prop('value').length>=10){
                if(e.keyCode!=32)
                {return false}
            }
        });

        $(document).on('keypress','#pincode',function(e){
            if($(e.target).prop('value').length>=6){
                if(e.keyCode!=32)
                {return false}
            }
        });


        //Fetch  dimension data using weight
        $('#form_div').on('blur', '.product_sku', function () {
            var that = $(this);
            if(that.val().trim()!=="") {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/fetch_product_sku/"}}' + that.val(),
                    success: function (response) {
                        hideOverlay();
                        if (response != '0') {
                            var info = JSON.parse(response);
                            $('#product_name' + that.data('id')).val(info.product_name);
                            $('#product_unitprice' + that.data('id')).val(info.product_price);
                            var weight = parseFloat(info.weight);
                            if(weight != NaN)
                                totalWeight += weight;
                            $('#weight').val(totalWeight);
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
            }
        });

        //Fetch  dimension data using weight
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
                    if(info != null){
                        $('#length').val(info.length);
                        $('#breadth').val(info.width);
                        $('#height').val(info.height);
                    }
                    else{
                        $('#length').val(10);
                        $('#breadth').val(10);
                        $('#height').val(10);
                    }
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

        //according of order detail form
        $('#orderTabButton').click(function () {
            // $('input[name="customer_order_number"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#shipping_tab').slideDown();
            }
        });

        $('#ShippingTabButton').click(function () {
            $('input[name="customer_name"]').valid();
            $('input[name="contact"]').valid();
            $('textarea[name="address"]').valid();
            $('input[name="pincode"]').valid();
            // $('input[name="state"]').valid();
            // $('input[name="city"]').valid();
            // $('input[name="country"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#product_tab').slideDown();
            }
        });

        $('#ProductTabButton').click(function () {
            // $('input[name="weight"]').valid();
            // $('input[name="length"]').valid();
            // $('input[name="height"]').valid();
            // $('input[name="breadth"]').valid();
            // $('input[name="product_name[]"]').valid();
            // $('input[name="product_sku[]"]').valid();
            // $('input[name="breadth"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('#other_tab').slideDown();
            }
        });
        $('#WarehouseTabButton').click(function () {
            $('input[name="invoice_amount"]').valid();
            $('input[name="ewaybill_number"]').valid();
            if ($('#order_form').valid()) {
                $('.all_tabs').slideUp();
                $('.warehouse_card').show();
                $('#warehouse_tab').slideDown();
            }
        });

        $('#SubmitOrderData').click(function () {
            $('input[name="customer_name"]').valid();
            $('input[name="contact"]').valid();
            $('input[name="address"]').valid();
            $('input[name="pincode"]').valid();
            $('input[name="product_name[]"]').valid();
            $('input[name="product_sku[]"]').valid();
            $('input[name="weight"]').valid();
            $('select[name="warehouse"]').valid();
            if ($('#order_form').valid()) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/get_courier_charges"}}',
                    method: "get",
                    data: { warehouse: $("#warehouse").val(), order_type: $("#order_type").val(), weight: $("#weight").val(), s_pincode: $("#pincode").val(), s_city: $("#city").val(), s_state: $("#state").val() },
                    success: function (response) {
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
                            $('#courier_partner_select').modal('show');
                            $('#partner_details_ship').html(response);
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
            }
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
            ++rowCounter;
            // if($(".shipment_type:checked").val() == "mps") {
            //     add_mps_row(rowCounter, $(this).data("target"), $(this).data("tab"));
            // } else if($(".shipment_type:checked").val() == "single") {
            //     add_row(rowCounter);
            // }
            add_row(rowCounter);
        });

        function add_row(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            $('#single_shipment_product_details').append(html);
        }

        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku" required="" placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name" required="" placeholder="Product Name"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>';
            if(cnt === 0){
                html += '<td><button type="button" name="add" class="btn btn-info btn-sm add"><i class="fa fa-plus"></i></button></td></tr>';
            }else{
                html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="fa fa-minus"></span></button></td></tr>';
            }
            $('#single_shipment_product_details').append(html);
        }

        function add_mps_row(i, target, tab) {
            var html = `
                <tr>
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku${tab}[]" class="form-control product_sku" placeholder="Product SKU"/></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name${tab}[]" class="form-control product_name" required="" placeholder="Product Name"/></td>
                    <td><input type="text" data-id="${i}" id="product_qty${i}" name="product_qty${tab}[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>
                    </td>
                    <td>
                        <button type="button" name="remove" class="btn btn-danger btn-sm remove" data-id="${i}"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $(`#${target}`).append(html);
        }

        function add_mps_row_update(i, target, tab) {
            var html = `
                <tr>
                    <td><input type="text" data-id="${i}" id="product_sku${i}" name="product_sku1[]" class="form-control product_sku" placeholder="Product SKU"/></td>
                    <td><input type="text" data-id="${i}" id="product_name${i}" name="product_name1[]" class="form-control product_name" required="" placeholder="Product Name"/></td>
                    <td><input type="text" data-id="${i}" id="product_qty${i}" name="product_qty1[]" class="form-control product_qty" required="" value="1" placeholder="Product Quantity" maxlength="4"/></td>
                    </td>
                    <td>
                        ${(i == 1 ?
                            '<button type="button" name="add" class="btn btn-info btn-sm add" data-target="mps-product-detail-1" data-tab="1"><i class="fa fa-plus"></i></button>'
                        :
                            '<button type="button" name="remove" class="btn btn-danger btn-sm remove" data-id="'+i+'"><i class="fa fa-minus"></i></button>'
                        )}
                    </td>
                </tr>
            `;
            $(`#${target}`).append(html);
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
            $('#reverse_ship_message').html('');
            $('#reverse_warehouse_message').html('');
        });

        $('.BackButton').click(function() {
            history.back();
        });

        $('#cancelButton').click(function () {
            history.back();
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

    $.validator.addMethod("notOnlyZero", function (value, element, param) {
        return this.optional(element) || parseInt(value) > 0;
    });
</script>
</body>
</html>
