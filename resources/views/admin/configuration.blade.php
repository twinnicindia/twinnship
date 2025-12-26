<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Configuration | {{env('appTitle')}} </title>

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
                        <h1>Manage Configuration</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Configuration</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Manage Admin</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('save_configuration')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">Project Title</label>
                                                <input type="text" name="title" class="form-control" id="title" placeholder="Title of Project" value="{{$config->title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Contact Address</label>
                                                <textarea type="text" name="address" class="form-control" id="address" placeholder="Contact Address" rows="5">{{$config->address}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" value="{{$config->email}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mobile">Contact Number</label>
                                                <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Contact Number" value="{{$config->mobile}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="copyright">Copyright Text</label>
                                                <input type="text" name="copyright" class="form-control" id="copyright" placeholder="Copyright Text" value="{{$config->copyright}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="copyright">Working Hours</label>
                                                <input type="text" name="working_hour" class="form-control" id="working_hour" placeholder="Working Hours" value="{{$config->working_hour}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="meta_keyword">Meta Keywords(coma separated)</label>
                                                <textarea name="meta_keyword" class="form-control" id="meta_keyword" placeholder="keyword1,keyword2" rows="5">{{$config->meta_keyword}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="meta_description">Meta Description</label>
                                                <textarea name="meta_description" class="form-control" id="meta_description" placeholder="Meta Description" rows="5">{{$config->meta_description}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="logo">Choose Logo</label>
                                                <input type="file" name="logo" class="form-control" id="logo">
                                                <img src="{{asset($config->logo)}}" style="width: 200px;" alt="{{$config->title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="favicon">Fav Icon</label>
                                                <input type="file" name="favicon" class="form-control" id="favicon">
                                                <img src="{{asset($config->favicon)}}" style="height: 50px;width: 50px;" alt="{{$config->title}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="account_details">NEFT Account Details</label>
                                                <textarea name="account_details" class="form-control" id="account_details" placeholder="Google Analytic Code" rows="5">{{$config->account_details}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="analytics_code">Google Analytic Code</label>
                                                <textarea name="analytics_code" class="form-control" id="analytics_code" placeholder="Google Analytic Code" rows="5">{{$config->analytics_code}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="minimum_balance">Minimum Balance to Continue</label>
                                                <input type="text" name="minimum_balance" class="form-control" id="minimum_balance" placeholder="Minimum Balance to Continue" value="{{$config->minimum_balance}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="login_message">Login Page Message</label>
                                                <textarea name="login_message" class="form-control" id="login_message" placeholder="Login Page Message" rows="3">{{$config->login_message}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="register_message">Register Page Message</label>
                                                <textarea name="register_message" class="form-control" id="register_message" placeholder="Register Page Message" rows="3">{{$config->register_message}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="forget_message">Forget Page Message</label>
                                                <textarea name="forget_message" class="form-control" id="forget_message" placeholder="Forget Page Message" rows="3">{{$config->forget_message}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h3>Front End Management</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="logistic_partner">Logistics Partner</label>
                                                <select name="logistic_partner" class="form-control" id="logistic_partner">
                                                    <option value="y" {{$config->logistic_partner=='y'?"selected":""}}>Display</option>
                                                    <option value="n" {{$config->logistic_partner=='n'?"selected":""}}>Hidden</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="channel_partner">Channel Partners</label>
                                                <select name="channel_partner" class="form-control" id="channel_partner">
                                                    <option value="y" {{$config->channel_partner=='y'?"selected":""}}>Display</option>
                                                    <option value="n" {{$config->channel_partner=='n'?"selected":""}}>Hidden</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="brands">Brands Slider</label>
                                                <select name="brands" class="form-control" id="brands">
                                                    <option value="y" {{$config->brands=='y'?"selected":""}}>Display</option>
                                                    <option value="n" {{$config->brands=='n'?"selected":""}}>Hidden</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="press_coverage">Press Coverage</label>
                                                <select name="press_coverage" class="form-control" id="press_coverage">
                                                    <option value="y" {{$config->press_coverage=='y'?"selected":""}}>Display</option>
                                                    <option value="n" {{$config->press_coverage=='n'?"selected":""}}>Hidden</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="about">About Details</label>
                                                <textarea name="about" class="form-control" id="about" placeholder="Meta Description" rows="5">{{$config->about}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testimonial_image">Testimonial Image</label>
                                                <input type="file" name="testimonial_image" class="form-control" id="testimonial_image">
                                                <img src="{{asset($config->testimonial_image)}}" style="height: 50px;width: 50px;" alt="{{$config->title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="favicon">Upload Agreement</label>
                                                <input type="file" name="agreement" class="form-control" id="agreement">
                                                <a href="{{asset($config->agreement)}}" target="_blank">View Agreement</a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h3>Front End Titles</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="stats_title">Stats Title</label>
                                                <input type="text" class="form-control" id="stats_title" name="stats_title" placeholder="Stats Title" value="{{$config->stats_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="associates_title">Associates Title</label>
                                                <input type="text" class="form-control" id="associates_title" name="associates_title" placeholder="Associates Title" value="{{$config->associates_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="steps_title">Steps Title</label>
                                                <input type="text" class="form-control" id="steps_title" name="steps_title" placeholder="Steps Title" value="{{$config->steps_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="signup_title">Signup Title</label>
                                                <input type="text" class="form-control" id="signup_title" name="signup_title" placeholder="SignUp Title" value="{{$config->signup_title}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ease_title">Ease Title</label>
                                                <input type="text" class="form-control" id="ease_title" name="ease_title" placeholder="Ease Title" value="{{$config->ease_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="logistics_title">Logistics Title</label>
                                                <input type="text" class="form-control" id="logistics_title" name="logistics_title" placeholder="Logistics Title" value="{{$config->logistics_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="channel_title">Channel Title</label>
                                                <input type="text" class="form-control" id="channel_title" name="channel_title" placeholder="Channel Title" value="{{$config->channel_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="brand_title">Brand Title</label>
                                                <input type="text" class="form-control" id="brand_title" name="brand_title" placeholder="Brand Title" value="{{$config->brand_title}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="press_title">Press Title</label>
                                                <input type="text" class="form-control" id="press_title" name="press_title" placeholder="Press Title" value="{{$config->press_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subscribe_title">Subscribe Title</label>
                                                <input type="text" class="form-control" id="subscribe_title" name="subscribe_title" placeholder="Subscribe Title" value="{{$config->subscribe_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subscribe_title">Early COD Title</label>
                                                <input type="text" class="form-control" id="early_cod_title" name="early_cod_title" placeholder="Early COD Title" value="{{$config->e_cod_title}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subscribe_title">Early COD Features</label>
                                                <input type="text" class="form-control" id="early_cod_features" name="early_cod_features" placeholder="Early COD Features" value="{{$config->e_cod_features}}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h3>Order Management</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">GST Charges (%)</label>
                                                <input type="number" name="gst_charge" class="form-control" id="gst_charge" value="{{$config->gst_percent}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">RTO Charges (%)</label>
                                                <input type="number" name="rto_charge" class="form-control" id="rto_charge" value="{{$config->rto_charge}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Reversal Charges (%)</label>
                                                <input type="number" name="reverse_charge" class="form-control" id="reverse_charge" value="{{$config->reverse_charge}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Reconciliation Days</label>
                                                <input type="number" name="reconciliation_days" class="form-control" id="reconciliation_days" value="{{$config->reconciliation_days}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Invoice Genrate Days</label>
                                                <input type="number" name="invoice_generate_days" class="form-control" id="invoice_generate_days" value="{{$config->invoice_generate_days}}">
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Bank & Other Details</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Account Holder Name</label>
                                                <input type="text" name="account_holder" class="form-control" id="account_holder" value="{{$config->account_holder}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Account Number</label>
                                                <input type="text" name="account_number" class="form-control" id="account_number" value="{{$config->account_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">IFSC Code</label>
                                                <input type="text" name="ifsc_code" class="form-control" id="ifsc_code" value="{{$config->ifsc_code}}" minlength="11" maxlength="11">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Bank Name</label>
                                                <input type="text" name="bank_name" class="form-control" id="bank_name" value="{{$config->bank_name}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Bank Branch</label>
                                                <input type="text" name="bank_branch" class="form-control" id="bank_branch" value="{{$config->bank_branch}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">GSTIN Number</label>
                                                <input type="text" name="gstin" class="form-control" id="gstin" value="{{$config->gstin}}" minlength="15" maxlength="15">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">PAN Number</label>
                                                <input type="text" name="pan_number" class="form-control" id="pan_number" value="{{$config->pan_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">CIN Number</label>
                                                <input type="text" name="cin_number" class="form-control" id="cin_number" value="{{$config->cin_number}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">IRN Number</label>
                                                <input type="text" name="irn_number" class="form-control" id="irn_number" value="{{$config->irn_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">SAC Number</label>
                                                <input type="text" name="sac_number" class="form-control" id="sac_number" value="{{$config->sac_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">HSN Number</label>
                                                <input type="text" name="hsn_number" class="form-control" id="hsn_number" value="{{$config->hsn_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="testimonial_image">Signature Image</label>
                                                <input type="file" name="signature_image" class="form-control" id="signature_image">
                                                <img src="{{asset($config->signature_image)}}" style="height: 100px;width: 100px;" alt="{{$config->title}}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h3>Payment Information</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Razorpay Key</label>
                                                <input type="text" name="razorpay_key" class="form-control" placeholder="Enter Razorpay Key" id="razorpay_key" value="{{$config->razorpay_key}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Razorpay Secret</label>
                                                <input type="text" name="razorpay_secret" class="form-control" placeholder="Enter Razorpay Secret" id="razorpay_secret" value="{{$config->razorpay_secret}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="favicon">Payment QR Code</label>
                                                <input type="file" name="payment_qrcode" class="form-control" id="payment_qrcode">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                                <img src="{{asset($config->payment_qrcode)}}" style="height: 150px;width: 150px;" alt="{{$config->title}}">
                                        </div>
                                    </div>
                                    <hr>
                                    <h3>Other Configuration</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="bulkship_limit">Bulk Ship Limit</label>
                                                <input type="number" name="bulkship_limit" class="form-control" placeholder="Enter Bulk Ship Limit" id="bulkship_limit" value="{{$config->bulkship_limit}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mis_download_limit">MIS Download Limit</label>
                                                <input type="number" name="mis_download_limit" class="form-control" placeholder="Enter MIS Download Limit" id="mis_download_limit" value="{{$config->mis_download_limit}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="qc_charges">QC Charges</label>
                                                <input type="number" name="qc_charges" class="form-control" placeholder="Enter QC Charges" id="qc_charges" value="{{$config->qc_charges}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (left) -->
                    <!-- right column -->
                    <div class="col-md-6">

                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
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
        $('#quickForm').validate({
            rules: {
                title: {
                    required: true
                },
            },
            messages: {
                title: {
                    required: "Please enter title of the project"
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
            }
        });
    });
</script>
</body>
</html>
