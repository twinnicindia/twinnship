<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <title>Home || {{$config->title}}</title>
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
                        <h1 style="color: rgb(255, 238, 0);"> Efficiency and Reliability</h1>
                        <p style="color: rgb(0, 0, 0);">Partner with Us for Tailored Logistics Solutions That Drive
                            Your Success </p>
                    </div>
                </div>
            </div>
            <div class="floatImageLeft">
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/person.png" alt=""></span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/men-women.svg" alt=""></span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/contact.png" alt=""></span>
            </div>
            <div class="floatImageRight">
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/shipping.png" alt=""> </span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/ai.svg" alt=""></span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/person-1.png" alt=""> </span>

            </div>
        </div>
    </section>

    @include('portal.pages.brand')

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

    <section class="contatUsLeftAndRightContact ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="contentWrapperLeftRight">
                        <div class="contentWrapper">
                            <div class="about-one_imgbox">
                                <div class="about-one_img1">
                                    <img src="{{url('/')}}/assets/web/assets/images/about-us-01.png" class="img-fluid" alt="">
                                </div>
                                <div class="about-one_img2">
                                    <img src="{{url('/')}}/assets/web/assets/images/about-us-02.png" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="pbmit-subtitle">About Us</p>
                            <h1 class="pbmit-title">Shipments delivered time with no hassle.</h1>
                            <p class="text-3" style="color: #000000;">Our team discussed every single detail to make
                                sure is the most versatile and unique theme created so far.No coding skills required
                                to create unique sites.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-borderless">
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Monthly Checkups</span>
                                        </li>
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Caring Medical Team</span>
                                        </li>
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Proactive and Fast Results</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-borderless">
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Monthly Checkups</span>
                                        </li>
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Caring Medical Team</span>
                                        </li>
                                        <li class="list-group-item">
                                                <span class="pbmit-icon-list-icon">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                            <span class="pbmit-icon-list-text">Proactive and Fast Results</span>
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

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
</div>

</body>

</html>
