<!doctype html>
<html lang="en">
<head>
    <title>Home Page 1 | {{$config->title}}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('web.pages.styles')
</head>
<body>

@include('web.pages.header')

<section class="hero-section">
    <div class="container">
        <div class="owl-carousel hero-carousel owl-theme" data-aos="fade-up">
            @foreach($slider as $s)
            <div class="item">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="wel-title">
                            <div class="sub-title">Welcome to {{$config->title}}</div>
                            <h3 class="title">{{$s->title}}</h3>
                            <p>{{$s->detail}}</p>
                            <a href="{{route('seller.register')}}" class="btn btn-light">Sign up for Free</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="{{asset($s->image)}}" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<section class="section-padding pb-0">
    <div class="container">
        <div class="section-title">
            <h2>
                <span>{{$config->stats_title}}</span></h2>
        </div>
        <div class="row">
            @foreach($stats as $s)
            <div class="col-sm-6 col-md-3 mb-3" data-aos="fade-up" data-aos-duration="300">
                <div class="card text-center counter-box">
                    <div class="card-body">
                        <div class="h3 theme-primary-color mb-3">{{$s->number}}</div>
                        <div class="h6 mb-0 text-muted">{{$s->title}}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="info-section section-padding gray-bg">
    <div class="section-title">
        <h2>
            <span>{{$config->associates_title}}</span>
        </h2>
    </div>
    <div class="container">
        <div class="row">
            @foreach($feature as $f)
                <div class="col-md-4 mb-3">
                    <div class="info-box">
                        <div class="img-box">
                            <img src="{{asset($f->image)}}" class="img-fluid" alt="">
                        </div>
                        <h3 class="info-title">{{$f->title}}</h3>
                        <p><?= $f->detail ?></p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


