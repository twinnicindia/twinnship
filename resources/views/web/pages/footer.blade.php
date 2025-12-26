<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="row">
                <div class="col-md-4" data-aos="fade-right" data-aos-duration="500">
                    <div class="logo-box">
                        <a class="navbar-brand" href="#"><img src="{{asset($config->logo)}}" height="30" alt=""></a>
                    </div>
                    <p>{{$config->about}}</p>
                    <ul class="social-icon">
                        @foreach($links as $l)
                            <li><a target="_blank" href="{{$l->link}}"><i class="fab {{$l->icon}}"></i></a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-8">
                    <div class="row pl-md-5 pt-5 pt-md-0">
                        <div class="col-md-4 col-6 mb-3 mb-md-0" data-aos="fade-right" data-aos-duration="600">
                            <div class="footer-title">
                                <h3>Our Services</h3>
                            </div>
                            <ul class="footer-menu">
                                <li><a href="#">Air Express</a></li>
                                <li><a href="#">Surface Express</a></li>
                                <li><a href="#">Surface Bulk Mode</a></li>
                                <li style="display:none;"><a href="#">Dangerous Goods</a></li>
                                <li style="display:none;"><a href="#">Reverse Logistics</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-6 mb-3 mb-md-0" data-aos="fade-right" data-aos-duration="700">
                            <div class="footer-title">
                                <h3>Information</h3>
                            </div>
                            <ul class="footer-menu">
                                <li><a href="{{route('web.about')}}">About us</a></li>
                                <li><a href="{{route('web.privacy')}}">Privacy</a></li>
                                <li><a href="{{route('web.terms')}}">Terms and Conditions</a></li>
                                <a href="{{route('seller.login')}}" class="btn btn-outline-light">Start Shipping</a>
                            </ul>
                        </div>
                        <div class="col-md-4 col-12" data-aos="fade-right" data-aos-duration="800">
                            <div class="footer-title">
                                <h3>Contact Us</h3>
                            </div>
                            <ul class="footer-menu">
                                <li><a href="#">{{$config->title}}</a></li>
                                <li><a href="#">{{$config->email}}</a></li>
                                <li><a href="#">{{$config->working_hour}}</a></li>
                                <li><a href="#">+91 {{$config->mobile}}</a></li>
                                <li><p style="color:#fff;">{{$config->address}}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright Ⓒ {{$config->copyright}}</p>
    </div>
</footer>
