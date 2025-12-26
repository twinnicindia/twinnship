<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Postpaid</title>
    @include('web.pages.styles')
</head>

<body>
    @include('web.pages.header')
    <section class="hero-section page-title-area">
        <div class="container">
            <div class="page-title-content text-left">
                <h2>Ship without Hindrance</h2>
                <p class="text-white">Utillize your COD remittance as shipping credits and experience ease</p>
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
            <div class="section-title p-bold">
                <h2>Manual Wallet Recharges are not <br><span>mandatory anymore</span></h2>
                <p>Utilize your COD remittance as shipping credits</p>
            </div>
            <div class="pt-4 pb-5 mb-md-5 w-75 mx-auto" data-aos="fade-up" data-aos-duration="450">
                <img src="{{asset('public/assets/seller/images/postpaid.png')}}" class="img-fluid" alt="">
            </div>
            <div class="row">
                <div class="col-md-4 my-5">
                    <div class="info-box style-2 gray-bg">
                        <div class="img-box h-auto pull-up">
                            <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                        </div>
                        <h3 class="info-title">Transfer Shipping Credits Automatic</h3>
                        <p>Transfer your COD remittance straight to your Twinnship wallet and ship advantageously</p>
                    </div>
                </div>
                <div class="col-md-4 my-5">
                    <div class="info-box style-2 gray-bg">
                        <div class="img-box h-auto pull-up">
                            <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                        </div>
                        <h3 class="info-title">Stabilize Cash Flow</h3>
                        <p>With COD remittance, enjoy undisturbed shipping with a stabilized cash flow</p>
                    </div>
                </div>
                <div class="col-md-4 my-5">
                    <div class="info-box style-2 gray-bg">
                        <div class="img-box h-auto pull-up">
                            <img src="{{asset('public/assets/seller/images/plan-icon.jpg')}}" class="img-fluid" alt="">
                        </div>
                        <h3 class="info-title">Obstacle free shipping</h3>
                        <p>Ship easily with our post paid services, without worrying about manually recharging your Twinnship wallet everytime</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding pt-0">
        <div class="container text-center">
            <div class="section-title" data-aos="fade-up" data-aos-duration="450">
                <h2 class="text-dark mb-4">How postpaid works?</h2>
            </div>
            <a href="#" class="btn btn-primary mx-auto">Start with Twinnship</a>
        </div>
    </section>

    <section class="section-padding bg-dark">
        <div class="container text-center">
            <div class="section-title" data-aos="fade-up" data-aos-duration="450">
                <h2 class="text-light mb-4">Get started for FREE</h2>
                <h5 class="text-secondary">No Fees. No Minimun Sign up Period. No credit card Required</h2>
            </div>
            <a href="#" class="btn btn-light mx-auto">Create an Account</a>
        </div>
    </section>

    @include('web.pages.footer')

    @include('web.pages.scripts')

    <script>
        $('.hero-carousel').owlCarousel({
            loop: true,
            margin: 0,
            // nav:true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    </script>
    <script>
        $('.ease-carousel').owlCarousel({
            loop: true,
            margin: 0,
            dots: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    </script>
    <script>
        AOS.init({
            disable: 'mobile'
        });
    </script>

</body>

</html>
