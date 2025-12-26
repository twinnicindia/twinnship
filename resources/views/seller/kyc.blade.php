<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('seller.pages.styles')
    <title>Customer KYC | {{$config->title}}</title>
</head>

<body>

    @include('seller.pages.header')
    @include('seller.pages.side_links')


    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <div class="row justify-content-center">
                <div class="tablist mb-1 mt-2" id="pills-tab" role="tablist">
                    <div class="me-2" role="presentation">
                        <a class="nav-link active btn btn btn-primary step" id="basic-information-tab" data-bs-toggle="pill"
                            data-bs-target="#basic-information" type="button" role="tab"
                            aria-controls="basic-information" aria-selected="true">Basic Information</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-link btn btn btn-primary step" id="account-information-tab" data-bs-toggle="pill"
                            data-bs-target="#account-information" type="button" role="tab"
                            aria-controls="account-information" aria-selected="false">Account Information</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-link btn btn btn-primary step" id="kyc-information-tab" data-bs-toggle="pill"
                            data-bs-target="#kyc-information" type="button" role="tab" aria-controls="kyc-information"
                            aria-selected="false">KYC Information
                            Invoices</a>
                    </div>
                    <div class="me-2" role="presentation">
                        <a class="nav-link btn btn btn-primary step" id="agreement-tab" data-bs-toggle="pill"
                            data-bs-target="#agreement" type="button" role="tab" aria-controls="agreement"
                            aria-selected="false"> Agreement</a>
                    </div>
                </div>
                <div class="tab-content card-row p-3" id="pills-tabContent" style="margin-left: 22px;">
                    <div class="tab-pane fade show active " id="basic-information" role="tabpanel" aria-labelledby="basic-information-tab" tabindex="0">
                        <form id="basic_form" novalidate method="post" enctype="multipart/form-data" action="{{route('seller.basic_information')}}" class="needs-validation">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="company_name">Company Name</label>
                                        <input type="text" class="form-control" placeholder="Company Name" id="company_name" name="company_name" required value="{{Session('MySeller')->company_name}}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="email">Email Address</label>
                                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="{{Session('MySeller')->email}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="website">Website URL</label>
                                        <input type="url" class="form-control" placeholder="Website URL"
                                            id="website" name="website" value="{{$basic->website_url ?? ""}}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="country">Mobile Number</label>
                                        <div class="input-group h-58 form-control p-0">
                                            <div class="input-group-text rounded-10 px-3">
                                                <select class="input-group-text rounded-10 px-3" id="country"
                                                    name="country">
                                                    <option value="+91">+91</option>
                                                    <option value="+07">+07</option>
                                                    <option value="+01">+01</option>
                                                </select>
                                            </div>
                                            <input type="number" class="form-control h-auto border-0 text-dark" id="inlineFormInputGroupUsername" placeholder="Enter number" name="mobile" value="{{Session('MySeller')->mobile}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="gst_number">GST Number</label>
                                        <input type="text" class="form-control" placeholder="GST Number" id="gst_number" name="gst_number" value="{{$basic->gst_number ?? ""}}" maxlength="15" minlength="15">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="gst_certificate">Please Upload Your GST
                                            Certificate</label>
                                        <input type="file" class="form-control" id="gst_certificate"
                                            name="gst_certificate" accept="application/pdf">
                                        <a href="" target="_blank"><i class="fa fa-eye"></i> View Document</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="logo">Choose Company Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="pan_number">Enter Your PAN Number</label>
                                        <input type="text" class="form-control" placeholder="PAN Number" id="pan_number"
                                            name="pan_number" value="{{$basic->pan_number ?? ""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="street">Enter Street Name</label>
                                        <input type="text" class="form-control" placeholder="Street" id="street"
                                            name="street" required value="{{$basic->street ?? ""}}" >
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label class="label" for="pincode">Enter Zip Code</label>
                                        <input type="text" class="form-control" placeholder="PIN Code" id="pincode"
                                            name="pincode" maxlength="6" required value="{{$basic->pincode ?? ""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="label" for="city">City Name</label>
                                    <input type="text" class="form-control" placeholder="City" id="city"
                                        name="city" required value="{{$basic->city ?? ""}}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="label" for="state">State Name</label>
                                    <input type="text" class="form-control" placeholder="State" id="state"
                                        name="state" required value="{{$basic->state ?? ""}}">
                                </div>
                            </div>
{{--                            <div style="overflow:auto;" class="mt-3">--}}
{{--                                <div style="float:right;">--}}
{{--                                    <button type="button" class="btn btn-dark" id="saveBasicInformationButton">Save--}}
{{--                                        Information</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </form>

                    </div>
                </div>

                <div class="tab-pane fade" id="account-information" role="tabpanel"
                    aria-labelledby="account-information-tab" tabindex="0">
                    <div class="row justify-content-center">
                        <div class="col-xxl-12 mb-3">
                            <form id="account_form" method="post" enctype="multipart/form-data" action="{{route('seller.account_information')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="account_holder_name">Account Holder Name</label>
                                            <input type="text" class="form-control" placeholder="Account Holder Name"
                                                id="account_holder_name" name="account_holder_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="account_number">Account Number</label>
                                            <input type="text" class="form-control" placeholder="Account Number"
                                                id="account_number" name="account_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="ifsc_code">IFSC Code</label>
                                            <input type="text" class="form-control" placeholder="IFSC Code"
                                                id="ifsc_code" name="ifsc_code">
                                            </div>
                                        </div>
                                        <div class=" col-md-6 mb-4">
                                            <div class="form-group">
                                                <label class="label" for="bank_name">Bank Name</label>
                                                <input type="text" class="form-control" placeholder="Bank Name"
                                                    id="bank_name" name="bank_name">
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group">
                                                <label class="label" for="bank_branch">Branch Name</label>
                                                <input type="text" class="form-control" placeholder="Branch Name"
                                                    id="bank_branch" name="bank_branch">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group">
                                                <label class="label" for="cheque_image">Please Upload Cheque
                                                    Image</label>
                                                <input type="file" class="form-control" id="cheque_image"
                                                    name="cheque_image" accept="image/*">
                                                <!-- <img src="#" style="width: 100px;"> -->
                                            </div>
                                        </div>
                                    </div>
{{--                                    <div style="overflow:auto;">--}}
{{--                                        <div style="float:right;">--}}
{{--                                            <button type="button" class="btn btn-dark"--}}
{{--                                                id="saveAccountInformationButton">Save Information</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="kyc-information" role="tabpanel" aria-labelledby="kyc-information-tab"
                    tabindex="0">
                    <div class="row justify-content-center">
                        <div class="col-xxl-12 mb-3">
                            <form id="kyc_form" method="post" enctype="multipart/form-data" action="{{route('seller.kyc_information')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="label" for="company_type">Company Type</label>
                                            <select type="text" class="form-control" id="company_type"
                                                name="company_type">
                                                <option value="">Select Document Type</option>
                                                <option value="Proprietorship">Proprietorship</option>
                                                <option value="Private" selected>Private</option>
                                                <option value="Partnership Firm">Partnership Firm</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="document_type">Document Type</label>
                                            <select type="text" class="form-control" id="document_type"
                                                name="document_type">
                                                <option value="">Select Document Type</option>
                                                <option value="aadhar_card">Aadhar Card</option>
                                                <option value="pan_card" selected>Pan Card</option>
                                                <option value="driving_license">Driving License</option>
                                                <option value="voter_id">Voter ID Card</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="document_upload">Upload Document</label>
                                            <input type="file" class="form-control" id="document_upload"
                                                name="document_upload" accept="application/pdf">
                                            <a download="" href="#">View Document</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="document_name">Document Name</label>
                                            <input type="text" class="form-control" id="document_name"
                                                name="document_name" placeholder="Document Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="label" for="document_id">Document Number</label>
                                            <input type="text" class="form-control" id="document_id" name="document_id"
                                                accept="Document Number">
                                        </div>
                                    </div>
                                </div>
{{--                                <div style="overflow:auto;">--}}
{{--                                    <div style="float:right;">--}}
{{--                                        <button type="button" class="btn btn-dark" id="saveKYCInformationButton">Save--}}
{{--                                            Information</button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="agreement" role="tabpanel" aria-labelledby="agreement-tab" tabindex="0">
                    <div class="row justify-content-center">
                        <div class="col-xxl-12 mb-3">
                            <form id="agreement_form" method="post" enctype="multipart/form-data" action="{{route('seller.agreement_information')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label" for="document_upload">Upload Agreement</label>
                                            <input type="file" class="form-control" id="document_upload" name="document_upload" accept="application/pdf">
                                            <a download="" href="#">View Agreement</a>
                                        </div>
                                        <div class="form-group">
                                            <span id="terms1">
                                                <input type="checkbox" name="accept" id="accept" value="yes" required> <span class="acceptTerms">I agree and accept the terms & conditions</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <!-- Recharge Modal -->
    <!-- end model -->

    @include('seller.pages.scripts')
<script>
    $(document).ready(function(){
        $('#basic-information').removeAttr('style');
    });
</script>
</body>

</html>
