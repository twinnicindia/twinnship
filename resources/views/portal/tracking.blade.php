<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <title>Tracking || {{$config->title}}</title>
    @include('portal.pages.styles')
</head>
@php
    $orderStatus = array(
    "pending" => "Pending",
    "shipped" => "Shipped",
    "manifested" => "Manifested",
    "pickup_scheduled" => "Pickup Scheduled",
    "picked_up" => "Pickup Up",
    "cancelled" => "Cancelled",
    "in_transit" => "In Transit",
    "out_for_delivery" => "Out for Delivery",
    "rto_out_for_delivery" => "RTO Out for Delivery",
    "rto_initated" => "RTO Initiated",
    "rto_initiated" => "RTO Initiated",
    "rto_delivered" => "RTO Delivered",
    "rto_in_transit" => "RTO In Transit",
    "delivered" => "Delivered",
    "ndr" => "NDR",
    "lost" => "Lost",
    "damaged" => "Damaged"
    );

    $partnerNames = [
        'amazon_swa' => 'AmazonSwa',
        'amazon_swa_10kg' => 'AmazonSwa',
        'amazon_swa_1kg' => 'AmazonSwa',
        'amazon_swa_3kg' => 'AmazonSwa',
        'amazon_swa_5kg' => 'AmazonSwa',
        'bluedart' => 'BlueDart',
        'bluedart_surface' => 'BlueDart',
        'delhivery_surface' => 'Delhivery',
        'delhivery_surface_10kg' => 'Delhivery',
        'delhivery_surface_20kg' => 'Delhivery',
        'delhivery_surface_2kg' => 'Delhivery',
        'delhivery_surface_5kg' => 'Delhivery',
        'delhivery_lite' => 'Delhivery',
        'dtdc_10kg' => 'DTDC',
        'dtdc_6kg' => 'DTDC',
        'dtdc_1kg' => 'DTDC',
        'dtdc_2kg' => 'DTDC',
        'dtdc_3kg' => 'DTDC',
        'dtdc_5kg' => 'DTDC',
        'dtdc_express' => 'DTDC',
        'dtdc_surface' => 'DTDC',
        'ecom_express' => 'Ecom Express',
        'ecom_express_rvp' => 'Ecom Express',
        'ecom_express_3kg' => 'Ecom Express',
        'ecom_express_3kg_rvp' => 'Ecom Express',
        'fedex' => 'FedEx',
        'shadow_fax' => 'Shadowfax',
        'smartr' => 'Smartrlogistics',
        'udaan' => 'Udaan',
        'udaan_10kg' => 'Udaan',
        'udaan_1kg' => 'Udaan',
        'udaan_2kg' => 'Udaan',
        'udaan_3kg' => 'Udaan',
        'wow_express' => 'WowExpress',
        'bombax' => 'Bombax',
        'shree_maruti' => 'Shree Maruti Courier',
        'shree_maruti_ecom' => 'Shree Maruti Courier',
        'smc_new' => 'Shree Maruti Courier',
        'shree_maruti_ecom_1kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_3kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_5kg' => 'Shree Maruti Courier',
        'shree_maruti_ecom_10kg' => 'Shree Maruti Courier',
        'xpressbees_sfc' => 'Xpressbees',
        'xpressbees_surface' => 'Xpressbees',
        'xpressbees_surface_10kg' => 'Xpressbees',
        'xpressbees_surface_1kg' => 'Xpressbees',
        'xpressbees_surface_3kg' => 'Xpressbees',
        'xpressbees_surface_5kg' => 'Xpressbees',
        'tpc_surface' => 'The Professional Couriers',
        'tpc_1kg' => 'The Professional Couriers',
        'pick_del' => 'PickNDel',
    ];

@endphp

<body data-rsssl=1>

