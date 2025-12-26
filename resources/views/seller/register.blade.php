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
    <title> Registration | {{ $config->title }}</title>
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
                            <div class="m-auto mw-510 py-5 px-4 mt-5 card-row shadow rounded-10">
                                <h2 class="text-center mb-4">Experience The Next-level Logistics</h2>
                                <form  class="needs-validation" novalidate method="post" action="{{route('seller.submit_register')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label for="first_name" class="label">First Name</label>
                                            <input type="text" class="form-control" placeholder="Name" name="first_name" required>
                                            <div class="invalid-feedback">
                                                Please enter first name.
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label for="first_name" class="label">Last Name</label>
                                            <input type="text" class="form-control" placeholder="Name" name="last_name" required>
                                            <div class="invalid-feedback">
                                                Please enter last name.
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="label">Email ID</label>
                                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                                            <div class="invalid-feedback">
                                                Please enter email.
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="label">Contact No.</label>
                                            <input type="number" class="form-control" placeholder="Contact No." name="mobile" required>
                                            <div class="invalid-feedback">
                                                Please enter contact no.
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="label">Company Name</label>
                                            <input type="text" id="" class="form-control" placeholder="Company Name*" name="company_name" required>
                                            <div class="invalid-feedback">
                                                Please enter company name.
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="label">Password</label>
                                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                                            <div class="invalid-feedback">
                                                Please enter password.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                                        <div class="text-center mt-3">
                                            <p class="d-inline text-dark">Have an Account?</p> <a href="{{route('seller.login')}}" style="color: #443e96;" class="d-inline">Sign In Now</a>
                                        </div>
                                    </div>
                                </form>
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
</body>

</html>
