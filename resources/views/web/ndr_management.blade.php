<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <title>NDR Management</title>
        @include('web.pages.styles')
    </head>
    <body>
    @include('web.pages.header')

        <section class="hero-section page-title-area">
            <div class="container">
                <div class="page-title-content">
                    <h2>Oversee Undelivered Orders With Ease Our automated NDR management will ease undelivery</h2>
                </div>
            </div>
            <div class="lines">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </section>

        <section class="info-section section-padding">
            <div class="container">
                <div class="section-title">
                    <h2>Less processing time for undelivered orders</h2>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6 my-5">
                                <div class="info-box style-2 gray-bg">
                                    <div class="img-box h-auto pull-up">
                                        <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                                    </div>
                                    <h3 class="info-title">Centralized handling of undelivered orders</h3>
                                    <p>Allocate an activity to each order within a few clicks utilizing a multifunctional and appropriately isolated NDR dashboard.</p>
                                </div>
                            </div>
                            <div class="col-md-6 my-5">
                                <div class="info-box style-2 gray-bg">
                                    <div class="img-box h-auto pull-up">
                                         <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                                    </div>
                                    <h3 class="info-title">Instant action by courier partners</h3>
                                    <p>Get an undelivered order straightforwardly in your panel minutes after the courier partner records un-delivery.  24 hours processing time and time consuming excel sheets</p>
                                </div>
                            </div>
                            <div class="col-md-6 my-5">
                                <div class="info-box style-2 gray-bg">
                                    <div class="img-box h-auto pull-up">
                                         <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                                    </div>
                                    <h3 class="info-title">Contact Buyers Progressively</h3>
                                    <p>Reduce NDR processing time by 12 hours with an automated panel. Contact buyers progressively with SMS, Email, and calls to straightforwardly record their delivery preference</p>
                                </div>
                            </div>
                            <div class="col-md-6 my-5">
                                <div class="info-box style-2 gray-bg">
                                    <div class="img-box h-auto pull-up">
                                         <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                                    </div>
                                    <h3 class="info-title">Reduce RTO with decreased NDR</h3>
                                    <p>Utilize an automated work process, make a move for undelivered orders in real time, and decrease RTO up to 10%!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-dark">
            <div class="container text-center">
                <div class="section-title" data-aos="fade-up" data-aos-duration="450">
                    <h2 class="text-light mb-4">Get started for FREE</h2>
                    <h5 class="text-secondary">No Fees. No Minimun Sign up Period. No credit card Required</h5>
                </div>
                <a href="{{route('seller.register')}}" class="btn btn-light mx-auto">Create an Account</a>
            </div>
        </section>

@include('web.pages.footer')

@include('web.pages.scripts')

<script>
    $('.hero-carousel').owlCarousel({
        loop:true,
        margin:0,
        // nav:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
</script>
<script>
    $('.ease-carousel').owlCarousel({
        loop:true,
        margin:0,
        dots:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
</script>
<script>
    AOS.init(
        { disable: 'mobile' }
    );
</script>

</body>
</html>
