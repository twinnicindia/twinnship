<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/remixicon.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/flaticon.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/sidebar-menu.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/simplebar.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/apexcharts.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/prism.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/rangeslider.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/sweetalert.min.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/quill.snow.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('assets/sellers/')}}/css/sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Login | {{ $config->title }}</title>
    <style>
        img {
        max-width: 100%;
        height: auto;
        max-width: 800px !important;
    }

    .card-row {
        border: none !important;
        align-items: end !important;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1) !important;
        border-radius: 25px !important;
    }
    .forgot-password-link {
        display: inline-block;  
        color: #007bff;        
        text-decoration: none; 
    }

    .forgot-password-link:hover {
        color: #0056b3;       
        text-decoration: underline;
    }

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex flex-column"  style="margin-left:3%;">
            <div class="content-wrapper">
                <div class="content-inner mt-5">
                    <h1 class="text-center text-primary"> Welcome To {{ $config->title }}</h1>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <img src="{{asset('assets/sellers/')}}/images/Login..png!sw800" class="img-fluid" style="max-width: 100% !important; height: auto;">
                       </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="m-auto mw-510 py-5 px-4 mt-5 card-row  rounded-10">
                                <h2 class="text-center mb-4">Forgot To Using {{ $config->title }}!</h2>

                                <form class="needs-validation" novalidate method="post" id="forget_form" action="{{route('seller.submit_forget')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label class="label">UserName</label>
                                            <input type="text" name="username" class="form-control" id="forgetUsername" placeholder="Mobile number or Email address" required>
                                            <div class="invalid-feedback">
                                                Enter email or mobile number.
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2 otp_div" style="display: none;">
                                            <label class="label">OTP Code</label>
                                            <input type="number" name="username" class="form-control" name="otp_code" id="otp_code" placeholder="Enter OTP Code" minlength="6" maxlength="6" required>
                                            <div class="invalid-feedback">
                                                Enter Valid OTP.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                        </div>
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="button" id="findAccountButton" class="btn btn-primary check_div">Find Account</button>
                                        <button type="button" id="verifyOTPButton" style="display: none;" class="btn btn-primary otp_div">Verify OTP</button>
                                    </div>
                                </form>
                                <form method="post" id="change_form" style="display:none;">
                                    @csrf
                                    <div class="col-12 mb-2">
                                        <input type="password" class="form-control" name="password" id="password" aria-describedby="emailHelp" placeholder="New Password" required>
                                        <small style="font-size: 8px;" class="passwordLabel label">Please Use 1 Uppercase,1 Number,1 Special Character and Minimum Length of 10 for Creating Password</small>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" aria-describedby="emailHelp" placeholder=" Confirm New Password" required>
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="button" id="changePasswordButton" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                                <div class="d-grid mt-4">
                                        <div class="text-center mt-3">
                                            <p class="d-inline text-dark">{{ $config->title }}?</p> <a href="{{route('seller.login')}}" style="color: #443e96;" class="d-inline">Login</a>
                                        </div>
                                    </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/sidebar-menu.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/dragdrop.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/rangeslider.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/sweetalert.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/quill.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/data-table.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/prism.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/clipboard.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/feather.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/simplebar.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/apexcharts.min.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/amcharts.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/custom/ecommerce-chart.js"></script>
    <script src="{{asset('assets/sellers/')}}/js/custom/custom.js"></script>
    <script src="{{asset('public/assets/seller/')}}/js/notify.js"></script>

    <script type="text/javascript">
    var base_path='{{url('/')}}/',ref_code='';

    $(document).ready(function () {

        $(document).on('keypress','#otp_code',function(e){
            if($(e.target).prop('value').length>=6){
            if(e.keyCode!=32)
            {return false}
        }})
        $(document).on('keypress','#password',function(e) {
            if ($(e.target).prop('value').length >= 15) {
                if (e.keyCode != 32) {
                    return false
                }
            }
        });
        $(document).on('keypress','#confirm_password',function(e) {
            if ($(e.target).prop('value').length >= 15) {
                if (e.keyCode != 32) {
                    return false
                }
            }
        });

        $('#findAccountButton').click(function () {
            var that=$('#forgetUsername');
            if(that.val().trim()===''){
                alert('Please enter username');
                return false;
            }
            $.ajax({
                type : 'post',
                data : {
                    'username' : that.val(),
                    '_token' : '{{csrf_token()}}'
                },
                url : '{{route('seller.submit_forget')}}',
                success : function (response) {
                    var info=JSON.parse(response);
                    if(info.status==='true'){
                        ref_code = info.ref_code;
                        $('.check_div').hide();
                        $('.otp_div').fadeIn();
                    }else{
                        alert('Invalid Email Address');
                    }
                }
            });
        });
        $('#verifyOTPButton').click(function () {
            var otpCode=$('#otp_code');
            if(otpCode.val().trim()===''){
                alert('Please enter OTP');
                return false;
            }
            $.ajax({
                type : 'get',
                url : '{{url('/')}}/seller-verify-otp/'+otpCode.val()+"/"+ref_code,
                success : function (response) {
                    var info=JSON.parse(response);
                    if(info.status==='true'){
                        $('#forget_form').hide();
                        $('#change_form').fadeIn();
                    }
                    else{
                        alert('Invalid OTP Code');
                    }
                }
            });
        });
        $('#changePasswordButton').click(function () {
            var password=$('#password');
            var confirm_password=$('#confirm_password');
            if(password.val().trim()===''){
                alert('Please enter new password');
                return false;
            }
            if(password.val().trim()!==confirm_password.val().trim()){
                alert('Both passwords are not same');
                return false;
            }
            else{
                $.ajax({
                    type : 'get',
                    data : {
                        'password' : password.val(),
                        'username' : $('#forgetUsername').val()
                    },
                    url : '{{route('seller.reset_seller_password')}}',
                    success : function (response) {
                        var info=JSON.parse(response);
                        if(info.status==='true'){
                            alert('Password changed successfully');
                            window.location.href = '{{route('seller.login')}}';
                        }
                    }
                });
            }
        });
        $('#password').keyup(function () {
            checkValidPassword();
        });
    });
    function checkValidPassword() {
        let result = false;
        var upperCase= new RegExp('[A-Z]');
        var lowerCase= new RegExp('[a-z]');
        var numbers = new RegExp('[0-9]');
        var special = new RegExp('[^0-9A-Za-z]');
        let that = $('#password');
        if(that.val().trim().length >= 10 && that.val().match(upperCase) && that.val().match(lowerCase) && that.val().match(numbers) && that.val().match(special)){
            $('.passwordLabel').removeClass('label-danger').addClass('label-success');
            result = true;
        }else{
            $('.passwordLabel').removeClass('label-success').addClass('label-danger');
        }
        return result;
    }
</script>
<script src="{{asset('public/assets/seller/')}}/js/myScript.js"></script>

</body>

</html>
