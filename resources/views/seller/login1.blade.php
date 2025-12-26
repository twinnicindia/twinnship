<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Seller Login | {{ $config->title }} </title>

    @include('seller.pages.styles')
    <style>
        ul.social-login {
            padding: 0;
            list-style: none;
            display: grid;
            grid-template-columns: repeat(1,1fr);
            grid-gap: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
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
                        <small class="text-center w-75 mx-auto mb-4 d-block opacity-4">{{$config->login_message}}</small>
                        <form class="needs-validation" novalidate method="post" action="{{route('seller.check_login')}}">
                            @csrf
                            <div class="form-group">
                                <input type="email" class="form-control" id="loginUsername" name="username" aria-describedby="emailHelp" placeholder="Enter email" required>
                                <div class="invalid-feedback">
                                    Enter a valid email address.
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Password" required>
                                <div class="invalid-feedback">
                                    Please enter password.
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-check text-left col-md">
                                    <input type="checkbox" name="remember" value="yes"> Remember Me
                                </div>
                                <div class="form-check text-right col-md">
                                    <a href="{{route('seller.forget')}}">Forgot-password?</a>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </form>
                         <ul class="social-login">
                            <li>
                                <a href="{{route('google.request')}}" class="google">
                                    <img src="{{asset('public/assets/seller/')}}/images/google-icon.png" alt=""> <span> Continue With Google</span>
                                </a>
                            </li>
                            <!--<li>-->
                            <!--    <a href="{{route('facebook.request')}}" class="facebook">-->
                            <!--        <img src="{{asset('public/assets/seller/')}}/images/facebook-icon.png" alt=""> <span>Facebook</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>
                        <div class="form-check text-center">Don't Have an account? <a href="{{route('seller.register')}}">Create now</a></div>
                    </div>
                </div>
            </div>
        </div>
        @include('seller.pages.footer')
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/popper.min.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{asset('public/assets/seller/')}}/js/myScript.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/notify.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/prettify.js"></script>

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function(){
        <?php
        if(Session()->has('notification'))
        {
            switch (Session('notification')['type']){
                case "success":
                    echo "$.notify(' ".Session('notification')['message']."', {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});";
                    break;
                case "error":
                    echo "$.notify(' ".Session('notification')['message']."', {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});";
                    break;
            }
            Session()->forget('notification');
        }
        ?>
    });
</script>



</body>
</html>
