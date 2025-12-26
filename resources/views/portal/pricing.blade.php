<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'/>
    <title>Pricing || {{$config->title}}</title>
    @include('portal.pages.styles')
</head>

<body data-rsssl=1>

<div id="page" class="site">
    @include('portal.pages.header')

    <section class="mainBannerContactUs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="textCenter contentWrapperBanner">
                        <h3> Pricing and Offers </h3>
                        <h1> Pricing and Offers!</h1>
                        <p> Try Twinnship with a free account and Upgrade as you grow </p>
                    </div>
                </div>
            </div>
            <div class="floatImageLeft">
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/person.png" alt=""></span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/contact.png" alt=""></span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/icon3.png" alt=""> </span>

            </div>
            <div class="floatImageRight">
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/shipping.png" alt=""> </span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/icon3.png" alt=""> </span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/person-1.png" alt=""> </span>
            </div>
        </div>
    </section>

    @include('portal.pages.brand')


    <section class="contactUsCTASection">
        <div class="container-fluid">
            <div class="container">
                <h1 class="text">Pay only for what you ship</h1>
                <div class="row">
                    <div class="col-sm-4 mb-5">
                        <div class="card text-center">
                            <div class="title">
                                <i class="fa fa-truck" aria-hidden="true"></i>
                                <h2 style="white-space: nowrap;">MONTHLY ORDERS</h2>
                                <h3 style="color: #000000;"> <b>0–1,000</b></h3>
                            </div>
                            <div class="price">
                                <h4><sup>₹</sup>29 <span class="small-text">/order</span></h4>
                            </div>
                            <div class="option">
                                <ul>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i> Plug & Play integrations</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>SMS/Email Notifications</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i> Email Support</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Zero Subscription Charge</li>
                                </ul>
                            </div>
                            <a href="{{route('web.web-register')}}">Sign Up </a>
                        </div>
                    </div>
                    <!-- END Col one -->
                    <div class="col-sm-4 mb-5">
                        <div class="card text-center">
                            <div class="title">
                                <i class="fa fa-truck" aria-hidden="true"></i>
                                <h2 style="white-space: nowrap;">MONTHLY ORDERS</h2>
                                <h3 style="color: #000000;"> <b>1000–5000</b></h3>
                            </div>
                            <div class="price">
                                <h4><sup>₹</sup>26 <span class="small-text">/order</span></h4>
                            </div>
                            <div class="option">
                                <ul>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i> Plug & Play integrations</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>SMS/Email Notifications</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>On-call Account Managers</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Zero Subscription Charge</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Easy Billing Options</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Real-Time NDR response capturee</li>
                                </ul>
                            </div>
                            <a href="{{route('web.web-register')}}">Sign Up</a>
                        </div>
                    </div>
                    <!-- END Col two -->
                    <div class="col-sm-4">
                        <div class="card text-center">
                            <div class="title">
                                <i class="fa fa-truck" aria-hidden="true"></i>
                                <h2 style="white-space: nowrap;">MONTHLY ORDERS</h2>
                                <h3 style="color: #000000;"> <b>5000 ></b></h3>
                            </div>
                            <div class="price">
                                <h4><sup>₹</sup>22 <span class="small-text">/order</span></h4>
                            </div>
                            <div class="option">
                                <ul>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i> Plug & Play integrations</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>SMS/Email Notifications</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>On-call Account Managers</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Zero Subscription Charge</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Easy Billing Options</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Early COD Remittance at No Cost</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>Weekly Performance Review & Consultation</li>
                                    <li> <i class="fa fa-check" aria-hidden="true"></i>NDR & COD confirmation calling Assistance</li>
                                </ul>
                            </div>
                            <a href="{{route('web.web-register')}}">Sign Up</a>
                        </div>
                    </div>
                    <!-- END Col three -->
                </div>
            </div>
        </div>
    </section>

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
</div>

</body>

</html>
