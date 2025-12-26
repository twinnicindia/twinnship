<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'/>
    <title>Contact Us || {{$config->title}}</title>
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
                        <h3> Contact us </h3>
                        <h1> Have any queries? We’re all ears!</h1>
                        <p> Our team is trained, equipped &amp; ready to guide you from scratch to success. </p>
                    </div>
                </div>
            </div>
            <div class="floatImageLeft">
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/person.png" alt=""></span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/contact.png" alt=""></span>
            </div>
            <div class="floatImageRight">
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/shipping.png" alt=""> </span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/person-1.png" alt=""> </span>

            </div>
        </div>
    </section>


    <section class="contactBoxSection">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    <div class="contactBoxWrapper">
                        <div class="iconWrappers">
                            <ul>
                                <li>
                                    <div class="iconWrap"><img src="{{url('/')}}/assets/web/assets/images/call.png" alt=""
                                                               style="height: 25px;"> </span>
                                    </div>
                                    <div class="contentWrapIcon">
                                        <h3> Ring us up </h3>
                                        <p><a href="#" rel="noopener"> +91- {{$config->mobile}} </a></p>
                                    </div>
                                </li>

                                <li>
                                    <div class="iconWrap"><img src="{{url('/')}}/assets/web/assets/images/email.png" alt=""
                                                               style="height: 25px;"> </span>
                                    </div>
                                    <div class="contentWrapIcon">
                                        <h3> Write to us </h3>
                                        <p><a href="#">{{$config->email}}</a></p>
                                    </div>
                                </li>

                                <li>
                                    <div class="iconWrap"><img src="{{url('/')}}/assets/web/assets/images/location.png" alt=""
                                                               style="height: 25px;"> </span>
                                    </div>
                                    <div class="contentWrapIcon">
                                        <h3> Visit us </h3>
                                        <p><a href="#" rel="noopener"> {!! nl2br($config->address) !!}</a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="imageWrapperMap">
                            <iframe src="//maps.google.com/maps?q=28.609766054682787, 77.39212182883557&z=15&output=embed"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="contactUsCTASection">
        <div class="container">
            <div class="row">
                <div class="ctaWrapperContact">
                    <div class="col-12 col-md-6">
                        <img width="717" height="908" decoding="async" src="{{url('/')}}/assets/web/assets/images/Contact-us.png"
                             class="ctaBoyImage" alt="" style="max-height: 440px;width: auto;">
                    </div>
                    <div class="col-12 col-md-6">
                        <h2> Twinnship customer care is always here,</h2>
                        <p> Go to the TwinShip customer care self-help page for instant answers to frequently asked
                            questions.</p>
                        <a href="#" class="whiteBtn"> Ask Now </a>
                    </div>


                </div>
            </div>
        </div>
    </section>


    <section class="contatUsLeftAndRightContact pb0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="upperHeaderContentWrapper">
                        <h2> How does our customer grievance policy work? </h2>
                        <p> While we believe in providing you with the best experience, you can always tell us how
                            we <br> can improve our services. We’ll be more than happy to assist you. </p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="contentWrapperLeftRight">
                        <div class="contentWrapper">
                            <h3> Customer support</h3>
                            <p> Contact the Twinnship customer care team &amp; get an online resolution to your
                                queries/complaints across channels, including chat. Our team will address your
                                concern within 5 business days from receipt. </p>
                            <a href="#" class="linkBtn"> Contact customer support</a>
                        </div>
                        <div class="imageWrapper greenAfterBg">
                            <img width="1330" height="813" decoding="async" src="{{url('/')}}/assets/web/assets/images/person-3.png"
                                 alt="img">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="Section footerCtaSection">
        <div class="footerCtaBgWrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12 p0">
                        <div class="contentWrapper">
                            <h2 class="headingText"> Ready to begin your growth journey? </h2>
                            <p class="content"> Start without a platform fee. No hidden charges. </p>
                            <a href="{{route('web.web-register')}}" target="_blank" class="whiteBtn" rel="noopener"> Sign up for free </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
</div>

</body>

</html>