<div id="page" class="site">
    @include('portal.pages.header')

    <section class="mainBannerContactUs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="textCenter contentWrapperBanner">
                        <h3> Track your orders </h3>
                        <h1>Track your orders easily</h1>
                        <p> Try Twinnship with a free account and Upgrade as you grow </p>
                    </div>
                </div>
            </div>
            <div class="floatImageLeft">
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/person.png" alt=""></span>
                <span class="iconsContactUs"><img src="{{url('/')}}/assets/web/assets/images/contact.png" alt=""></span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/icon3.png" alt=""> </span>

            </div>
            <div class="floatImageRight">
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/shipping.png" alt=""> </span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/icon3.png" alt=""> </span>
                <span class="iconsContactUs"> <img src="{{url('/')}}/assets/web/assets/images/person-1.png" alt=""> </span>
            </div>
        </div>
    </section>

    @include('portal.pages.brand')

    <section class=" contactUsCTASection">
        <div class="container">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <h1 class="headingText">
                            Track your <br>
                            <span class="blueOrangeGradient"> orders easily </span>
                        </h1>
                        <img src="{{url('/')}}/assets/web/assets/images/traking.png" alt="" srcset="">
                    </div>
                    <div class="card-row  col-md-6 col-sm-12">
                        <div class="row">
                            <div class="track col-12">
                                <h3 class="text-1">Tracking Your Order:</h3>
                                <form action="{{route('portal.single-order-tracking')}}" method="GET">
                                    <input type="text" id="awb_number" name="awb_number" value="{{$order->awb_number ?? ''}}" placeholder="Enter your Airway Bill Number(AWB)">
                                    <button class="btn btn-success" id="track-btn" type="submit">
                                        Track now
                                    </button>
                                </form>

                                <h5 class="text-1"> Can’t Find Your Order Details?</h5>
                                <span> We sent your AWB tracking number to you via Email &
                                    <p>SMS upon order confirmation.</p>
                                </span>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="container" id="trackingDetailsContainer" style="display: ;">
        @if(!empty($order))
        <div class="twinnship-track-details">
            <div class="track-head container">
                <div class="col-md-10 offset-1">
                    <div class="row row-cols-1 row-cols-lg-4 row-cols-sm-2 track-data">
                        <div class="col text-center process-step-style-01 last-paragraph-no-margin md-margin-50px-bottom wow animate__fadeIn" >
                            <span class="alt-font d-block font-weight-500 text-extra-dark-gray margin-10px-bottom">Track ID</span>
                            <p class="w-85 d-inline-block lg-w-100 md-w-70 sm-w-90">{{$order->awb_number}}</p>
                        </div>
                        <div class="col text-center process-step-style-01 last-paragraph-no-margin md-margin-50px-bottom wow animate__fadeIn" data-wow-delay="0.2s" >
                            <span class="alt-font d-block font-weight-500 text-extra-dark-gray margin-10px-bottom">Order ID</span>
                            <p class="w-85 d-inline-block lg-w-100 md-w-70 sm-w-90">{{$order->customer_order_number}}</p>
                        </div>
                
                        <div class="col text-center process-step-style-01 last-paragraph-no-margin xs-margin-50px-bottom wow animate__fadeIn" data-wow-delay="0.4s" >
                            <span class="alt-font d-block font-weight-500 text-extra-dark-gray margin-10px-bottom">Order Place</span>
                            <p class="w-85 d-inline-block lg-w-100 md-w-70 sm-w-90">{{date('d M Y',strtotime($order->awb_assigned_date))}}</p>
                        </div>
                        <div class="col text-center process-step-style-01 last-paragraph-no-margin wow animate__fadeIn" data-wow-delay="0.6s" >
                            <?php
                                if($order->status == 'delivered' && $order->rto_status == 'y')
                                    $order->status = 'rto_delivered';
                                elseif($order->status == 'in_transit' && $order->rto_status == 'y')
                                    $order->status = 'rto_in_transit';
                                elseif($order->status == 'out_for_delivery' && $order->rto_status == 'y')
                                    $order->status = 'rto_out_for_delivery';
                            ?>
                            <span class="alt-font d-block font-weight-500 text-extra-dark-gray margin-10px-bottom">Status : {{$orderStatus[$order->status] ?? "manifested"}}</span>
                            <p class="w-85 d-inline-block lg-w-100 md-w-70 sm-w-90">{{ ($order->status != 'delivered' && $order->status != 'rto_delivered' ) ? "" : (empty($order->delivered_date) ? "" : date('d M Y', strtotime($order->delivered_date))) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="track-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-7 mb-4 mb-lg-0 pt-5 " id="trackingOrder">
                            <ul class="track-list">
                            @forelse($order_tracking as $t)
                                    <li>
                                        <div class="text-black text-gray font-semibold mb-1">{{$t->status}}</div>
                                        <span><b>Activity : </b>
                                                <activity>{{$t->status}} - {{$t->status_code}}</activity>
                                            </span>
                                        <span>
                                        <br>
                                        <span>
                                        <b>Status Description : </b>
                                            <activity>{{$t->status_description == "Status Changed by CRM" ? " ":$t->status_description}}</activity>
                                        </span>
                                        <br>
                                        <b>Location : </b>
                                                <activity>{{$t->location == "Status Changed by CRM" ? " ": $t->location}}</activity>
                                        </span>
                                        <!-- Delivered or RTO delivered -->
                                        <!-- undelivered -->
                                        <?php
                                        $statusDateTime = date('Y-m-d H:i:s',strtotime($t->updated_date));
                                        if(str_contains($statusDateTime,'1970')){
                                            $date = date('d M', strtotime($t->created_at));
                                            $time = date('h:i:A', strtotime($t->created_at));
                                        }else{
                                            $date = date('d M', strtotime($t->updated_date));
                                            $time = date('h:i:A', strtotime($t->updated_date));
                                        }
                                        ?>
                                        <div class="date_info_wrap">
                                            <span class="date">{{$date}}</span>
                                            <span class="time">{{$time}}</span>
                                        </div>
                                        <i class="circle_icon text-green"></i>
                                    </li>
                                @empty
                                    <li>
                                        <div class="text-black text-gray font-semibold mb-1">No Tracking Details Found</div>
                                    </li>
                                @endforelse

                            </ul>
                        </div>
                        @php
                            $originalDate = $order->awb_assigned_date;
                            $expectedDate = $order->expected_delivery_date;
                            if($expectedDate < date('Y-m-d H:i:s'))
                                $expectedDate =date('Y-m-d H:i:s',strtotime('+28 hours'));
                        @endphp
                            <div class="col-12 col-md-12 col-lg-5">
                                <div class="bg-light card-track card">
                                    @if($order->status == 'delivered' && $order->rto_status == 'y')
                                        @php($order->status='rto_delivered')
                                    @elseif($order->status == 'in_transit' && $order->rto_status == 'y')
                                        @php($order->status='rto_in_transit')
                                    @elseif($order->status == 'out_for_delivery' && $order->rto_status == 'y')
                                        @php($order->status = 'rto_out_for_delivery');
                                    @endif
                                    <span class="text-base text-gray mb-1">Status : {{$orderStatus[$order->status] ?? 'In Transit'}}</span>
                                        @if($order->manifest_sent == 'y')
                                            @if(!empty($order->expected_delivery_date))
                                                <p class="text-base text-gray opacity-50 m-0">Estimate Delivery Date : {{date('d M Y',strtotime($order->expected_delivery_date))}}</p>
                                            @else
                                                <p class="text-base text-gray opacity-50 m-0">Estimate Delivery Date : {{date('d M Y',strtotime($order->awb_assigned_date . '+4 days'))}}</p>
                                            @endif
                                        @endif
                                </div>
                                <div class="bg-light card-track card">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img class="float-left courier_logo img-fluid"
                                                src="{{asset($partner->image)}}" style="height: 80px;">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="text-gray mt-4">{{ $partnerNames[$order->courier_partner] ?? $partner->title}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @include('portal.pages.footer')
    @include('portal.pages.scripts')
</div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('awb_track');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const awbInput = document.getElementById('awb');
            const awbValue = awbInput.value.trim();

            if (awbValue === '') {
                alert('Please enter a valid AWB number.');
                return;
            }

            const trackIdElements = document.querySelectorAll('.track-data .process-step-style-01 p');
            console.log('trackIdElements:', trackIdElements); 

            if (trackIdElements.length > 0) {
                trackIdElements[0].textContent = awbValue;
                document.getElementById('trackingDetailsContainer').style.display = 'block';
            } else {
                console.error('Track ID element not found');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#trackOrder').removeClass('active');
        $('#NOrder').addClass('active');
        setTimeout(function () {
            scrollToElement('order-tracking');
        },500);

        $('#NOrder').click(function () {
            $('#trackingOrder').hide();
            $('#ndrOrder').show();
            $('#trackOrder').addClass('active');
            $('#NOrder').removeClass('active');
        });

        $('#trackOrder').click(function () {
            $('#trackingOrder').show();
            $('#ndrOrder').hide();
            $('#NOrder').addClass('active');
            $('#trackOrder').removeClass('active');
        });

    });
    function scrollToElement(elementId) {
        var element = $('#' + elementId);
        var offset = element.offset.top;
        $(document).scrollTop(offset);
    }
</script>

</html>
