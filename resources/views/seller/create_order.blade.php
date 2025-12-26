<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <style>
        table {
            border-spacing: 0px !important;
        }

        th {
            box-shadow: none !important;
        }

        td {
            box-shadow: none !important;
        }
    </style>
    <title>Create Order | {{$config->title}}</title>
</head>

<body>


    @include('seller.pages.header')
    @include('seller.pages.side_links')

    <div class="container-fluid">
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
                                            <a href="{{route('seller.all_order')}}?type=processing"><button type="button" class="btn btn-primary btn-sm"><i
                                                    class="fal fa-arrow-alt-left"></i> Go Back</button></a>
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
                                                        <input type="checkbox" id="mps_checkbox">
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
                                        <h6>Select Warehouse</h6>
                                        <div class="row">
                                            @foreach($warehouse as $w)
                                                <div class="col-sm-6 col-md-4 mb-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="radio" id="warehouse_{{$w->id}}" name="warehouse"
                                                                   data-id="" class="warehouse_select me-1"
                                                                   value="{{$w->id}}">
                                                            <label for="warehouse"
                                                                   class="h6 text-dark font-weight-bold">{{$w->warehouse_name}}</label><br>
                                                            <div class="h6 mb-0 text-muted">{{$w->address_line1}} , {{$w->address_line2}}</div>
                                                            <div class="h6 mb-0 text-muted">
                                                                {{$w->state}},{{$w->city}},{{$w->pincode}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
    @include('seller.pages.scripts')
    <script>
        var cnt = 1;
        $(document).ready(function (){
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
                $('input[name="warehouse"]').valid();
                if ($('#order_form').valid()) {
                    $('.all_tabs').slideUp();
                }
                showOverlay();
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
            $(document).on('click', '.add', function () {
                console.log('clic');
                ++cnt;
                add_row(cnt);
            });

            $(document).on('click', '.remove', function () {
                var id = $(this).data('id');
                $('#total_amount' + id).val('');
                $(this).closest('tr').remove();
            });

            document.getElementById('mps_checkbox').addEventListener('change', function() {
                var packetsDiv = document.getElementById('number_of_packets_div');
                if (this.checked) {
                    packetsDiv.style.display = 'block';
                } else {
                    packetsDiv.style.display = 'none';
                }
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
        });
        //axxording of order detail form


        function add_row(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku product_requierd" required placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name product_requierd" required placeholder="Product Name"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty product_requierd" required value="1" placeholder="Product Quantity"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="ri-subtract-line"></span></button></td>';
            html += '</tr>';
            $('#item_table tbody').append(html);
        }

        function add_row_update(cnt) {
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_sku' + cnt + '" name="product_sku[]" class="form-control product_sku product_requierd" required placeholder="Product SKU"/></td>';
            html += '<td><input type="text" data-id="' + cnt + '" id="product_name' + cnt + '" name="product_name[]" class="form-control product_name product_requierd" required placeholder="Product Name"/></td>';
            html += '<td><input type="number" data-id="' + cnt + '" id="product_qty' + cnt + '" name="product_qty[]" class="form-control product_qty product_requierd" required value="1" placeholder="Product Quantity"/></td>';
            html += '<td><button type="button" data-id="' + cnt + '" name="remove" class="btn btn-danger btn-sm remove"><span class="ri-subtract-line"></span></button></td>';
            html += '</tr>';
            $('#item_table tbody').append(html);
        }

        $.validator.addMethod("notOnlyZero", function (value, element, param) {
            return this.optional(element) || parseInt(value) > 0;
        });

    </script>
</body>

</html>
