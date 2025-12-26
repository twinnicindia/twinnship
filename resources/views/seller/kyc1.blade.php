<?php error_reporting(0); ?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Seller KYC | {{$config->title}}</title>

    @include('seller.pages.styles')

    <style type="text/css">
        .invalid{
            border-color: crimson;
            color: crimson;
        }
        .invalid:hover{
            border-color: crimson;
            color: crimson;
        }
    </style>

</head>
<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')

    @include('seller.pages.sidebar')

    <div class="content-wrapper">
        <div class="content-inner">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">Customer KYC</h3>
                    <!-- MultiStep Form -->
                    <!-- Circles which indicates the steps of the form: -->
                    <div class="mb-4">
                        <span class="step" id="step1">Basic Information</span>
                        <span class="step" id="step2">Account Information</span>
                        <span class="step" id="step3">KYC Information</span>
                        <span class="step" id="step4">Agreement</span>
                    </div>
                        <!-- One "tab" for each step in the form: -->
                    <div class="tab" id="basic_information">
                        <form id="basic_form" method="post" enctype="multipart/form-data" action="{{route('seller.basic_information')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control" placeholder="Company Name" id="company_name" name="company_name" required value="{{Session('MySeller')->company_name}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="{{Session('MySeller')->email}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website">Website URL</label>
                                        <input type="url" class="form-control" placeholder="Website URL" id="website" name="website" value="{{$basic->website_url}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">Mobile Number</label>
                                        <div class="input-group ship-form-group">
                                            <div class="input-group-prepend">
                                                <select class="form-control" id="country" name="country" required>
                                                    <option value="+91">+91</option>
                                                    <option value="+91">+07</option>
                                                    <option value="+01">+01</option>
                                                </select>
                                            </div>
                                            <input type="number" class="form-control" placeholder="Phone Number" id="mobile" name="mobile" value="{{Session('MySeller')->mobile}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gst_number">GST Number</label>
                                        <input type="text" class="form-control" placeholder="GST Number" id="gst_number" name="gst_number" value="{{$basic->gst_number}}" maxlength="15" minlength="15">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gst_certificate">Please Upload Your GST Certificate</label>
                                        <input type="file" class="form-control" id="gst_certificate" name="gst_certificate" accept="application/pdf,image/*">

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">Choose Company Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pan_number">Enter Your PAN Number</label>
                                        <input type="text" minlength="10" maxlength="10" class="form-control" placeholder="PAN Number" id="pan_number" name="pan_number" value="{{$basic->pan_number}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="street">Enter Street Name</label>
                                        <input type="text" class="form-control" placeholder="Street" id="street" name="street" required value="{{$basic->street}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pincode">Enter Zip Code</label>
                                        <input type="text" class="form-control" placeholder="PIN Code" id="pincode" name="pincode" maxlength="6" required value="{{$basic->pincode}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City Name</label>
                                        <input type="text" class="form-control" placeholder="City" id="city" name="city" required value="{{$basic->city}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">State Name</label>
                                        <input type="text" class="form-control" placeholder="State" id="state" name="state" required value="{{$basic->state}}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab" id="account_information">
                        <form id="account_form" method="post" enctype="multipart/form-data" action="{{route('seller.account_information')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_holder_name">Account Holder Name</label>
                                        <input type="text" class="form-control" placeholder="Account Holder Name" id="account_holder_name" name="account_holder_name" value="{{$account->account_holder_name}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_number">Account Number</label>
                                        <input type="text" class="form-control" placeholder="Account Number" id="account_number" name="account_number" value="{{$account->account_number}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ifsc_code">IFSC Code</label>
                                        <input type="text" class="form-control" placeholder="IFSC Code" id="ifsc_code" name="ifsc_code" value="{{$account->ifsc_code}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control" placeholder="Bank Name" id="bank_name" name="bank_name" value="{{$account->bank_name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_branch">Branch Name</label>
                                        <input type="text" class="form-control" placeholder="Branch Name" id="bank_branch" name="bank_branch" value="{{$account->bank_branch}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cheque_image">Please Upload Cheque Image</label>
                                        <input type="file" class="form-control" id="cheque_image" name="cheque_image" accept="application/pdf,image/*" {{-- $account->cheque_image==""?"required":"" --}}>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab" id="kyc_information">
                        <form id="kyc_form" method="post" enctype="multipart/form-data" action="{{route('seller.kyc_information')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_type">Company Type</label>
                                        <select type="text" class="form-control" id="company_type" name="company_type" required>
                                            <option value="">Select Document Type</option>
                                            <option value="Proprietorship" {{$kyc->company_type=="Proprietorship"?"selected":""}}>Proprietorship</option>
                                            <option value="Private" {{$kyc->company_type=="Private"?"selected":""}}>Private</option>
                                            <option value="Partnership Firm" {{$kyc->company_type=="Partnership Firm"?"selected":""}}>Partnership Firm</option>
                                            <option value="Other" {{$kyc->company_type=="Other"?"selected":""}}>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="document_type">Document Type</label>
                                        <select type="text" class="form-control" id="document_type" name="document_type" required>
                                            <option value="">Select Document Type</option>
                                            <option value="aadhar_card" {{$kyc->document_type=="aadhar_card"?"selected":""}}>Aadhar Card</option>
                                            <option value="pan_card" {{$kyc->document_type=="pan_card"?"selected":""}}>Pan Card</option>
                                            <option value="driving_license" {{$kyc->document_type=="driving_license"?"selected":""}}>Driving License</option>
                                            <option value="voter_id" {{$kyc->document_type=="voter_id"?"selected":""}}>Voter ID Card</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="document_upload">Upload Document</label>
                                        <input type="file" class="form-control" id="document_upload" name="document_upload" accept="application/pdf,image/*" {{$kyc->document_upload==""?"required":""}}>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_name">Document Name</label>
                                        <input type="text" class="form-control" id="document_name" name="document_name" placeholder="Document Name" value="{{$kyc->document_name}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_id">Document Number</label>
                                        <input type="text" class="form-control" id="document_id" name="document_id" placeholder="Document Number" value="{{$kyc->document_id}}" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab" id="agreement">
                        <form id="agreement_form" method="post" enctype="multipart/form-data" action="{{route('seller.agreement_information')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_upload">Upload Agreement</label>
                                        <input type="file" class="form-control" id="document_upload" name="document_upload" accept="application/pdf,image/*">
                                        <a download="" href="{{asset($config->agreement)}}">Download Agreement</a>
                                    </div>
                                    <div class="form-group">
                                        <span id="terms1">
                                            <input type="checkbox" name="accept" id="accept" value="yes" required> <span class="acceptTerms">I agree and accept the terms & conditions</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_upload">Upload Guide</label>
                                        <ul>
                                            <li>Please download the agreement</li>
                                            <li>Make a signature on the agreement</li>
                                            <li>Scan the copy of agreement</li>
                                            <li>Upload the scanned agreement here</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div style="overflow:auto;">
                        <div style="float:right;">
                            <button type="button" class="btn btn-dark" id="prevBtn" onclick="nextPrev(-1)">Back</button>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextPrev(1)">Next</button>
                            <!-- <button type="button" class="btn btn-dark" id="prevBtn" onclick="nextPrev(-1)">Back</button>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextPrev(1)">Next</button> -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.pages.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('keypress','#mobile',function(e){
            if($(e.target).prop('value').length>=10){
            if(e.keyCode!=32)
            {return false}
        }})

        $(document).on('keypress','#pincode',function(e){
            if($(e.target).prop('value').length>=6){
            if(e.keyCode!=32)
            {return false}
        }})

        $('#pan_number').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
        });

        $('#account_holder_name').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9\ ]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
        });

        $('#account_number').keypress(function (e) {
        var regex = new RegExp("^[0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
        });

        $('#document_name').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z\ ]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
        });

        $('#document_id').keypress(function (e) {
        var regex = new RegExp("^[0-9A-Za-z]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
        });

        $('#pincode').blur(function () {
            var that=$(this);
            if(that.val().trim().length===6){
                that.removeClass('invalid');
                showOverlay();
                $.ajax({
                    type : 'get',
                    url : '{{url('/')}}' + '/pincode-detail/' + that.val(),
                    success : function (response) {
                        hideOverlay();
                        var info=JSON.parse(response);
                        if(info.status=="Success"){
                            $('#city').val(info.city);
                            $('#state').val(info.state);
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Invalid Pincode'
                            });
                            that.val('');
                        }
                    },
                    error : function (response) {
                        hideOverlay();
                    }
                });
            }
            else{
                that.addClass('invalid');
            }
        });
        $('#ifsc_code').blur(function () {
            var that=$(this);
            if(that.val().trim().length===11){
                that.removeClass('invalid');
                showOverlay();
                $.ajax({
                    type : 'get',
                    url : '{{url('/')}}' + '/ifsc-detail/' + that.val(),
                    success : function (response) {
                        hideOverlay();
                        var info=JSON.parse(response);
                        if(info.status=="false"){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Invalid IFSC Code'
                            });
                            //that.val('');
                        }else{
                            $('#bank_name').val(info.BANK);
                            $('#bank_branch').val(info.BRANCH);
                        }
                    },
                    error : function (response) {
                        hideOverlay();
                    }
                });
            }
            else{
                that.addClass('invalid');
            }
        });
    });
</script>
</body>
</html>
