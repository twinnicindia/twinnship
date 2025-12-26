<footer class="footer-section">
    <div class="container">
        <div class=" mt-5 footer-content pt-5 pb-5">
            <div class="row">
                <div class="col-xl-3 col-lg-3 mb-50">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a href="{{url('/')}}"><img src="{{url('/')}}/assets/web/assets/images/twinnshi-new.png" style="width: 200px;height: 89px;" class="img-fluid"
                                             alt="logo"></a>
                        </div>
                        <div class="footer-text">
                            <p>{{$config->meta_description}}</p>
                        </div>
                        <div class="subscribe-form">
                            <form action="{{route('web.email_submit')}}" method="post">
                                @csrf
                                <input type="text" name="email" placeholder="Email Address" required>
                                <button type="submit" style="width: 70px;"><i class="fa fa-telegram"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h3>Useful Links</h3>
                        </div>
                        <ul>
                            <li><a href="{{url('/')}}">Home</a></li>
                            <li><a href="{{route('web.web-pricing')}}">Pricing</a></li>
                            <li><a href="{{route('web.tracking')}}">Tracking</a></li>
                            <li><a href="{{route('web.contact-us')}}">Contact us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h3>Product</h3>
                        </div>
                        <ul>
                            <li><a href="{{route('web.terms-of-services')}}">Terms & Condition</a></li>
                            <li><a href="{{route('web.privacy')}}">Privacy Policy</a></li>
                            <li><a href="{{route('web.cancel')}}">Cancellation & Return</a></li>
                            <li><a href="{{route('web.disclaimer')}}">Disclaimer</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-50">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h3>Contact Info </h3>
                        </div>
                        <ul>
                            <li><a href="#">{{$config->email}}</a></li>
                            <h3 style="color: #fff;">Office Address:</h3>
                            <li><a href="#">{!! nl2br($config->address) !!}</a></li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-area">
        <div class="container">
            <div class="row">

            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="mt-5 footer-content pt-5 pb-5">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 text-center text-md-start sm-margin-20px-bottom">
                        <a href="{{url('/')}}">
                            <img width="1" height="1" src="{{url('/')}}/assets/web/assets/images/twinnship.png"
                                 class="attachment-full size-full" alt="" decoding="async"
                                 style="width: 100px;" />
                        </a>
                </div>

                <div class="col-12 col-md-6 text-center last-paragraph-no-margin sm-margin-20px-bottom">
                    <p class="text-white">© 2024  TWINNIC INDIA PRIVATE LIMITED. All rights reserved.</p>
                </div>

                <div class="col-12 col-md-3 text-center text-md-end elements-social social-icon-style-09">
                    <ul class="large-icon light mr-50 social-icons-list">
                        <li><a class="facebook" href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
                        <li><a class="linkedin" href="https://www.linkedin.com/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                        <li><a class="instagram" href="https://www.instagram.com/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
