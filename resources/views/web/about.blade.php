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
                <h2>About Us</h2>
            </div>
        </div>
        <div class="lines">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </section>
    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col-md-12 aos-init aos-animate mb-3" data-aos="fade-left" data-aos-duration="500">
                    <div class="section-title text-left">
                        <h4 class="text-muted">{{$config->title}}</h4>
                        <h2><span>About</span> Us</h2>
                    </div>
                    <p>{{$config->about}}</p>
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
