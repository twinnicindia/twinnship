<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <title>Home Page || {{$config->title}}</title>
    @include('portal.pages.styles')

</head>

<body data-rsssl=1>

<div id="page" class="site">
    @include('portal.pages.header')

    <section class="mainBannerhome">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="textCenter contentWrapperBanner">
                                <h1> Streamlining Supply Chains for </h1>
                                <h1 style="color: rgb(255, 238, 0);"> Efficiency
                                    and Reliability</h1>
                                <p style="color: rgb(0, 0, 0);">Partner with Us
                                    for Tailored Logistics Solutions That Drive
                                    Your Success </p>
                            </div>
                        </div>
                    </div>
                    <div class="floatImageLeft">
                        <span class="iconsContactUs"><img
                                src="{{url('/')}}/assets/web/assets/images/person.png" alt></span>
                        <span class="iconsContactUs"><img
                                src="{{url('/')}}/assets/web/assets/images/men-women.svg"
                                alt></span>
                        <span class="iconsContactUs"><img
                                src="{{url('/')}}/assets/web/assets/images/contact.png"
                                alt></span>
                    </div>
                    <div class="floatImageRight">
                        <span class="iconsContactUs"> <img
                                src="{{url('/')}}/assets/web/assets/images/shipping.png" alt>
                        </span>
                        <span class="iconsContactUs"><img
                                src="{{url('/')}}/assets/web/assets/images/ai.svg" alt></span>
                        <span class="iconsContactUs"> <img
                                src="{{url('/')}}/assets/web/assets/images/person-1.png" alt>
                        </span>

                    </div>
                </div>
            </section>

            <section class="contatUsLeftAndRightContact pb0">
                <div class="container">
                    <div class="row">

                        <div class="col-12">
                            <div class="contentWrapperLeftRight">
                                <div class="contentWrapper">
                                    <h3> Why Twinnship ?</h3>
                                    <div style="margin-top: 5%; margin-bottom: 7%;">
                                        <p>Faster Delivery & Reduced Costs. Did you know nearly 49% of &amp;
                                            customers are more likely to shop online if they
                                            get same-day or next-day delivery .</p>
                                    </div>

                                    @foreach($stats as $s)
                                    <h1 class="text">{{$s->number}} +
                                        <P class="small-text" style="color: #000000;">{{$s->title}}</P>
                                    </h1>
                                    @endforeach
                                </div>
                                <div class="imageWrapper greenAfterBg">
                                    <img width="1330" height="900" decoding="async" src="{{url('/')}}/assets/web/assets/images/seller.jpg"
                                        alt="img">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            <section class="courierServices">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="servicesWrapper">
                                <h2 class="servicesTitle">Courier Services</h2>
                                <p class="servicesDescription">
                                    A courier service is a company, usually a
                                    private firm, that facilitates the shipping
                                    of parcels and important documents. They
                                    offer speedy, next-day delivery within
                                    metropolitan areas.
                                </p>
                                <div class="servicesList">
                                    <div class="serviceItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/p1.png"
                                            alt="Check Progress"
                                            class="serviceIcon">
                                        <div class="serviceText">
                                            <h3
                                                style="color: #735ae5;">Carefully
                                                Check Progress Using Handheld
                                                Computers</h3>
                                            <p>Monitor delivery statuses and
                                                ensure accuracy using handheld
                                                computing devices.</p>
                                        </div>
                                    </div>
                                    <div class="serviceItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/p2.png"
                                            alt="Manage Requests"
                                            class="serviceIcon">
                                        <div class="serviceText">
                                            <h3 style="color: #735ae5;">Manage
                                                Pickup Requests and Daily
                                                Operations</h3>
                                            <p>Handle pickup requests and manage
                                                warehousing and delivery
                                                information with automated
                                                systems.</p>
                                        </div>
                                    </div>
                                    <div class="serviceItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/p3.png"
                                            alt="Real-time Progress"
                                            class="serviceIcon">
                                        <div class="serviceText">
                                            <h3
                                                style="color: #735ae5;">Real-time
                                                Work Progress</h3>
                                            <p>Track work progress with barcode
                                                scanning and data tabulation
                                                across all shipping stages.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="contatUsLeftAndRightContact ">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="contentWrapperLeftRight">
                                <div class="contentWrapper">
                                    <div class="about-one_imgbox">
                                        <div class="about-one_img1">
                                            <img
                                                src="{{url('/')}}/assets/web/assets/images/about-us-01.png"
                                                class="img-fluid" alt>
                                        </div>
                                        <div class="about-one_img2">
                                            <img
                                                src="{{url('/')}}/assets/web/assets/images/about-us-02.png"
                                                class="img-fluid" alt>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="pbmit-subtitle">About Us</p>
                                    <h1 class="pbmit-title">Shipments delivered
                                        time with no hassle.</h1>
                                    <p class="text-3"
                                        style="color: #000000;">Twinnic India
                                        Private Limited (Twinnship) is an Asia
                                        based company dedicated to providing
                                        Courier Services with a comprehensive
                                        network covering the region. We offer a
                                        complete range of express services to
                                        handle from documents to parcels
                                        freight. We are totally committed to
                                        meeting all the express needs of the
                                        Asian business communities.</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul
                                                class="list-group list-group-borderless">
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Monthly
                                                        Checkups</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Caring
                                                        Medical Team</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Proactive
                                                        and Fast Results</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul
                                                class="list-group list-group-borderless">
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Monthly
                                                        Checkups</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Caring
                                                        Medical Team</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span
                                                        class="pbmit-icon-list-icon">
                                                        <i class="fa fa-check"
                                                            aria-hidden="true"></i>
                                                    </span>
                                                    <span
                                                        class="pbmit-icon-list-text">Proactive
                                                        and Fast Results</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <section>
                <div class="container-fluid">
                    <div class="container">
                        <h3 class="Courier-h3">Our Courier Partner</h3>
                        <div class="container py-5">
                            <div class="row_branchen slider" id="sliderData">

                                <div
                                    class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="cardss">
                                        <div class="image__wrapper">
                                            <div class="card__shadow--1"></div>
                                            <img class="card-img-top"
                                                src="{{url('/')}}/assets/web/assets/images/c1.png"
                                                alt="Angebote - Verkaufsautomaten">
                                        </div>
                                        <div class="card-body pb-4 pt-3">
                                            <p class="card-text">
                                                Xpressbees, one of the fastest
                                                growing express logistics
                                                service providers in India,
                                                specialise in providing
                                                same/next day delivery, cash on
                                                delivery, reverse pickup,
                                                and reverse shipping.
                                            </p>
                                        </div>
                                        <div class="card-content">
                                            <div class="option">
                                                <p>Serviceability:</p>
                                                <p>20000 pin codes</p>
                                            </div>
                                            <div class="option">
                                                <p>Cash on delivery:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>Tracking:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>International courier
                                                    facility:</p>
                                                <p>No</p>
                                            </div>
                                            <div class="option">
                                                <p>Domestic courier
                                                    facility:</p>
                                                <p>Yes</p>
                                            </div>
                                        </div>
                                        <div class="link">
                                            <a
                                                href="#"
                                                target="_blank"
                                                class="linkBtn link-click">
                                                Let's start
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24">
                                                    <path
                                                        d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="cardss">
                                        <div class="image__wrapper">
                                            <div class="card__shadow--1"></div>
                                            <img class="card-img-top"
                                                src="{{url('/')}}/assets/web/assets/images/c2.png"
                                                alt="Angebote - Verkaufsautomaten">
                                        </div>
                                        <div class="card-body pb-4 pt-3">
                                            <p class="card-text">
                                                Delhivery, India’s fastest
                                                growing courier company, with
                                                same/next day delivery
                                                capabilities. It services over
                                                18000
                                                pin codes in India, offering
                                                48-96 hour deliveries for
                                                long-distance orders.
                                            </p>
                                        </div>
                                        <div class="card-content">
                                            <div class="option">
                                                <p>Serviceability:</p>
                                                <p>18000 pin codes</p>
                                            </div>
                                            <div class="option">
                                                <p>Cash on delivery:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>Tracking:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>International courier
                                                    facility:</p>
                                                <p>No</p>
                                            </div>
                                            <div class="option">
                                                <p>Domestic courier
                                                    facility:</p>
                                                <p>Yes</p>
                                            </div>
                                        </div>
                                        <div class="link">
                                            <a
                                                href="#"
                                                target="_blank"
                                                class="linkBtn link-click">
                                                Let's start
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24">
                                                    <path
                                                        d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="cardss">
                                        <div class="image__wrapper">
                                            <div class="card__shadow--1"></div>
                                            <img class="card-img-top ecomimg"
                                                src="{{url('/')}}/assets/web/assets/images/c3.png"
                                                alt="Angebote - Verkaufsautomaten">
                                        </div>
                                        <div class="card-body pb-4 pt-3">
                                            <p class="card-text">
                                                Ecom Express is a leading
                                                logistics solutions provider
                                                that
                                                offers end-to-end services. It
                                                focuses on delivery
                                                capability, scalability,
                                                customisation and
                                                sustainability.
                                            </p>
                                        </div>
                                        <div class="card-content">
                                            <div class="option">
                                                <p>Serviceability:</p>
                                                <p>27000 pin codes</p>
                                            </div>
                                            <div class="option">
                                                <p>Cash on delivery:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>Tracking:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>International courier
                                                    facility:</p>
                                                <p>No</p>
                                            </div>
                                            <div class="option">
                                                <p>Domestic courier
                                                    facility:</p>
                                                <p>Yes</p>
                                            </div>
                                        </div>
                                        <div class="link">
                                            <a
                                                href="#"
                                                target="_blank"
                                                class="linkBtn link-click">
                                                Let's start
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24">
                                                    <path
                                                        d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="cardss">
                                        <div class="image__wrapper">
                                            <div class="card__shadow--1"></div>
                                            <img class="card-img-top"
                                                src="{{url('/')}}/assets/web/assets/images/c4.png"
                                                alt="Angebote - Verkaufsautomaten">
                                        </div>
                                        <div class="card-body pb-4 pt-3">
                                            <p class="card-text">
                                                Shadowfax offers pickups,
                                                same-day delivery, and assured
                                                next-day deliveries for
                                                intercity. On integrating with
                                                Twinnship, you also get return
                                                order pickups and reverse
                                                shipping.
                                            </p>
                                        </div>
                                        <div class="card-content">
                                            <div class="option">
                                                <p>Serviceability:</p>
                                                <p>12000 pin codes</p>
                                            </div>
                                            <div class="option">
                                                <p>Cash on delivery:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>Tracking:</p>
                                                <p>Yes</p>
                                            </div>
                                            <div class="option">
                                                <p>International courier
                                                    facility:</p>
                                                <p>No</p>
                                            </div>
                                            <div class="option">
                                                <p>Domestic courier
                                                    facility:</p>
                                                <p>Yes</p>
                                            </div>
                                        </div>
                                        <div class="link">
                                            <a
                                                href="#"
                                                target="_blank"
                                                class="linkBtn link-click">
                                                Let's start
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24">
                                                    <path
                                                        d="M7 7h8.586L5.293 17.293l1.414 1.414L17 8.414V17h2V5H7v2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section class>
                <div class="container-fluid">
                    <div class="container">
                        <h3 class="Courier-h3">Our Courier Partner</h3>
                        <div class="container py-5">
                            <div class=" slider">

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                   <img src="{{url('/')}}/assets/web/assets/images/partners-1.png" alt="" srcset="">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <img src="{{url('/')}}/assets/web/assets/images/partners-2.png" alt="" srcset="">
                                 </div>
                                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <img src="{{url('/')}}/assets/web/assets/images/partners-3.png" alt="" srcset="">
                                 </div>
                                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <img src="{{url('/')}}/assets/web/assets/images/partners-4.png" alt="" srcset="">
                                 </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->

            <section class="contatUsLeftAndRightContact ">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="contentWrapperLeftRight">
                                <div class="contentWrapper">
                                    <img
                                        src="{{url('/')}}/assets/web/assets/images/request_demo.jpg"
                                        class="img-fluid" alt>
                                </div>
                                <div>
                                    <h1 class="pbmit-title">Top-notch Customer
                                        Experience</h1>
                                    <p class="text-3"
                                        style="color: #000000;">Go beyond for
                                        your customers to boost customer
                                        loyalty. Twinnship transforms ordinary
                                        deliveries to help you build lifelong
                                        connections with your customers.</p>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <span class="icon">
                                                    <img
                                                        src="{{url('/')}}/assets/web/assets/images/r2.svg"
                                                        alt="Icon">
                                                </span>
                                                <div class="text-content">
                                                    <h2>Real Time Status
                                                        Updates</h2>
                                                    <p>The real-time shipment
                                                        status updates on the
                                                        twinnship page.</p>
                                                </div>
                                            </div>
                                            <div class="info-container">
                                                <span class="icon">
                                                    <img
                                                        src="{{url('/')}}/assets/web/assets/images/r1.svg"
                                                        alt="Icon">
                                                </span>
                                                <div class="text-content">
                                                    <h2>Unified Tracking
                                                        Page</h2>
                                                    <p>Allow customers to track
                                                        orders on your own
                                                        website instead of a
                                                        carrier website..</p>
                                                </div>
                                            </div>
                                            <div class="info-container">
                                                <span class="icon">
                                                    <img
                                                        src="{{url('/')}}/assets/web/assets/images/r3.svg"
                                                        alt="Icon">
                                                </span>
                                                <div class="text-content">
                                                    <h2>Returns & Exchange</h2>
                                                    <p>Manage your returns &
                                                        exchanges effortlessly
                                                        starting right from your
                                                        website..</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="requestDemoSection">
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <div class="requestDemoWrapper">
                                <h2 class="requestDemoTitle">Request a Demo</h2>
                                <form class="requestDemoForm" action="{{route('portal.submit-contact')}}" method="post" >
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group form-group-half">
                                            <label for="first_name">Full Name</label>
                                            <input type="text" id="first_name"
                                                name="first_name" required>
                                        </div>
                                        <div class="form-group form-group-half">
                                            <label for="email">Email
                                                Address</label>
                                            <input type="email" id="email"
                                                name="email" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group form-group-half">
                                            <label for="mobile">Phone
                                                Number</label>
                                            <input type="tel" id="mobile"
                                                name="mobile" required>
                                        </div>
                                        <div class="form-group form-group-half">
                                            <label for="company_name">Company
                                                Name</label>
                                            <input type="text" id="company_name"
                                                name="company_name" required>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="submitBtn">Submit
                                        Request</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-6">
                            <img src="{{url('/')}}/assets/web/assets/images/r4.jpg" alt srcset>
                        </div>
                    </div>
                </div>
            </section>

            <section class="twinnshipFeatures">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="featuresWrapper">
                                <h2 class="featuresTitle">The Simplest Way to
                                    Ship Online Orders</h2>
                                <p class="featuresDescription">
                                    With Twinnship, you can elevate your brand's
                                    shipping experience, ensuring seamless
                                    deliveries and happy customers.
                                </p>
                                <div class="featuresGrid">
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f1.png"
                                            alt="Door to Door"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Door to Door Pickup and
                                                Delivery</h3>
                                            <p>We provide door-to-door pickup
                                                and delivery facilities for your
                                                convenience.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f2.png"
                                            alt="Bulk Shipping"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Bulk Order Shipping</h3>
                                            <p>Efficiently handle large volumes
                                                of orders with our bulk shipping
                                                services.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f3.png"
                                            alt="eCommerce Fulfillment"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>eCommerce Fulfillment</h3>
                                            <p>Streamline your eCommerce
                                                operations with our
                                                comprehensive fulfillment
                                                solutions.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f4.png"
                                            alt="Real-time Status Updates"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Real-time Status Updates</h3>
                                            <p>Stay informed with real-time
                                                updates on your shipments.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f5.png"
                                            alt="Customer Support"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Dedicated Customer Support</h3>
                                            <p>Receive support through chat,
                                                call, and email.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f6.jpeg"
                                            alt="Reduce RTO"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Reduce RTO</h3>
                                            <p>Minimize returns to origin with
                                                our efficient processes.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f7.png"
                                            alt="COD Facility"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>COD Facility with Weekly
                                                Clearance</h3>
                                            <p>Offer cash on delivery with
                                                regular weekly clearances.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f8.png"
                                            alt="Returns Management"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Automated Return Order
                                                Management</h3>
                                            <p>Efficiently manage returns with
                                                automated systems.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f9.png"
                                            alt="Priority Support"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Priority Support on Weight
                                                Dispute/RTO/NDR</h3>
                                            <p>Get priority support for
                                                disputes, RTO, and non-delivery
                                                reports.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f10.png"
                                            alt="Account Management"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Dedicated Account Management
                                                Support</h3>
                                            <p>Enjoy personalized support with
                                                dedicated account
                                                management.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f11.png"
                                            alt="Courier Network"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>Ship with India's Top Courier
                                                Network</h3>
                                            <p>Leverage the top courier network
                                                in India for reliable
                                                shipping.</p>
                                        </div>
                                    </div>
                                    <div class="featureItem">
                                        <img src="{{url('/')}}/assets/web/assets/images/f12.png"
                                            alt="Pin Code Service"
                                            class="featureIcon">
                                        <div class="featureContent">
                                            <h3>25,000++ Pin Code Service
                                                Availability</h3>
                                            <p>Benefit from our extensive pin
                                                code service coverage.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
    <script>
            $(document).ready(function(){
                $('#sliderData').slick({
                    dots: true,  
                    infinite: true,  
                    speed: 300,  
                    slidesToShow: 3,  
                    slidesToScroll: 1,  
                    autoplay: true,  
                    autoplaySpeed: 2000,  
                    arrows: false,  
                    pauseOnHover: false,  
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });
    </script>
</div>

</body>

</html>
