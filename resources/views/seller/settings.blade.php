<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')
    <title>Settings | {{$config->title}}</title>
</head>

<body>
    @include('seller.pages.header')
    @include('seller.pages.side_links')


    <div class="container-fluid">
        <div class="main-content d-flex flex-column">


            <div class="row justify-content-center">
                <div class="col-xxl-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="card-body mt-3">
                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-sm-4">
                                    <div class="card addons-box h-100 custome-bg p-4">
                                        <h4 class="fw-600">Settings</h4>
                                        <p class="fw-400">Take your shipping experience a notch higher with us by regulating your panel settings as per your convenience and specific business requirements. From importing orders to managing labels and all other account
                                            settings, get everything at the tap of your finger for an uninterrupted experience.</p>
                                        <div class="addons-img-box text-center pt-4 mt-1 mt-sm-4">
                                            <img src="https://dqtccm62inm81.cloudfront.net/assets/img/settings/setting.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-8 col-sm-8">
                                    <div class="addons-boxes mt-4 mt-sm-0">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.kyc')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/company.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Company Profile</h5>
                                                                <p class="text-muted m-0 fw-600">Your company profile</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.warehouses')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/warehouse.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Warehouse</h5>
                                                                <p class="text-muted m-0 fw-600">Manage your pickup locations</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.employees')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/employe.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Employees</h5>
                                                                <p class="text-muted m-0 fw-600">Allow access to team members</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.customiseLabel')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/invo.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Label Settings</h5>
                                                                <p class="text-muted m-0 fw-600">Set your shipping label format</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.customiseLabel')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/invo.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Label Settings</h5>
                                                                <p class="text-muted m-0 fw-600">Set your shipping label format</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div> -->

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.seller_api_key')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/api.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">API</h5>
                                                                <p class="text-muted m-0 fw-600">Programmatically access TwinShip data</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.settings_partner')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/courier.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Courier Preferences</h5>
                                                                <p class="text-muted m-0 fw-600">Your Courier Preferences</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{ route('seller.sku') }}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/sku.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Sku</h5>
                                                                <p class="text-muted m-0 fw-600">Manage SKU</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.rules')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/rules.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Rules</h5>
                                                                <p class="text-muted m-0 fw-600">Set your Rules</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.rate_calculator')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/rate.png" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Rate Calculator</h5>
                                                                <p class="text-muted m-0 fw-600">Rate Calculator</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                                <div class="card box">
                                                    <a href="{{route('seller.shipping_rates')}}">
                                                        <div class="card-body my-2">
                                                            <div class="pb-2">
                                                                <button type="button" class="btn mb-3">
                                                                    <img src="{{asset('assets/sellers/')}}/images/rate_card.jpg" class="icon">
                                                                </button>
                                                            </div>
                                                            <div>
                                                                <h5 class="fw-600 text-dark">Rate Card</h5>
                                                                <p class="text-muted m-0 fw-600">Rate Card</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('seller.pages.scripts')
</body>

</html>
