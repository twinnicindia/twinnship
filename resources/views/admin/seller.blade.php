<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller Management | {{env('appTitle')}} </title>

    @include('admin.pages.styles')

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
@include('admin.pages.header')
<!-- /.navbar -->

    <!-- Main Sidebar Container -->
@include('admin.pages.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Manage Seller</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Seller Information</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        @if(isset($sellerInfo))
            <section class="content" id="form_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">

                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Seller Information</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" src="{{$sellerInfo[0]->profile_image==""?asset('public/assets/seller/images/user-photo.svg'):asset($sellerInfo[0]->profile_image)}}" alt="Seller Image">
                                    </div>

                                    <h3 class="profile-username text-center"> <span id="first_name"></span> <span id="last_name"></span></h3>

                                    <p class="text-muted text-center" id="email"></p>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Contact Number</b> <a class="float-right" id="mobile">{{$sellerInfo[0]->mobile}}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Balance</b> <a class="float-right" id="balance">{{round($sellerInfo[0]->balance,2)}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->

                            <!-- About Me Box -->
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Comapany Information</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" src="{{$sellerInfo[0]->company_logo==""?asset('public/assets/seller/images/brand-iclone.png'):asset($sellerInfo[0]->company_logo)}}" id="company_logo" alt="Company Logo">
                                    </div>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Company Name</b> <a class="float-right" id="company_name">{{$sellerInfo[0]->company_name}}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Company Type</b> <a class="float-right" id="company_type">{{$sellerInfo[0]->company_type}}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Company Site</b> <a class="float-right" id="website_url">{{$sellerInfo[0]->website_url}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#basic_info" data-toggle="tab">Basic Information</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#account_info" data-toggle="tab">Account Information</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#kyc_info" data-toggle="tab">KYC Information</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#agreement" data-toggle="tab">Agreement</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#seller_info" data-toggle="tab">Configuration</a></li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="basic_info">
                                            <form action="{{route('administrator.seller.basic_information')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{$sellerInfo[0]->seller_id}}">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered table-stripped">
                                                            <tr>
                                                                <th>Company Name</th>
                                                                <td><input name="company_name" class="form-control" value="{{$sellerInfo[0]->company_name}}"></td>
                                                                <th>Website URL</th>
                                                                <td><input name="website_url" id="b_website_url" class="form-control" value="{{$sellerInfo[0]->website_url}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Email</th>
                                                                <td><input name="email" id="b_email" class="form-control" value="{{$sellerInfo[0]->email}}"></td>
                                                                <th>Mobile Number</th>
                                                                <td id="b_mobile"><input name="mobile_number" class="form-control" value="{{$sellerInfo[0]->mobile}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>GST Number</th>
                                                                <td><input name="gst_number" class="form-control" value="{{$sellerInfo[0]->gst_number}}"></td>
                                                                <th>PAN Number</th>
                                                                <td><input name="pan_number" class="form-control" value="{{$sellerInfo[0]->pan_number}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Zip Code</th>
                                                                <td><input name="zipcode" class="form-control" value="{{$sellerInfo[0]->pincode}}" id="pincode"></td>
                                                                <th>State</th>
                                                                <td><input name="state" id="state" class="form-control" value="{{$sellerInfo[0]->state}}"></td>

                                                            </tr>
                                                            <tr>
                                                                <th>City</th>
                                                                <td><input name="city" id="city" class="form-control" value="{{$sellerInfo[0]->city}}"></td>
                                                                <th>Street</th>
                                                                <td><input name="street" class="form-control" value="{{$sellerInfo[0]->street}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>GST Certificate</th>
                                                                <td>
                                                                    <span><a id="gst_cetificate" href='{{$sellerInfo[0]->gst_certificate != "" ? url($sellerInfo[0]->gst_certificate) : ""}}' target="_blank"> Click To See</a></span>
                                                                    <span><input type="file" name="get_certificate"/></span>
                                                                    <span class="float-right"><input class="gst_status float-right" data-id='{{$sellerInfo[0]->seller_id}}' data-size="sm" data-width="80" type="checkbox" {{$sellerInfo[0]->gst_certificate_status=="y"?"checked":""}} data-toggle="switchbutton" data-onstyle="success" data-onlabel="Approve" data-offlabel="Rejected" data-offstyle="danger"></span>
                                                                </td>
                                                                <th>Company Logo</th>
                                                                <td>
                                                                    <a id="b_company_logo" href='{{$sellerInfo[0]->company_logo != "" ? url($sellerInfo[0]->company_logo) : ""}}' target="_blank"> Click To See</a>
                                                                    <span><input type="file" name="logo"/></span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <button class="save_btn btn btn-primary float-right" type="submit">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->
                                        <div class="tab-pane" id="account_info">
                                            <form action="{{route('administrator.seller.account_information')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{$sellerInfo[0]->seller_id}}">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered table-stripped">
                                                            <tr>
                                                                <th>Account Holder Name</th>
                                                                <td><input name="ac_holder_name" id="ac_holder_name" class="form-control" value="{{$sellerInfo[0]->account_holder_name}}"></td>
                                                                <th>Account Number</th>
                                                                <td><input name="account_number" id="account_number" class="form-control" value="{{$sellerInfo[0]->account_number}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>IFSC Code</th>
                                                                <td><input name="ifsc_code" class="form-control ifsc_code" value="{{$sellerInfo[0]->ifsc_code}}"></td>
                                                                <th>Bank Name</th>
                                                                <td><input name="bank_name" id="bank_name" class="form-control" value="{{$sellerInfo[0]->bank_name}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Branch Name</th>
                                                                <td><input name="bank_branch" id="bank_branch" class="form-control" value="{{$sellerInfo[0]->bank_branch}}"></td>
                                                                <th>Cheque Image</th>
                                                                <td>
                                                                    <span><a id="cheque_image" href='{{$sellerInfo[0]->cheque_image != "" ? url($sellerInfo[0]->cheque_image) : ""}}' target="_blank"> Click To See</a></span>
                                                                    <span><input type="file" name="cheque_image"/></span>
                                                                    <span class="float-right"><input class="cheque_status float-right" data-id='{{$sellerInfo[0]->seller_id}}' data-size="sm" data-width="80" type="checkbox" {{$sellerInfo[0]->cheque_status=="y"?"checked":""}} data-toggle="switchbutton" data-onstyle="success" data-onlabel="Approve" data-offlabel="Rejected" data-offstyle="danger"></span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <button class="save_btn btn btn-primary float-right" type="submit">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->

                                        <div class="tab-pane" id="kyc_info">
                                            <form action="{{route('administrator.seller.kyc_information')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{$sellerInfo[0]->seller_id}}">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered table-stripped">
                                                            <tr>
                                                                <th>Comapny Type</th>
                                                                <td><select type="text" class="form-control" id="company_type" name="company_type" required>
                                                                        <option value="">Select Document Type</option>
                                                                        <option value="Proprietorship" {{$sellerInfo[0]->company_type=="Proprietorship"?"selected":""}}>Proprietorship</option>
                                                                        <option value="Private" {{$sellerInfo[0]->company_type=="Private"?"selected":""}}>Private</option>
                                                                        <option value="Partnership Firm" {{$sellerInfo[0]->company_type=="Partnership Firm"?"selected":""}}>Partnership Firm</option>
                                                                        <option value="Other" {{$sellerInfo[0]->company_type=="Other"?"selected":""}}>Other</option>
                                                                    </select></td>
                                                                <th>Document Type</th>
                                                                <td>
                                                                    <select type="text" class="form-control" id="document_type" name="document_type" required>
                                                                        <option value="">Select Document Type</option>
                                                                        <option value="aadhar_card" {{$sellerInfo[0]->document_type=="aadhar_card"?"selected":""}}>Aadhar Card</option>
                                                                        <option value="pan_card" {{$sellerInfo[0]->document_type=="pan_card"?"selected":""}}>Pan Card</option>
                                                                        <option value="driving_license" {{$sellerInfo[0]->document_type=="driving_license"?"selected":""}}>Driving License</option>
                                                                        <option value="voter_id" {{$sellerInfo[0]->document_type=="voter_id"?"selected":""}}>Voter ID Card</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Document Name</th>
                                                                <td><input name="document_name" id="document_name" class="form-control" value="{{$sellerInfo[0]->document_name}}"></td>
                                                                <th>Document Number</th>
                                                                <td><input name="document_number" id="document_number" class="form-control" value="{{$sellerInfo[0]->document_id}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Document File</th>
                                                                <td>
                                                                    <span><a id="document_file" href='{{$sellerInfo[0]->document_upload != "" ? url($sellerInfo[0]->document_upload) : ""}}' target="_blank"> Click To See</a></span>
                                                                    <span><input type="file" name="document_upload"/></span>
                                                                    <span class="float-right"><input class="document_status float-right" data-id='{{$sellerInfo[0]->seller_id}}' data-size="sm" data-width="80" type="checkbox" {{$sellerInfo[0]->document_status=="y"?"checked":""}} data-toggle="switchbutton" data-onstyle="success" data-onlabel="Approve" data-offlabel="Rejected" data-offstyle="danger"></span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <button class="save_btn btn btn-primary float-right" type="submit">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane" id="agreement">
                                            <div class="row">
                                                <div class="col-md-12" id="agreement_tab">
                                                    <form action="{{route('verify_kyc_information')}}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="seller_id" value="{{$sellerInfo[0]->seller_id}}">
                                                        <table class="table table-bordered table-stripped">
                                                            <tr>
                                                                <th>Agreement File</th>
                                                                <td>
                                                                    <span><a id="agreement_file" href='{{$sellerInfo[0]->agreement_document != "" ? url($sellerInfo[0]->agreement_document) : ""}}' target="_blank"> Click To See</a></span>
                                                                    <span><input type="file" name="agreement_document"/></span>
                                                                    <span class="float-right"><input class="agreement_status float-right" data-id='{{$sellerInfo[0]->seller_id}}' data-size="sm" data-width="80" type="checkbox" {{$sellerInfo[0]->agreement_status=="y"?"checked":""}} data-toggle="switchbutton" data-onstyle="success" data-onlabel="Approve" data-offlabel="Rejected" data-offstyle="danger"></span>
                                                                    <br>
                                                                    <br>
                                                                    <u><a href="{{asset($agreement_info->document_upload)}}" target="_blank"><b>View File</b></a></u>
                                                                </td>
                                                            </tr>
                                                            {{--                                                    <tr>--}}
                                                            {{--                                                        <th>Rto Charge (%)</th>--}}
                                                            {{--                                                        <td><input type="text" name="rto_charge" class="form-control" value="@if(isset($sellerInfo[0]->rto_charge)) {{$sellerInfo[0]->rto_charge}} @else {{$config->rto_charge}} @endif"></td>--}}
                                                            {{--                                                    </tr>--}}
                                                            {{--                                                    <tr>--}}
                                                            {{--                                                        <th>Reversal Charges (%)</th>--}}
                                                            {{--                                                        <td><input type="text" name="reverse_charge" class="form-control" value="@if(isset($sellerInfo[0]->reverse_charge)) {{$sellerInfo[0]->reverse_charge}} @else {{$config->reverse_charge}} @endif"></td>--}}
                                                            {{--                                                    </tr>--}}
                                                            {{--                                                    <tr>--}}
                                                            {{--                                                        <th>Reconciliation Days</th>--}}
                                                            {{--                                                        <td><input type="text" name="reconciliation_days" class="form-control" value="@if(isset($sellerInfo[0]->reconciliation_days)) {{$sellerInfo[0]->reconciliation_days}} @else {{$config->reconciliation_days}} @endif"></td>--}}
                                                            {{--                                                    </tr>--}}
                                                            {{--                                                    <tr>--}}
                                                            {{--                                                        <th>Remmitance Days</th>--}}
                                                            {{--                                                        <td><input type="text" name="remmitance_days" class="form-control" value="@if(isset($sellerInfo[0]->remmitance_days)) {{$sellerInfo[0]->remmitance_days}} @else {{7}} @endif"></td>--}}
                                                            {{--                                                    </tr>--}}
                                                        </table>
                                                        <!-- <button class="verify_document btn btn-success" id="verify_btn">Verify Document</button> -->
                                                        <button class="verify_document btn btn-primary" type="submit" id="verify_btn">Save</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="seller_info">
                                            <form id="sellerConfiguration" action="{{route('administrator.seller.seller_information')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$sellerInfo[0]->seller_id}}">
                                                <div class="row">
                                                    <div class="col-md-12">
{{--                                                        <h3>Admin</h3>--}}
{{--                                                        <hr>--}}
{{--                                                        <table class="table table-borderless">--}}
{{--                                                            <tr>--}}
{{--                                                                <th>Warehouse Status</th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select class="form-control" id="warehouse_status" name="warehouse_status">--}}
{{--                                                                        <option value="y" {{$sellerInfo[0]->warehouse_status == "y" ?"selected":""}}>Allowed</option>--}}
{{--                                                                        <option value="n" {{$sellerInfo[0]->warehouse_status == "n" ?"selected":""}}>Denied</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                                <th>Status </th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select class="form-control" id="status" name="status">--}}
{{--                                                                        <option value="y" {{$sellerInfo[0]->status == "y" ?"selected":""}}>Allowed</option>--}}
{{--                                                                        <option value="n" {{$sellerInfo[0]->status == "n" ?"selected":""}}>Denied</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                            </tr>--}}
{{--                                                            <tr>--}}
{{--                                                                <th>Webhook Enabled</th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select class="form-control" id="webhook_enabled" name="webhook_enabled">--}}
{{--                                                                        <option value="y" {{$sellerInfo[0]->webhook_enabled == "y" ?"selected":""}}>Allowed</option>--}}
{{--                                                                        <option value="n" {{$sellerInfo[0]->webhook_enabled == "n" ?"selected":""}}>Denied</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                                <th>Cheapest</th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select type="number" name="cheapest_enabled" id="cheapest_enabled" class="form-control">--}}
{{--                                                                        <option value="y" {{$sellerInfo[0]->cheapest_enabled == 'y' ? 'selected' : ''}}>Enable</option>--}}
{{--                                                                        <option value="n" {{$sellerInfo[0]->cheapest_enabled == 'n' ? 'selected' : ''}}>Disable</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                            </tr>--}}
{{--                                                            <tr>--}}

{{--                                                                <th>Onboarded By</th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select class="form-control" id="onboarded_by" name="onboarded_by">--}}
{{--                                                                        @foreach($employee as $e)--}}
{{--                                                                            <option value="{{$e->id}}" {{$sellerInfo[0]->onboarded_by == $e->id ?"selected":""}}>{{$e->name}}</option>--}}
{{--                                                                        @endforeach--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                                <th>Zone Type</th>--}}
{{--                                                                <td>--}}
{{--                                                                    <select class="form-control" id="zone_type" name="zone_type">--}}
{{--                                                                        <option value="sl" {{$sellerInfo[0]->zone_type == "sl" ?"selected":""}}>Sl</option>--}}
{{--                                                                        <option value="default" {{$sellerInfo[0]->zone_type == "default" ?"selected":""}}>Default</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </td>--}}
{{--                                                            </tr>--}}
{{--                                                        </table>--}}
                                                        <h3>Feature</h3>
                                                        <hr>
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <th>Sms Service </th>
                                                                <td>
                                                                    <select class="form-control" id="sms_service" name="sms_service">
                                                                        <option value="y" {{$sellerInfo[0]->sms_service == "y" ?"selected":""}}>Allowed</option>
                                                                        <option value="n" {{$sellerInfo[0]->sms_service == "n" ?"selected":""}}>Denied</option>
                                                                    </select>
                                                                </td>
                                                                <th>Employee Flag Enabled</th>
                                                                <td>
                                                                    <select class="form-control" id="employee_flag_enabled" name="employee_flag_enabled">
                                                                        <option value="y" {{$sellerInfo[0]->employee_flag_enabled == "y" ?"selected":""}}>Allowed</option>
                                                                        <option value="n" {{$sellerInfo[0]->employee_flag_enabled == "n" ?"selected":""}}>Denied</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Selected Plan</th>
                                                                <td>
                                                                    <select class="form-control" id="plan" name="plan">
                                                                        @foreach($plan as $p)
                                                                        <option value="{{$p->id}}" {{$p->id == $sellerInfo[0]->plan_id ?"selected":""}}>
                                                                            {{$p->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <h3>Finance</h3>
                                                        <hr>
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <th>Rto Charge (%)</th>
                                                                <td><input type="number" name="rto_charge" id="rto_charge" class="form-control" value="{{$sellerInfo[0]->rto_charge ?? 100}}"></td>
                                                                <th>Reconciliation Days</th>
                                                                <td>
                                                                    <select name="reconciliation_days" id="reconciliation_days" class="form-control">
                                                                        <option value="1" {{$sellerInfo[0]->reconciliation_days == 1 ? "selected" : ""}}>1</option>
                                                                        <option value="2" {{$sellerInfo[0]->reconciliation_days == 2 ? "selected" : ""}}>2</option>
                                                                        <option value="3" {{$sellerInfo[0]->reconciliation_days == 3 ? "selected" : ""}}>3</option>
                                                                        <option value="4" {{$sellerInfo[0]->reconciliation_days == 4 ? "selected" : ""}}>4</option>
                                                                        <option value="5" {{$sellerInfo[0]->reconciliation_days == 5 ? "selected" : ""}}>5</option>
                                                                        <option value="6" {{$sellerInfo[0]->reconciliation_days == 6 ? "selected" : ""}}>6</option>
                                                                        <option value="7" {{$sellerInfo[0]->reconciliation_days == 7 ? "selected" : ""}}>7</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Reversal Charges (%)</th>
                                                                <td><input name="reverse_charge" id="reverse_charge" class="form-control" value="{{$sellerInfo[0]->reverse_charge ?? 150}}"></td>
                                                                <th>Early Cod Charge</th>
                                                                <td><input type="number" name="early_cod_charge" id="early_cod_charge" class="form-control" value="{{$sellerInfo[0]->early_cod_charge}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Remittance Frequency</th>
                                                                <td>
                                                                    <select name="remittance_frequency" id="remmitnace_frequency" class="form-control">
                                                                        <option value="1" {{$sellerInfo[0]->remittance_frequency == 1 ? 'selected' : ''}}>Weekly Once</option>
                                                                        <option value="2" {{$sellerInfo[0]->remittance_frequency == 2? 'selected' : ''}}>Weekly Twice</option>
                                                                        <option value="3" {{$sellerInfo[0]->remittance_frequency == 3 ? 'selected' : ''}}>Weekly Thrice</option>
                                                                        <option value="5" {{$sellerInfo[0]->remittance_frequency == 5 ? 'selected' : ''}}>Daily</option>
                                                                    </select>
                                                                </td>
                                                                <th>Invoice Date</th>
                                                                <td><input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{$sellerInfo[0]->invoice_date}}"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Remittance Days</th>
                                                                <td>
                                                                    <select name="remmitance_days" id="remmitance_days" class="form-control">
                                                                        <option value="1" {{$sellerInfo[0]->remmitance_days == 1 ? "selected" : ""}}>1</option>
                                                                        <option value="2" {{$sellerInfo[0]->remmitance_days == 2 ? "selected" : ""}}>2</option>
                                                                        <option value="3" {{$sellerInfo[0]->remmitance_days == 3 ? "selected" : ""}}>3</option>
                                                                        <option value="4" {{$sellerInfo[0]->remmitance_days == 4 ? "selected" : ""}}>4</option>
                                                                        <option value="5" {{$sellerInfo[0]->remmitance_days == 5 ? "selected" : ""}}>5</option>
                                                                        <option value="6" {{$sellerInfo[0]->remmitance_days == 6 ? "selected" : ""}}>6</option>
                                                                        <option value="7" {{$sellerInfo[0]->remmitance_days == 7 ? "selected" : ""}}>7</option>
                                                                    </select>
                                                                </td>
                                                                <th>Remittance Week Day</th>
                                                                <td>
                                                                    <input type="checkbox" value="Monday" {{ stripos($sellerInfo[0]->remittanceWeekDay, "Monday") !== false ? "checked" : "" }} name="remittanceWeekDay[]" class="remmitanceWeekDay mt-1"><small class="text-info mb-2">&nbsp;M</small>&nbsp;&nbsp;
                                                                    <input type="checkbox" value="Tuesday" {{ stripos($sellerInfo[0]->remittanceWeekDay, "Tuesday") !== false ? "checked" : "" }} name="remittanceWeekDay[]" class="remmitanceWeekDay mt-1"><small class="text-info mb-2">&nbsp;T</small>&nbsp;&nbsp;
                                                                    <input type="checkbox" value="Wednesday" {{ stripos($sellerInfo[0]->remittanceWeekDay, "Wednesday") !== false ? "checked" : "" }} name="remittanceWeekDay[]" class="remmitanceWeekDay mt-1"><small class="text-info mb-2">&nbsp;W</small>&nbsp;&nbsp;
                                                                    <input type="checkbox" value="Thursday" {{ stripos($sellerInfo[0]->remittanceWeekDay, "Thursday") !== false ? "checked" : "" }} name="remittanceWeekDay[]" class="remmitanceWeekDay mt-1"><small class="text-info mb-2">&nbsp;T</small>&nbsp;&nbsp;
                                                                    <input type="checkbox" value="Friday" {{ stripos($sellerInfo[0]->remittanceWeekDay, "Friday") !== false ? "checked" : "" }} name="remittanceWeekDay[]" class="remmitanceWeekDay mt-1"><small class="text-info mb-2">&nbsp;F</small>
                                                                </td>
                                                            </tr>
                                                            <!-- <tr class="whatsapp-charge">
                                                                <th>WhatsApp Charges</th>
                                                                <td>
                                                                    <input type="text" value="{{$sellerInfo[0]->whatsapp_charges}}" class="form-control" name="whatsapp_charges" id="whatsapp-charge">
                                                                </td>
                                                            </tr> -->
                                                        </table>
{{--                                                        <h3>Technology</h3>--}}
{{--                                                        <hr>--}}
{{--                                                        <table class="table table-borderless">--}}
{{--                                                            <tr>--}}
{{--                                                                <th>API Key</th>--}}
{{--                                                                <td><input type="text" name="api_key" id="api_key" class="form-control" value="{{$sellerInfo[0]->api_key}}"></td>--}}
{{--                                                                <th>EasyEcom Token</th>--}}
{{--                                                                <td><input type="text" name="easyecom_token" id="easyecom_token" class="form-control" value="{{$sellerInfo[0]->easyecom_token}}"></td>--}}
{{--                                                            </tr>--}}
{{--                                                            <tr>--}}
{{--                                                                <th>Webhook Url</th>--}}
{{--                                                                <td><input type="text" name="webhook_url" id="webhook_url" class="form-control" value="{{$sellerInfo[0]->webhook_url}}"></td>--}}
{{--                                                                <th>Google Id</th>--}}
{{--                                                                <td><input type="text" name="google_id" id="google_id" class="form-control" value="{{$sellerInfo[0]->google_id}}"></td>--}}
{{--                                                            </tr>--}}
{{--                                                        </table>--}}
                                                        <a href="{{url('administrator/block-courier')}}" target="_blank" class="ml-1 btn btn-success float-right" type="submit">Courier Blocking</a>
                                                        <button class="save_btn btn btn-success float-right" type="submit">Save & Verify</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
        @endif
    <!-- /.content -->
        @if(isset($sellerInfo))
            <section class="content" id="data_div" style="display:none;">
                @else
                    <section class="content" id="data_div" style="display:block;">
                        @endif
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <p class="card-title h3">Seller Information
                                            <div class="float-right">
                                                <a href="{{route('export_seller')}}">
                                                    <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                                                </a>
                                            </div>
                                            </p>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <form action="{{route('kyc_approve')}}" method="get">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <input type="text" name="q" class="form-control" value="{{ request()->q ?? '' }}" placeholder="Search..">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <input type="submit" class="btn btn-primary" value="Search">
                                                                    <a class="btn btn-primary ml-1" href="{{route('kyc_approve')}}">Reset</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- <button type="button" id="addDataButton" class="btn btn-warning"><i class="fa fa-plus"></i> Add Why Choose</button><br><br> -->
                                            @if($seller instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="seller-data">
                                                        <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th>Seller Name</th>
                                                            <th>Company Name</th>
                                                            <th>Seller Details</th>
                                                            <th>KYC Status</th>
                                                            <th>Status</th>
                                                            <th style="width:90px;">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php($cnt=$seller->firstItem())
                                                        @forelse($seller as $key => $s)
                                                            <tr id="row{{$s->id}}">
                                                                <td>{{$cnt++}}</td>
                                                                <td>{{$s->first_name.' '.$s->last_name."($s->code)"}}</td>
                                                                <td>{{$s->company_name}}</td>
                                                                <td>
                                                                    Email :- {{$s->email}}<br>
                                                                    Contact :- {{$s->mobile}}
                                                                </td>
                                                                <td id="verified-{{ $s->id }}">
                                                                    {!! ($s->verified == 'y' ? '<a href="#" class="badge bg-success verified" data-id="'.$s->id.'" data-status="'.$s->verified.'">Verified</a>' : '<a href="#" class="badge bg-danger verified" data-id="'.$s->id.'" data-status="'.$s->verified.'">Not Verified</a>') !!}
                                                                </td>
                                                                <td id="active_status-{{ $s->id }}">
                                                                    {!! ($s->status == 'y' ? '<a href="#" class="badge bg-success active_status" data-id="'.$s->id.'" data-status="'.$s->status.'">Active</a>' : '<a href="#" class="badge bg-danger active_status" data-id="'.$s->id.'" data-status="'.$s->status.'">Blocked</a>') !!} <br>
                                                                    @if($s->is_migrated)
                                                                        <a href="#" class="badge bg-success">Migrated</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <a href='{{url("/administrator/view-seller/$s->id")}}' title="View Information"><i class="fa fa-eye"></i></a>&nbsp;
                                                                    @if($s->verified == 'y')
                                                                        <a data-id="{{$s->id}}" title="Add Balance" class="add_balance"><i class="fa fa-plus"></i></a>&nbsp;
                                                                        <a data-id="{{$s->id}}" title="Deduct Balance" class="deduct_balance"><i class="fa fa-minus"></i></a>&nbsp;
                                                                    @endif
                                                                    <a href="javascript:;" title="Remove Information" data-id="{{$s->id}}" class="remove_data"><i class="fa fa-trash"></i></a>
                                                                    @if($s->verified == 'y')
                                                                        <a href="{{route('seller.generate-invoice', $s->id)}}" title="Generate Invoice" onclick="return confirm('Are you sure to generate invoice?')"><i class="fa fa-calendar"></i></a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    {{ $seller->links() }}
                                                </div>
                                            @endif
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
    </div>


    <div class="modal fade" id="AddBalanceModal" tabindex="-1" role="dialog" aria-labelledby="AddBalanceModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Balance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('administrator.add_seller_balance')}}" id="addsellerBalance" method="post">
                    @csrf
                    <input type="hidden" name="seller_id" id="add_seller_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" placeholder="Amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Description" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm cloneSubmitButton" onclick="showOverlay();">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeductBalanceModal" tabindex="-1" role="dialog" aria-labelledby="DeductBalanceModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Deduct Balance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('administrator.deduct_seller_balance')}}" id="deductsellerBalance" method="post">
                    @csrf
                    <input type="hidden" name="seller_id" id="deduct_seller_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" placeholder="Amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Description" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm cloneSubmitButton" onclick="showOverlay();">Deduct</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /.content-wrapper -->
@include('admin.pages.footer')

<!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


@include('admin.pages.scripts')

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function () {

        $('#whatsapp_service').change(function(){
            if(parseInt($(this).val()) === 1)
                $('.whatsapp-charge').show();
            else
                $('.whatsapp-charge').hide();
        });

        $('#whatsapp-charge').keypress(function(event) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $('#sellerConfiguration').on("submit", function(event){
            if(parseInt($('#remmitnace_frequency').val()) === parseInt($('.remmitanceWeekDay:checked').length)){
                console.log("Form will be submitted.");
            } else {
                event.preventDefault();
                alert('Please select valid week day');
            }
        });

        $('#remmitnace_frequency').change(function(){
            var that = $(this);
            if(parseInt(that.val()) != 7 || parseInt(that.val() != 1)) {
                $('#remmitance_day').attr('multiple', true);
            }
            else {
                $('#remmitance_day').attr('multiple', false);
            }
        });

        $('.remmitanceWeekDay').change(function(){
            var that = $(this);
            var checkedCheckboxes = $(".remmitanceWeekDay:checked").length;
            if(parseInt($('#remmitnace_frequency').val()) === 1 && checkedCheckboxes > 1){
                alert("You can select 1 day");
                that.prop("checked",false);
                return false;
            }
            if(parseInt($('#remmitnace_frequency').val()) === 2 && checkedCheckboxes > 2){
                alert("You can select 2 days");
                that.prop("checked",false);
                return false;
            }
            if(parseInt($('#remmitnace_frequency').val()) === 3 && checkedCheckboxes > 3){
                alert("You can select 3 days");
                that.prop("checked",false);
                return false;
            }
            if(parseInt($('#remmitnace_frequency').val()) === 5 && checkedCheckboxes > 5){
                alert("You can select 5 days");
                that.prop("checked",false);
                return false;
            }
        });

        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add_why')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
        });

        // $('.add_balance').click(function(){
        $('#data_div').on('click', '.add_balance', function () {
            $('#add_seller_id').val($(this).data('id'));
            $('#AddBalanceModal').modal('show');
        });

        $('#data_div').on('click', '.deduct_balance', function () {
            $('#deduct_seller_id').val($(this).data('id'));
            $('#DeductBalanceModal').modal('show');
        });

        $('#seller-data').on('click','.view_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/administrator/view_kyc_information/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    if(info[0] != undefined){
                        $('#first_name').html(info[0].first_name);
                        $('#last_name').html(info[0].last_name);
                        $('#email').html(info[0].email);
                        $('#mobile').html(info[0].mobile);
                        $('#balance').html(info[0].balance);
                        $('#company_name').html(info[0].company_name);
                        $('#company_type').html(info[0].company_type);
                        $('#website_url').html(info[0].website_url);
                        $('#company_logo').prop('src','{{url('/')}}/' + info[0].company_logo);

                        //Basic Information
                        $('#b_company_name').html(info[0].company_name);
                        $('#b_website_url').html(info[0].website_url);
                        $('#b_email').html(info[0].email);
                        $('#b_mobile').html(info[0].mobile);
                        $('#gst_number').html(info[0].gst_number);
                        $('#pan_number').html(info[0].pan_number);
                        $('#state').html(info[0].state);
                        $('#city').html(info[0].city);
                        $('#street').html(info[0].street);
                        $('#zipcode').html(info[0].pincode);
                        $('#gst_status').data('id', info[0].id);
                        $('#gst_cetificate').prop('href','{{url('/')}}/' + info[0].gst_certificate);

                        $('#b_company_logo').prop('href','{{url('/')}}/' + info[0].company_logo);

                        //Account Information
                        $('#ac_holder_name').html(info[0].account_holder_name);
                        $('#ac_number').html(info[0].account_number);
                        $('#bank_name').html(info[0].bank_name);
                        $('#branch_name').html(info[0].bank_branch);
                        $('#ifsc_code').html(info[0].ifsc_code);
                        $('#cheque_image').prop('href','{{url('/')}}/' + info[0].cheque_image);

                        //KYC Information
                        $('#k_company_type').html(info[0].company_type);
                        $('#document_type').html(info[0].document_type);
                        $('#document_name').html(info[0].document_name);
                        $('#document_number').html(info[0].document_id);
                        $('#document_file').prop('href','{{url('/')}}/' + info[0].document_upload);

                        //Agreement Information
                        $('#agreement_file').prop('href','{{url('/')}}/' + info[0].agreement_document);
                        $('#verify_btn').val(info[0].id);
                        showForm();

                    }
                    else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Seller has not Fiil any KYC Information yet ..Please Check after some Time!',
                        })
                    }


                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $("#seller-data").on("click", ".active_status", function() {
            var id = $(this).data("id");
            var status = $(this).data("status") == "y" ? "n" : "y";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller_status') }}",
                method: "post",
                data: {id: id, status: status, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#active_status-"+id).html('<a href="#" class="badge bg-'+(status == 'y' ? 'success' : 'danger')+' active_status" data-id="'+id+'" data-status="'+status+'">'+(status == 'y' ? 'Active' : 'Blocked')+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Status not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $("#seller-data").on("click", ".seller_order_type", function() {
            var id = $(this).data("id");
            var type = $(this).data("type") == "SE" ? "NSE" : "SE";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller_order_type') }}",
                method: "post",
                data: {id: id, type: type, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#seller_order_type-"+id).html('<a href="#" class="badge bg-'+(type == 'SE' ? 'info' : 'warning')+' seller_order_type" data-id="'+id+'" data-type="'+type+'">'+(type)+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Seller Order Type not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $("#seller-data").on("click", ".is_alpha", function() {
            var id = $(this).data("id");
            var type = $(this).data("type") == "SE" ? "NSE" : "SE";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller_is_alpha') }}",
                method: "post",
                data: {id: id, type: type, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#is_alpha-"+id).html('<a href="#" class="badge bg-'+(type == 'SE' ? 'info' : 'warning')+' is_alpha" data-id="'+id+'" data-type="'+type+'">'+(type)+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Seller Is Alpha not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $("#seller-data").on("change", ".zone_type", function() {
            var id = $(this).data("id");
            var type = $(this).val();
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.zone_type') }}",
                method: "post",
                data: {id: id, type: type, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        showSuccess('Changed','Zone Type Changed Successfully');
                    } else {
                        showError('Error','Zone Type not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $("#seller-data").on("click", ".sms_status", function() {
            var id = $(this).data("id");
            var status = $(this).data("status") == "y" ? "n" : "y";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller.sms_status') }}",
                method: "post",
                data: {id: id, status: status, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#status-"+id).html('<a href="#" class="badge bg-'+(status == 'y' ? 'success' : 'danger')+' sms_status" data-id="'+id+'" data-status="'+status+'">'+(status == 'y' ? 'Active' : 'Deactive')+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Status not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $("#seller-data").on("click", ".pincode_editable", function() {
            var id = $(this).data("id");
            var status = $(this).data("status") == "y" ? "n" : "y";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller.pincode_editable') }}",
                method: "post",
                data: {id: id, status: status, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#pincode_status-"+id).html('<a href="#" class="badge bg-'+(status == 'y' ? 'success' : 'danger')+' pincode_editable" data-id="'+id+'" data-status="'+status+'">'+(status == 'y' ? 'Yes' : 'No')+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Status not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });

        $('#seller-data').on('change','.change_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'id' : that.data('id'),
                    'status' : that.prop('checked')?'y':'n',
                },
                url : '{{url('/')."/administrator/seller_status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed successfully');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#form_div').on('change','.gst_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'id' : that.data('id'),
                    'status' : that.prop('checked')?'y':'r',
                },
                url : '{{url('/')."/administrator/seller_gst_status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#form_div').on('change','.cheque_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'id' : that.data('id'),
                    'status' : that.prop('checked')?'y':'r',
                },
                url : '{{url('/')."/administrator/seller_cheque_status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#form_div').on('change','.document_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'id' : that.data('id'),
                    'status' : that.prop('checked')?'y':'r',
                },
                url : '{{url('/')."/administrator/seller_document_status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#form_div').on('change','.agreement_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'id' : that.data('id'),
                    'status' : that.prop('checked')?'y':'r',
                },
                url : '{{url('/')."/administrator/seller_agreement_status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        //for delete seller
        $('#seller-data').on('click','.remove_data',function(){
            var that=$(this);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this imaginary file!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/administrator/delete-seller"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            Swal.fire(
                                'Deleted!',
                                'Information has been deleted.',
                                'success'
                            );
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            })
        });

        $('#pincode').blur(function () {
            var that=$(this);
            if(that.val().trim().length===6){
                that.removeClass('invalid');
                showOverlay();
                $.ajax({
                    type : 'get',
                    url : '{{url('/')}}' + '/administrator/pincode-detail/' + that.val(),
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
        $('#account_info').on('blur','.ifsc_code',function(){
            var that=$(this);
            if(that.val().trim().length===11){
                that.removeClass('invalid');
                showOverlay();
                $.ajax({
                    type : 'get',
                    url : '{{url('/')}}' + '/administrator/ifsc-detail/' + that.val(),
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

        $.validator.setDefaults({
            submitHandler: function () {
                return true;
            }
        });
        $("#seller-data").on("click", ".verified", function() {
            var id = $(this).data("id");
            var status = $(this).data("status") == "y" ? "n" : "y";
            //Show preloader
            showOverlay();
            $.ajax({
                url: "{{ route('administrator.seller.verified') }}",
                method: "post",
                data: {id: id, status: status, _token: "{{ csrf_token() }}"},
                success: function(res) {
                    //Hide preloader
                    if(res.status == true) {
                        $("#verified-"+id).html('<a href="#" class="badge bg-'+(status == 'y' ? 'success' : 'danger')+' verified" data-id="'+id+'" data-status="'+status+'">'+(status == 'y' ? 'verified' : 'Not verified')+'</a>');
                        showSuccess('Changed','Status Changed Successfully');
                    } else {
                        showError('Error','Status not Changed');
                    }
                    hideOverlay();
                },
                error: function(error) {
                    //Hide preloader
                    hideOverlay();
                    showError('Error','Something went Wrong');
                }
            });
        });
    });

    function showForm() {
        $('#data_div').hide();
        $('#form_div').slideDown();
    }
    function showData() {
        $('#form_div').hide();
        $('#data_div').slideDown();
    }
</script>
</body>

</html>
