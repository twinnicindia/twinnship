<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('portal.pages.styles')
</head>

<body>
<div class="container-fluid">
    <div class="d-flex flex-column"  style="margin-left:3%;">
        <div class="content-wrapper">
            <div class="content-inner mt-5">
                <h1 class="text-center text-primary"> Welcome To Twinnship</h1>
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <img src="{{url('/')}}/assets/web/assets/images/Login..png!sw800" class="img-fluid" style="max-width: 100% !important; height: auto;">
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="m-auto mw-510 py-5 px-4 mt-5 card-row  rounded-10">
                            <h2 class="text-center mb-4">Login To Using Twinnship!</h2>

                            <form>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <label class="label">Email ID</label>
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="label">Password</label>
                                        <input type="password" class="form-control" placeholder="Password">
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                                    <div class="text-center mt-3">
                                        <p class="d-inline text-dark">New To Twinnship?</p> <a href="{{route('web.web-register')}}" style="color: #443e96;" class="d-inline">Sign Up Now</a>
                                    </div>
                                </div>
                                <span class="d-block fs-18 fw-semibold text-center or mb-4">
                                        <span class="bg-body-bg d-inline-block py-1 px-3">or</span>
                                    </span>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-lg quick"><img src="{{url('/')}}/assets/web/assets/images/google.png" alt="" style="height: 30px;" class="me-2">Google</button>
                                    <button type="submit" class="btn btn-lg quick"><img src="{{url('/')}}/assets/web/assets/images/facebook.png" alt="" style="height: 30px;" class="me-2">Facebook</button>
                                    <button type="submit" class="btn btn-lg quick"><img src="{{url('/')}}/assets/web/assets/images/telephone.png" alt="" style="height: 30px;" class="me-2">Phone No</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
</body>

</html>
