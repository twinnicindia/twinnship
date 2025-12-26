<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Rate Calculator | {{$config->title}}</title>

    @include('seller.pages.styles')
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')

<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="row justify-content-center">
            <div class="content-wrapper">
                <div class="content-inner">
                    <div class="row">
                        <div class="col-md-8 content-inner">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h3 class="h4 mb-4">Rate Calculator</h3>
                                        <div class="tab" id="partner_info">
                                            <form id="courier_partner" method="post" action="{{route('seller.set_courier_partner')}}">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{$modify->id}}">
                                                <div class="row bg-gray-300">
                                                    <div class="form-group col-sm-6">
                                                        <label for="inputPassword" class="col-form-label">Shipment
                                                            Type</label>
                                                        <select name="shipment_type" required=""
                                                                class="custom-select" id="shipment_type">
                                                            <option value="forward" selected>Forward</option>
                                                            <option value="reverse">Reverse</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row bg-gray-300">
                                                    <div class="form-group col-sm-6">
                                                        <label for="inputPassword" class="col-form-label">Pickup
                                                            Pincode</label>
                                                        <input type="number" name="pickupPincode" id="pickupPincode"
                                                               class="form-control pincode"
                                                               placeholder="Enter Pickup Pincode" maxlength="6">
                                                    </div>
                                                    <div class="form-group col-sm-6">
                                                        <label for="inputPassword" class="col-form-label">Delivery
                                                            Pincode</label>
                                                        <input type="number" name="deliveryPincode"
                                                               id="deliveryPincode" class="form-control pincode"
                                                               placeholder="Enter Delivery Pincode" maxlength="6">
                                                    </div>
                                                </div>
                                                <div class="row bg-gray-300">
                                                    <div class="form-group col-sm-6">
                                                        <label for="inputPassword" class="col-form-label">Weight (in
                                                            kg)</label>
                                                        <input type="text" name="weight" id="weight"
                                                               class="form-control" placeholder="e.g 0.9 for 900 gm">
                                                    </div>
                                                    <div class="form-group col-sm-6 mt-4">
                                                        <label for="inputPassword"
                                                               class="col-form-label">Dimensions</label>
                                                        <div class="row">
                                                            <div class="col-md-2 text-center pt-2">
                                                                <label>CM</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" id="length" placeholder="L"
                                                                       class="form-control dimension">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" id="height" placeholder="H"
                                                                       class="form-control dimension">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" id="width" placeholder="W"
                                                                       class="form-control dimension">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row bg-gray-300 cod_row">
                                                    <div class="form-group col-sm-6">
                                                        <label for="cod" class="col-form-label">COD</label>
                                                        <select name="cod" class="custom-select" id="cod">
                                                            <option value="no">No</option>
                                                            <option value="yes">Yes</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-6 mt-4">
                                                        <div class="row">
                                                            <div class="col-md-12 mt-2">
                                                                <b>Volumetric Weight</b> : <span
                                                                    id="vol_weight">0.0</span> Kg
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6 invoice_value"
                                                         style="display: none;">
                                                        <label for="invoice_value" class="col-form-label">Invoice
                                                            Value</label>
                                                        <input type="number" id="invoice_value"
                                                               placeholder="Invoice Value" class="form-control">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-primary"
                                                                id="getShippingRatesButton">Get Rates</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 content-inner">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h3 class="h4 mb-4">Zone Mapping</h3>
                                        <div class="row bg-gray-300">
                                            <div class="form-group col-sm-12">
                                                <label for="pickerPincodes" class="col-form-label">Enter
                                                    Pincode</label>
                                                <input type="number" name="pickerPincodes" id="pickerPincodes"
                                                       class="form-control" placeholder="Enter Pincode" maxlength="6">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-primary" id="getZoneMapping">Get Zone Mapping</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="h4 mb-4">Download Serviceable Pincodes</h3>
                                    <form action="{{route('seller.download_serviceable_pincode')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="seller_id">Select Courier Partner</label>
                                                    <select name="courier_partner" class="form-control"
                                                            id="courier_partner">
                                                        @foreach($courier_partner as $c)
                                                            <option value="{{$c->courier_partner}}">
                                                                {{ $PartnerName[$c->courier_partner] ?? 'TwinShip' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mt-2">
                                                <br>
                                                <button type="submit" class="btn btn-primary">Download</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                    </div>
                </div>
                <bR>
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="h4 mb-4">Rate Chart</h3>
                                </div>
                                <div class="col-md-6" style="text-align: end;">
                                    <!-- <small class="text-danger">(* All the rates shown below are exclusive of
                                        GST)<br>(** Gati Rates include INR 100 docket charges and INR 100 ROV
                                        charges)</small> -->
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Courier Partner</th>
                                        <th>Freight Charge</th>
                                        <th>COD Charge</th>
                                        <th>Total Charge</th>
                                    </tr>
                                    </thead>
                                    <tbody id="rate_chart_datas">
                                    </tbody>
                                </table>
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
    var length=1,width=1,height=1,cal_weight=0,entered_weight=0,volume_weight;
    var pickupPincode=$('#pickupPincode'),deliveryPincode=$('#deliveryPincode'),inputLength=$('#length'),inputWidth=$('#width'),inputHeight=$('#height'),inputWeight=$('#weight');
    $(document).ready(function () {
        $('#cod').change(function () {
            if($(this).val()==='no'){
                $('.invoice_value').hide();
            }
            else{
                $('.invoice_value').fadeIn();
            }
        });
        $('#shipment_type').change(function () {
            if($(this).val()==='forward'){
                $('.cod_row').fadeIn();
            }
            else{
                $('.cod_row').hide();
            }
        });
        $('.dimension').keyup(function () {
            if($('#length').val().trim()!=='')
                length=parseInt($('#length').val().trim());
            if($('#height').val().trim()!=='')
                height=parseInt($('#height').val().trim());
            if($('#width').val().trim()!=='')
                width=parseInt($('#width').val().trim());
            calculate_volume();
        });
        $('#weight').blur(function () {
            var that = $(this);
            entered_weight = $(this).val() * 1000;
            if(that.val().trim() === '')
                return false;
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + entered_weight,
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#length').val(info.length);
                    $('#width').val(info.width);
                    $('#height').val(info.height);
                    $('#length').trigger('keyup');
                    calculate_volume();
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
            // console.log(entered_weight);
        });
        pickupPincode.blur(function(){
            if(pickupPincode.val().trim().length !== 6){
                pickupPincode.addClass('error').focus();
                return false;
            }
            else{
                pickupPincode.removeClass('error');
            }
            $.ajax({
                url : "{{url('/')}}/pincode-detail/" + pickupPincode.val().trim(),
                success : function(response){
                    var info=JSON.parse(response);
                    if(info.status==='Failed'){
                        pickupPincode.addClass('error').focus();
                    }
                }
            });
        });
        deliveryPincode.blur(function(){
            if(deliveryPincode.val().trim().length !== 6){
                deliveryPincode.addClass('error').focus();
                return false;
            }
            else{
                deliveryPincode.removeClass('error');
            }
            $.ajax({
                url : "{{url('/')}}/pincode-detail/" + deliveryPincode.val().trim(),
                success : function(response){
                    var info=JSON.parse(response);
                    if(info.status==='Failed'){
                        deliveryPincode.addClass('error').focus();
                    }
                }
            });
        });
        $('#getShippingRatesButton').click(function () {
            if(pickupPincode.val().trim().length !== 6){
                pickupPincode.addClass('error').focus();
                return false;
            }
            else{
                pickupPincode.removeClass('error');
            }
            if(deliveryPincode.val().trim().length !== 6){
                deliveryPincode.addClass('error').focus();
                return false;
            }
            else{
                deliveryPincode.removeClass('error');
            }
            if(deliveryPincode.val().trim() === pickupPincode.val().trim()){
                deliveryPincode.val('').focus();
                return false;
            }
            if(inputWeight.val().trim().length === 0){
                inputWeight.addClass('error').focus();
                return false;
            }
            else{
                inputWeight.removeClass('error');
            }
            if(inputLength.val().trim().length === 0){
                inputLength.addClass('error').focus();
                return false;
            }
            else{
                inputLength.removeClass('error');
            }
            if(inputHeight.val().trim().length === 0){
                inputHeight.addClass('error').focus();
                return false;
            }
            else{
                inputHeight.removeClass('error');
            }
            if(inputWidth.val().trim().length === 0){
                inputWidth.addClass('error').focus();
                return false;
            }
            else{
                inputWidth.removeClass('error');
            }
            showOverlay();
            $.ajax({
                type : 'post',
                data : {
                    'delivery_pincode' : deliveryPincode.val(),
                    'pickup_pincode' : pickupPincode.val(),
                    'shipment_type' : $('#shipment_type').val(),
                    'weight' : cal_weight,
                    'cod' : $('#cod').val(),
                    'invoice_value' : $('#invoice_value').val(),
                    '_token' : '{{csrf_token()}}'
                },
                url : '{{route('seller.get_calculated_rates')}}',
                success : function (response) {
                    // alert('Hello');
                    $('#rate_chart_datas').html(response);
                    hideOverlay();
                }
            });
        });
        $('#getZoneMapping').click(function () {
            return false;
            var pickerPincode=$('#pickerPincodes');
            if(pickerPincode.val().trim().length !== 6){
                pickerPincode.addClass('error').focus();
                return false;
            }
            else{
                pickerPincode.removeClass('error');
            }
            $.ajax({
                url : "{{url('/')}}/pincode-detail/" + pickerPincode.val().trim(),
                success : function(response){
                    var info=JSON.parse(response);
                    if(info.status==='Failed'){
                        pickerPincode.addClass('error').focus();
                    }
                    else{
                        window.location = '{{url('')."/download-mapping/"}}' + pickerPincode.val().trim();
                    }
                }
            });
        });
    });
    // function calculate_volume() {
    //     volume_weight = (length * width * height)/5;
    //     if(volume_weight > entered_weight)
    //         cal_weight = volume_weight;
    //     else
    //         cal_weight = entered_weight;
    //     $('#vol_weight').html((volume_weight/1000));
    //     // console.log(cal_weight);
    // }

    function calculate_volume() {
        volume_weight = (length * width * height)/5;
        var d_weight = parseFloat(volume_weight / 1000);
        if(volume_weight > entered_weight)
            cal_weight = volume_weight;
        else
            cal_weight = entered_weight;
        $('#vol_weight').html(d_weight.toFixed(2));
    }
    $('.pincode').on('input', function() {
        var pincode = $(this).val();

        pincode = pincode.replace(/\D/g, '');

        if (pincode.length > 6) {
            pincode = pincode.slice(0, 6);
        }

        $(this).val(pincode);

        if (pincode.length === 6) {
            $('#validationResult').text('Pincode is valid.');
        } else {
            $('#validationResult').text('Please enter a 6-digit pincode.');
        }
    });
</script>


</body>

</html>
