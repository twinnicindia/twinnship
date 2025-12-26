<!doctype html>
<html lang="en">
<head>
    <title>About Us | {{$config->title}}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('web.pages.styles')
</head>
<body>

@include('web.pages.header')

    <section class="hero-section page-title-area">
        <div class="container">
            <div class="page-title-content">
                <h2>Terms & Conditions</h2>
            </div>
        </div>
        <div class="lines">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </section>
    <section class="section-padding mb-0">
        <div class="container">
            <div class="row align-items-center mb-1">
                <div class="col-md-12 aos-init aos-animate mb-3" data-aos="fade-left" data-aos-duration="500">
                    <div class="section-title text-left">
                        <h2>Terms & Conditions</h2>
                    </div>
                    <ul>
                        <li>You may cancel your account at anytime by emailing support@Twinnship.in</li>
                        <li>Once your account is cancelled all of your Content will be immediately deleted <a href="https://www.Twinnship.in/">from the Service</a>. Since deletion of all data is final please be sure that you do in fact want to cancel your account before doing so.</li>
                        <li>If you cancel the Service in the middle of the month, you will receive one final invoice via email. Once that invoice has been paid you will not be charged again.</li>
                        <li>We reserve the right to modify or terminate the Twinnship service for any reason, without notice at any time.</li>
                        <li>Fraud: Without limiting any other remedies, Twinnship&nbsp;may suspend or terminate your account if we suspect that you (by conviction, settlement, insurance or escrow investigation, or otherwise) have engaged in fraudulent activity in connection with the Site.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <section class="section-padding newslatter" data-aos="flip-left">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="section-title">
                        <h2><span>{{$config->subscribe_title}}</span></h2>
                    </div>
                    <form action="{{route('web.newsletter')}}" class="newslatter-form" method="post">
                    @csrf
                        <div class="form-group input-group">
                            <input type="email" class="form-control" name="email" placeholder="Enter your email address">
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('web.pages.footer')

    @include('web.pages.scripts')

    <script>
        $('.hero-carousel').owlCarousel({
            loop:true,
            margin:0,
            autoplay:true,
            autoplayTimeout:5000,
            dots : true,
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
        });
        $('.ease-carousel').owlCarousel({
            loop:true,
            margin:0,
            autoplay:true,
            dots : true,
            autoplayTimeout:3000,
            autoplayHoverPause:true,
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
        });
    </script>
    <script>
        AOS.init(
            { disable: 'mobile' }
        );
    </script>

</body>
</html>
