<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="menuWrapper">
                    <div class="logo">
                        <a href="{{url('/')}}">
                            <img width="1" height="1" src="{{url('/')}}/assets/web/assets/images/twinnship.png"
                                 class="attachment-full size-full" alt="" decoding="async"
                                 style="width: auto;" />
                        </a>
                    </div>
                    <div class="mainMenu">
                        <nav id="site-navigation" class="main-navigation">
                            <div class="menu-main-menu-container">
                                <ul id="main_menu" class="menu">
                                    <li id="menu-item-7" class="menu-item ">
                                        <a href="{{url('/')}}">
                                            <div><span>Home</span></div>
                                        </a>
                                    </li>
                                    <li id="" class="horizontalColumns menu-item ">
                                        <a href="{{route('web.web-pricing')}}">
                                            <div><span>Pricing</span></div>
                                        </a>
                                    </li>
                                    <li id="" class="menu-item  menu-item-object-page ">
                                        <a href="{{route('web.tracking')}}">
                                            <div><span>Tracking</span></div>
                                        </a>
                                    </li>
                                    <li id="" class="horizontalColumns bottomSingleColumn menu-item  ">
                                        <a href="{{route('web.contact-us')}}">
                                            <div><span>Contact Us</span></div>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="appLogins">
                        <ul>
                            <li>
                                <a class="" href="{{route('seller.login')}}" target="">Log
                                    In</a>
                            </li>
                            <li>
                                <a class="button-light-blue registerCta" href="{{route('seller.register')}}" target="">Try for
                                    free</a>
                            </li>
                        </ul>
                    </div>
                    <a class="MobileNavController" href="#">
                        <div>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
