<!doctype html>
<html lang="en">

<head>
    <title>Recommendation Engine | {{$config->title}}</title>
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
                <h2>Recommendation Engine</h2>
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
            <div class="row mb-4">
            <div class="col-md-12 aos-init aos-animate mb-3" data-aos="fade-left" data-aos-duration="500">
                    <div class="section-title text-left">
                        <h2><span>Recommendation</span> Engine</h2>
                    </div>
                </div>
                <div class="col-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        @foreach ($recon_engine as $data)
                            <a class="nav-link {{$loop->iteration==1 ? 'active' : ''}}" id="v-title-tab_{{$data->id}}" data-toggle="pill" href="#v-description-tab_{{$data->id}}" role="tab" aria-controls="v-description-tab_{{$data->id}}" aria-selected="true">{{$data->title}}</a>
                        @endforeach
                    </div>
                </div>
                <div class="col-9">
                    <div class="tab-content" id="v-pills-tabContent">
                    @foreach ($recon_engine as $data)
                        <div class="tab-pane fade show {{$loop->iteration==1 ? 'active' : ''}}" id="v-description-tab_{{$data->id}}" role="tabpanel" aria-labelledby="v-description-tab_{{$data->id}}">{!!$data->description!!}</div>
                    @endforeach
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
            loop: true,
            margin: 0,
            autoplay: true,
            autoplayTimeout: 5000,
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
        });
        $('.ease-carousel').owlCarousel({
            loop: true,
            margin: 0,
            autoplay: true,
            dots: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
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
        });
    </script>
    <script>
        AOS.init({
            disable: 'mobile'
        });
    </script>

</body>

</html>