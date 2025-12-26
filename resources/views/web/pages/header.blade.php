<header class="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMenu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{route('/')}}">
                <img src="{{asset('public/assets/seller/images/logo-light.png')}}" class="logo-light" height="30" alt="">
                <img src="{{asset($config->logo)}}" class="logo-dark" height="30" alt="">
            </a>
            <div class="d-flex justify-content-between align-items-center w-100">
                <ul class="navbar-nav" id="navMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/')}}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Features <i class="fa fa-chevron-down"></i></a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="{{route('web.ndr_management')}}" class="nav-link">NDR Management</a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{route('web.postpaid')}}" class="nav-link">Postpaid</a>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link">Hyperlocal Services</a>-->
                            <!--</li>-->
                            <li class="nav-item">
                                <a href="{{route('web.early_cod')}}" class="nav-link">Early COD</a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{route('web.recommendation_engine')}}" class="nav-link">Recommendation Engine</a>-->
                            <!--</li>-->
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Offering <i class="fa fa-chevron-down"></i></a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="{{route('web.pricing')}}" class="nav-link">Plans</a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{route('web.table_pricing')}}" class="nav-link">Pricing</a>-->
                            <!--</li>-->
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-0" href="">Integration<i class="fa fa-chevron-down"></i></a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="" class="nav-link">Channel Integration</a>
                            </li>
                            <li class="nav-item">
                                <a href="" class="nav-link">OMS Integration</a>
                            </li>
                            <li class="nav-item">
                                <a href="" class="nav-link">Courier Integration</a>
                            </li>
                        </ul>
                    </li>
                    <li class="p-0 d-lg-none">
                        <a href="{{route('seller.login')}}" class="btn btn-primary mx-1">Signin/Signup</a>
                    </li>
                    <li class="p-0 d-lg-none">
                        <a href="{{route('web.order_track')}}" class="btn btn-outline-primary mx-1">Track Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Tracking <i class="fa fa-chevron-down"></i></a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="{{route('web.order_track')}}" class="nav-link">Orders</a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="" class="pe-none nav-link">Shipments</a>-->
                            <!--</li>-->
                        </ul>
                    </li>

                </ul>
                <ul class="navbar-nav d-none d-lg-flex button-menu" id="navMenu">
                    <li class="p-0">
                        <a href="{{route('web.order_track')}}" class="btn btn-outline-primary mx-1">Track Order</a>
                    </li>
                    <li class="p-0">
                        <a href="{{route('seller.login')}}" class="btn btn-outline-primary mx-1">Signin</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
