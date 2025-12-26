<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/seller/')}}/css/bootstrap.min.css" type="text/css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/seller/')}}/css/style.css" type="text/css">
    <link href="{{asset('public/assets/seller/')}}/css/notify.css" rel="stylesheet">
    <link href="{{asset('public/assets/seller/')}}/css/prettify.css" rel="stylesheet">
    <link rel="icon" href="{{asset($config->favicon)}}">
    <title> Seller Registration | {{ $config->title }} </title>
    <style type="text/css">
        .label-danger {background:whitesmoke;color:red;}
        .label-success {background:whitesmoke;color:green;}
    </style>
</head>
<body>

<div class="login-page d-md-flex align-items-center">
    <div class="container h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-8 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="navbar-brand mx-auto w-100 text-center">
                            <a href="{{route('/')}}" class="d-block"><img src="{{asset($config->logo)}}" height="30" alt=""></a>
                        </div>
                        <small class="text-center w-75 mx-auto mb-4 d-block opacity-4">{{$config->register_message}}</small>
                        <form class="needs-validation" novalidate method="post" action="{{route('seller.submit_register')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="first_name" id="registerFirstName" class="form-control" placeholder="First Name" required>
                                        <div class="invalid-feedback">
                                            Enter first name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="last_name" id="registerLastName" type="text" class="form-control" placeholder="Last Name" required>
                                        <div class="invalid-feedback">
                                            Enter last name
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="text" name="company_name" id="registerCompanyName" class="form-control" placeholder="Company Name" required>
                                <div class="invalid-feedback">
                                    Enter company name
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="email" name="email" id="registerEmail" class="form-control" placeholder="Email Address" required>
                                <div class="invalid-feedback">
                                    Enter a valid email address.
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group ship-form-group">
                                    <div class="input-group-prepend">
                                        <select class="form-control" name="country" id="country">
                                            <option value="+91">+91</option>
                                            <option value="+07">+07</option>
                                            <option value="+01">+01</option>
                                        </select>
                                    </div>
                                    <input type="number" maxlength="10" name="mobile" id="registerMobile" class="form-control" placeholder="Mobile Number" required>
                                    <div class="invalid-feedback">
                                        Enter a valid mobile number.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="display: none;" id="otp_class">
                                <input type="number" class="form-control" name="otp_code" id="otp_code" placeholder="Enter OTP Code" minlength="6" maxlength="6" required>
                                <div class="invalid-feedback">
                                    Enter Valid OTP.
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" id="registerPassword" class="form-control" placeholder="Password" required>
                                <div class="invalid-feedback">
                                    Enter a password.
                                </div>
                                <small style="font-size: 8px;" class="passwordLabel label">Please Use 1 Uppercase,1 Number,1 Special Character and Minimum Length of 10 for Creating Password</small>
                            </div>

                            <button type="submit" onsubmit="return checkValidPassword();" class="btn btn-primary btnSubmit">Sign Up</button>
                        </form>

                        <div class="form-check text-center">Already Have an account? <a href="{{route('seller.login')}}">Sign in</a></div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="text-white">
            <p class="opacity-3 text-white">Copyright Ⓒ {{$config->copyright}}</p>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/popper.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/bootstrap.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/notify.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/prettify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{asset('public/assets/seller/')}}/js/myScript.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var registerMobile=$('#registerMobile');
        var registerEmail=$('#registerEmail');
        var registerOtp=$('#otp_class');

        registerMobile.blur(function () {
            $('#otp_code').removeClass('is-invalid');
            if(registerMobile.val()!==''){
                $.ajax({
                    type : 'get',
                    url : base_path + 'check-SellerVerifyByOtpMobile/' + registerMobile.val() + '/' + registerEmail.val(),
                    success : function (response) {
                        var info=JSON.parse(response);
                        if(info.status=='true'){
                            registerOtp.show();
                            $('#otp_code').val('').focus();
                        }
                        else{
                            $('#registerMobile').val('').focus();
                        }
                    },
                    error : function (response) {
                        $.notify("Email field is Required!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                        $('#registerEmail').val('').focus();
                    }
                });
            }
        });
        $('#otp_code').blur(function () {
            var otpCode=$('#otp_code');
            var mobile=$('#registerMobile').val();
            $.ajax({
                type : 'get',
                url : '{{url('/')}}/seller-otp-verify/'+otpCode.val() + '/'+ mobile,
                success : function (response) {
                    var info=JSON.parse(response);
                    if(info.status==='false'){
                        otpCode.addClass('is-invalid');
                        otpCode.next().html('Oops... Please enter valid OTP');
                        otpCode.val('').focus();
                    }else{
                        otpCode.removeClass('is-invalid');
                        otpCode.addClass('label-success');
                        otpCode.next().html('Your OTP Successfully Verified');
                    }
                }
            });

        });
    });

    $('#otp_code').focusout(function () {
        var otpCode=$('#otp_code');
        if(otpCode.val().trim()===''){
            otpCode.addClass('is-invalid');
            otpCode.next().html('Oops... OTP Field Cannot be Empty');
            //otpCode.val('').focus();
            return false;
        }else{
            otpCode.removeClass('is-invalid');
        }
    })

    $('#registerMobile').on('keyup', function() {
        if(this.value.length < 10)
        {
            registerMobile.removeClass('is-invalid');
            registerMobile.next().html('');
        }
    });
    $(document).on('keypress','#registerMobile',function(e){
        if($(e.target).prop('value').length>=10){
            if(e.keyCode!=32)
            {return false}
        }});
    var base_path='{{url('/')}}/';
    @if(Session()->has('registered'))
    Swal.fire({
        icon: 'success',
        title: 'Great...',
        text: 'Your account has been created successfully'
        @php(Session()->forget('registered'))
    });
    @endif
    $(document).ready(function(){
        $('#registerPassword').keyup(function () {
            checkValidPassword();
        });
    });
    function checkValidPassword() {
        let result = false;
        var upperCase= new RegExp('[A-Z]');
        var lowerCase= new RegExp('[a-z]');
        var numbers = new RegExp('[0-9]');
        var special = new RegExp('[^0-9A-Za-z]');
        let that = $('#registerPassword');
        if(that.val().trim().length >= 10 && that.val().match(upperCase) && that.val().match(lowerCase) && that.val().match(numbers) && that.val().match(special)){
            $('.passwordLabel').removeClass('label-danger').addClass('label-success');
            result = true;
        }else{
            $('.passwordLabel').removeClass('label-success').addClass('label-danger');
        }
        return result;
    }
</script>

</body>
</html>
