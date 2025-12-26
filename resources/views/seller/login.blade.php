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
                                <h2 class="text-center mb-4">Login To Using {{ $config->title }}!</h2>

                                <form class="needs-validation" novalidate method="post" action="{{route('seller.check_login')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label class="label">Email ID</label>
                                            <input type="text" name="username" class="form-control" placeholder="Email" required>
                                            <div class="invalid-feedback">
                                                Enter a valid email address.
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="label">Password</label>
                                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                                            <div class="invalid-feedback">
                                                Please enter password.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <a href="{{route('seller.forget')}}" class="forgot-password-link">Forgot-password?</a>
                                        </div>
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        <div class="text-center mt-3">
                                            <p class="d-inline text-dark">New To {{ $config->title }}?</p> <a href="{{route('seller.register')}}" style="color: #443e96;" class="d-inline">Sign Up Now</a>
                                        </div>
                                    </div>
{{--                                    <span class="d-block fs-18 fw-semibold text-center or mb-4">--}}
{{--                                        <span class="bg-body-bg d-inline-block py-1 px-3">or</span>--}}
{{--                                    </span>--}}
{{--                                    <div class="d-flex justify-content-between align-items-center">--}}
{{--                                        <button type="submit" class="btn btn-lg quick"><img src="{{asset('assets/sellers/')}}/images/google.png" alt="" style="height: 30px;" class="me-2">Google</button>--}}
{{--                                        <button type="submit" class="btn btn-lg quick"><img src="{{asset('assets/sellers/')}}/images/facebook.png" alt="" style="height: 30px;" class="me-2">Facebook</button>--}}
{{--                                        <button type="submit" class="btn btn-lg quick"><img src="{{asset('assets/sellers/')}}/images/telephone.png" alt="" style="height: 30px;" class="me-2">Phone No</button>--}}
{{--                                    </div>--}}
                                </form>

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

    <script>
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
