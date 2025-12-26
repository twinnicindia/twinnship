<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <title>Disclaimer || {{$config->title}}</title>
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
                        <h1> Disclaimer</h1>
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

    <section class="contatUsLeftAndRightContact pt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-12 mb-5 pb-3 pb-md-0 mb-md-0">
                   
                </div>
            </div>
        </div>
    </section>
    <section class="contatUsLeftAndRightContact pt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-12 mb-5 pb-3 pb-md-0 mb-md-0">
                    <?= $term->lease_description ?? ""?>
                </div>
            </div>
        </div>
    </section>

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
</div>

</body>

</html>