<section class="section-padding">
    <div class="section-title">
        <h2><span>{{$config->steps_title}}</span></h2>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            @foreach($steps as $s)
            <div class="col-md col-2 col-sm-3 mb-3" data-aos="fade-up" data-aos-duration="500">
                <div class="info-box-2">
                    <div class="img-box">
                        <img src="{{asset($s->image)}}" alt="" class="">
                    </div>
                    <div class="info-title">{{$s->title}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding container py-4 pb-2 rounded" style="background: var(--secondary);">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-md-4 offset-md-2">
                <div class="section-title aos-init aos-animate mb-0" data-aos="fade-up" data-aos-duration="450">
                    <h2 class="text-light mb-4">Its Easy and Free!!!</h2>
                    <h5 class="text-dark">{{$config->signup_title}}</h5>
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{route('seller.register')}}" class="btn btn-light mx-auto">Create an Account</a>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="section-title">
        <h2><span>{{$config->ease_title}}</span></h2>
    </div>
    <div class="container">
        <div class="owl-carousel ease-carousel">
            @foreach($why as $w)
            <div class="item">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center" data-aos="fade-right" data-aos-duration="500">
                        <img src="{{asset($w->image)}}" alt="" class="w-auto mx-auto">
                    </div>
                    <div class="col-md-6" data-aos="fade-left" data-aos-duration="500">
                        <div class="section-title text-left">
                            <h2><span>{{$w->title}}</span></h2>
                        </div>
                            <?php echo $w->detail ?>
                        <a href="#" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{--<section class="info-section section-padding gray-bg">--}}
{{--    <div class="container">--}}
{{--        <div class="section-title">--}}
{{--            <h2>Why Choose us?</h2>--}}
{{--        </div>--}}
{{--        <div class="row">--}}
{{--            @foreach($why as $w)--}}
{{--            <div class="col-md-4 mb-3 mb-md-4">--}}
{{--                <div class="info-box style-2">--}}
{{--                    <div class="img-box h-auto">--}}
{{--                        <img src="{{asset($w->image)}}" class="img-fluid" alt="">--}}
{{--                    </div>--}}
{{--                    <h3 class="info-title">{{$w->title}}</h3>--}}
{{--                    <p>{{$w->detail}}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}


<?php if($config->logistic_partner=='y') { ?>
<section class="section-padding client-slider gray-bg" data-aos="fade-right"  data-aos-duration="500" data-aos-duration="800">
    <div class="container">
        <div class="section-title" data-aos="fade-up" data-aos-duration="450">
            <h2>
                <span>{{$config->logistics_title}}</span>
            </h2>
        </div>
        <div id="logisticsPartnersSlider" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                    $remain=count($logistics)%6;
                    $page=intval(count($logistics)/6);
                    if($remain>0)
                        $page++;
                ?>
                @for($i=0;$i<$page;$i++)
                <li data-target="#logisticsPartnersSlider" data-slide-to="{{$i}}" class="{{$i==0?"active":""}}"></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                <?php $resCount=0; ?>
                @for($i=0;$i<$page;$i++)
                <?php
                if($i==($page-1))
                    $flag=$remain>0?$remain:6;
                else
                    $flag=6;
                ?>
                <div class="carousel-item {{$i==0?"active":""}}">
                    <div class="row">
                        @for($j=1;$j<=$flag;$j++)
                        <div class="col-4 col-md-2">
                            <div class="client-logo-box">
                                <a target="_blank" href="{{$logistics[$resCount]->link}}">
                                    <img src="{{asset($logistics[$resCount]->image)}}" class="img-fluid" alt="">
                                </a>
                            </div>
                        </div>
                        <?php $resCount++; ?>
                        @endfor
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</section>
<?php } ?>

<?php if($config->channel_partner=='y') { ?>
<section class="section-padding client-slider" data-aos="fade-right" data-aos-duration="800">
    <div class="container">
        <div class="section-title" data-aos="fade-up" data-aos-duration="450">
            <h2>
                <span>{{$config->channel_title}}</span>
            </h2>
        </div>
        <div id="channelPartnersSlider" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                    $remain=count($channel)%6;
                    $page=intval(count($channel)/6);
                    if($remain>0)
                        $page++;
                ?>
                @for($i=0;$i<$page;$i++)
                    <li data-target="#channelPartnersSlider" data-slide-to="{{$i}}" class="{{$i==0?"active":""}}"></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                <?php $resCount=0; ?>
                @for($i=0;$i<$page;$i++)
                    <?php
                        if($i==($page-1))
                            $flag=$remain>0?$remain:6;
                        else
                            $flag=6;
                    ?>
                    <div class="carousel-item {{$i==0?"active":""}}">
                        <div class="row">
                            @for($j=1;$j<=$flag;$j++)
                                <div class="col-4 col-md-2">
                                    <div class="client-logo-box">
                                        <a target="_blank" href="{{$channel[$resCount]->link}}">
                                            <img src="{{asset($channel[$resCount]->image)}}" class="img-fluid" alt="">
                                        </a>
                                    </div>
                                </div>
                                <?php $resCount++; ?>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>
<?php } ?>

<?php if($config->brands=='y') { ?>
<section class="section-padding client-slider gray-bg" data-aos="fade-right" data-aos-duration="800">
    <div class="container">
        <div class="section-title">
            <h2>
                <span>{{$config->brand_title}}</span>
            </h2>
        </div>
        <div id="brandPartnersSlider" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                $remain=count($brands)%6;
                $page=intval(count($brands)/6);
                if($remain>0)
                    $page++;
                ?>
                @for($i=0;$i<$page;$i++)
                    <li data-target="#brandlPartnersSlider" data-slide-to="{{$i}}" class="{{$i==0?"active":""}}"></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                <?php $resCount=0; ?>
                @for($i=0;$i<$page;$i++)
                    <?php
                    if($i==($page-1))
                        $flag=$remain>0?$remain:6;
                    else
                        $flag=6;
                    ?>
                    <div class="carousel-item {{$i==0?"active":""}}">
                        <div class="row">
                            @for($j=1;$j<=$flag;$j++)
                                <div class="col-4 col-md-2">
                                    <div class="client-logo-box">
                                        <a target="_blank" href="{{$brands[$resCount]->link}}">
                                            <img src="{{asset($brands[$resCount]->image)}}" class="img-fluid" alt="">
                                        </a>
                                    </div>
                                </div>
                                <?php $resCount++; ?>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>
<?php } ?>

<?php if($config->press_coverage=='y') { ?>
<section class="section-padding client-slider" data-aos="fade-right" data-aos-duration="800">
    <div class="container">
        <div class="section-title">
            <h2>
                <span>{{$config->press_title}}</span>
            </h2>
        </div>
        <div id="coveragePartnersSlider" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                $remain=count($coverage)%6;
                $page=intval(count($coverage)/6);
                if($remain>0)
                    $page++;
                ?>
                @for($i=0;$i<$page;$i++)
                    <li data-target="#coveragePartnersSlider" data-slide-to="{{$i}}" class="{{$i==0?"active":""}}"></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                <?php $resCount=0; ?>
                @for($i=0;$i<$page;$i++)
                    <?php
                    if($i==($page-1))
                        $flag=$remain>0?$remain:6;
                    else
                        $flag=6;
                    ?>
                    <div class="carousel-item {{$i==0?"active":""}}">
                        <div class="row">
                            @for($j=1;$j<=$flag;$j++)
                                <div class="col-4 col-md-2">
                                    <div class="client-logo-box">
                                        <a target="_blank" href="{{$coverage[$resCount]->link}}">
                                            <img src="{{asset($coverage[$resCount]->image)}}" class="img-fluid" alt="">
                                        </a>
                                    </div>
                                </div>
                                <?php $resCount++; ?>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>
<?php } ?>

<section class="section-padding testimonial gray-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5 text-center" data-aos="zoom-in">
                <img src="{{asset($config->testimonial_image)}}" class="img-fluid" alt="">
            </div>
            <div class="col-md-7">
                <div class="section-title text-left" data-aos="fade-down-left">
                    <h2 class="text-light">Testimonial</h2>
                </div>
                <div id="testimonialSlider" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @php($cnt=0)
                        @foreach($testimonial as $t)
                        <li data-target="#testimonialSlider" data-slide-to="{{$cnt}}" class="{{$cnt++==0?"active":""}}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @php($cnt=0)
                        @foreach($testimonial as $t)
                        <div class="carousel-item {{$cnt++==0?"active":""}}">
                            <div class="testimonial-box">
                                <div class="testimonial-des">{{$t->description}}</div>
                                <div class="author-box">
                                    <div class="author-img">
                                        <img src="{{asset($t->image)}}" alt="" class="img-fluid">
                                    </div>
                                    <div class="author-content">
                                        <div class="name">{{$t->name}}</div>
                                        <div class="position">{{$t->designation}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
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
                        <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
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
