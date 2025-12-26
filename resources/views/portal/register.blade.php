<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('portal.pages.styles')
</head>

<body>
<div class="container-fluid">
    <div class="container-fluid">
        <div class="d-flex flex-column" style="margin-left:3%;">
            <div class="content-wrapper">
                <div class="content-inner mt-5">
                    <h1 class="text-center text-primary"> Welcome To Twinnship</h1>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <img src="{{url('/')}}/assets/web/assets/images/Login..png!sw800" class="img-fluid"
                                 style="max-width: 100% !important; height: auto;">
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="m-auto mw-510 py-5 px-4 mt-5 card-row shadow rounded-10">
                                <h2 class="text-center mb-4">Experience The Next-level Logistics</h2>
                                <form>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="label">Name</label>
                                            <input type="name" class="form-control" placeholder="Name">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="label">Email ID</label>
                                            <input type="email" class="form-control" placeholder="Email">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="label">Contact No.</label>
                                            <input type="number" class="form-control" placeholder="Contact No.">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="label">Company Name</label>
                                            <input type="text" id="" class="form-control" placeholder="Company Name*">
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="label">Password</label>
                                            <input type="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                                        <div class="text-center mt-3">
                                            <p class="d-inline text-dark">Have an Account?</p> <a href="{{route('web.web-login')}}"
                                                                                                  style="color: #443e96;"
                                                                                                  class="d-inline">Sign
                                                In Now</a>
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
        <script src="{{url('/')}}/assets/web/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/sidebar-menu.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/dragdrop.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/rangeslider.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/sweetalert.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/quill.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/data-table.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/prism.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/clipboard.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/feather.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/simplebar.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/apexcharts.min.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/amcharts.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/custom/ecommerce-chart.js"></script>
        <script src="{{url('/')}}/assets/web/assets/js/custom/custom.js"></script>
    </div>
</div>
</body>

</html>
