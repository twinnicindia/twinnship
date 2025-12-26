<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('seller.pages.styles')
    <title>Tracking Details</title>
</head>

<body>
    @php
    $orderStatus = array(
    "pending" => "Pending",
    "shipped" => "Shipped",
    "manifested" => "Manifested",
    "pickup_scheduled" => "Pickup Scheduled",
    "picked_up" => "Picked Up",
    "cancelled" => "Cancelled",
    "in_transit" => "In Transit",
    "out_for_delivery" => "Out for Delivery",
    "rto_initated" => "RTO Initiated",
    "delivered" => "Delivered",
    "ndr" => "NDR",
    "lost" => "Lost",
    "damaged" => "Damaged"
    );
    @endphp

    @include('web.pages.header')

    <section class="hero-section page-title-area">
        <div class="container">
            <div class="page-title-content">
                <h2>Order Track</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Order</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order Track</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="lines">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </section>
    @php
        $expectedDate = $order->expected_delivery_date;
        if($expectedDate < date('Y-m-d H:i:s'))
            $expectedDate =date('Y-m-d H:i:s',strtotime('+28 hours'));
    @endphp
    <section class="section-padding gray-bg delievery_status">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 mb-md-4 mb-3">
                    <div class="card status_check rounded">
                        <div class="card-body text-center">
                            @if($order->status == 'delivered')
                            <h4 class="text-success font-weight-bold">Your Order is Delivered</h4>
                            @elseif($order->status == 'cancelled')
                            <h4 class="text-danger font-weight-bold">Your Order is Cancelled</h4>
                            @else
                            <div class="h4 mt-4">Estimated Delivery Date</div>
                            <a href="javascript:;" class="copy-btn" data-toggle="tooltip" data-placement="top" title="Copy URL"><i class="mdi mdi-content-copy"></i></a>
                            <div class="badge badge-pill badge-success">{{$order_tracking != '[]' ? $order_tracking->last()->status : ''}}</div>
                            <h4 class="text-warning">Available Soon!</h4>
                            <!--
                            <h4 class="text-warning">{{$order->expected_delivery_date != '' ? date('d M Y', strtotime($order->expected_delivery_date)) : 'Available Soon!'}}</h4>
                                @if($order->expected_delivery_date == '')
                                <div class="mb-4">
                                    <img class="img-fluid" src="{{asset('public/assets/images/available_soon.png')}}">
                                </div>
                                <strong>Order is under processing</strong>
                                <p>We will update the Estimated Delivery Date once order is processed</p>
                                @endif
                            -->
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="courier_info d-flex align-items-center justify-content-between">
                                <img class="float-left courier_logo img-fluid" src="{{asset($partner->image)}}">
                                <div class="w-100">
                                    <div class="float-left d-flex align-items-center">
                                        <span><b>{{$partner->title}}</b></span>
                                        <!-- <a href="#" class="tracking_id">Support?</a> -->
                                    </div>
                                    <div class="float-right">
                                        <span><b>Tracking ID</b> </span>
                                        <span class="d-block text-success h6">{{$order->awb_number}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="delievery_info ">
                                <div class="delievery_list_wrap clearfix">
                                    <ul>
                                        @forelse($order_tracking as $t)
                                        <li>
                                            <span><b>Activity : </b>
                                                <activity>{{$t->status}} - {{$t->status_code}}</activity>
                                            </span>
                                            <span><b>Location : </b>
                                                <activity>{{$t->location}}</activity>
                                            </span>
                                            <!-- Delivered or RTO delivered -->
                                            <!-- undelivered -->
                                            <?php
                                            $date = date('d M', strtotime($t->updated_date));
                                            $time = date('h:i:A', strtotime($t->updated_date));
                                            ?>
                                            <div class="date_info_wrap">
                                                <span class="date">{{$date}}</span>
                                                <span class="time">{{$time}}</span>
                                            </div>
                                            <i class="circle_icon text-green"></i>
                                        </li>
                                        @empty
                                        <li>No Tracking Details Found</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="hp_cards information_block">
                                <div class="hp_cards_info">
                                    <h5>Order Details</h5>
                                    <div class="row align-items-center">
                                        <div class="col-4"><b>Order ID</b></div>
                                        <div class="col-8 right_info">#{{$order->customer_order_number}}</div>
                                    </div>
                                    <div class="row align-items-center">
                                        <?php
                                        $date = date('d M Y', strtotime($order->awb_assigned_date));
                                        $time = date('h:i A', strtotime($order->awb_assigned_date));
                                        ?>
                                        <div class="col-4"><b>Order Placed On</b></div>
                                        <div class="col-8 right_info">{{$date}}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="recommend_info" id="nps_form">
                                <p><b>How likely are you to recommend Twinnship to friends &amp; family?</b></p>
                                <ul class="pagination nps_rating mt-3">
                                    <li data-rating="0">
                                        <a class="red" href="javascript:void(0);">0</a>
                                    </li>
                                    <li data-rating="1">
                                        <a class="red" href="javascript:void(0);">1</a>
                                    </li>
                                    <li data-rating="2">
                                        <a class="red" href="javascript:void(0);">2</a>
                                    </li>
                                    <li data-rating="3">
                                        <a class="red" href="javascript:void(0);">3</a>
                                    </li>
                                    <li data-rating="4">
                                        <a class="red" href="javascript:void(0);">4</a>
                                    </li>
                                    <li data-rating="5">
                                        <a class="red" href="javascript:void(0);">5</a>
                                    </li>
                                    <li data-rating="6">
                                        <a class="red" href="javascript:void(0);">6</a>
                                    </li>
                                    <li data-rating="7">
                                        <a class="yellow" href="javascript:void(0);">7</a>
                                    </li>
                                    <li data-rating="8">
                                        <a class="yellow" href="javascript:void(0);">8</a>
                                    </li>
                                    <li data-rating="9">
                                        <a class="green" href="javascript:void(0);">9</a>
                                    </li>
                                    <li data-rating="10">
                                        <a class="green" href="javascript:void(0);">10</a>
                                    </li>
                                </ul>
                                <div class="clearfix arrow_wrap_pagination">
                                    <a href="javascript:void(0);" class="extremely_arrow"><i class="fa fa-angle-down  hidden-xs"></i> Not at all likely</a>
                                    <a href="javascript:void(0);" class="likely_arrow"><i class="fa fa-angle-down  hidden-xs"></i>Extremely likely </a>
                                </div>
                                <form>
                                    <label>Remarks</label>
                                    <input type="hidden" name="nps_score" id="nps_score" value="">
                                    <input type="text" name="nps_review" class="form-control" maxlength="250" id="nps_review" placeholder="Please enter your remarks (Max. 250 characters)">
                                    <button class="btn btn-info submit_btn" type="button">Submit</button>
                                </form>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('web.pages.footer')
    @include('web.pages.scripts')
    <script>
        AOS.init({
            disable: 'mobile',
            once: true
        });
    </script>
    <script>
        var copy_path = '{{url('')}}/track-order/{{$order->awb_number}}';
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        $(document).ready(function(){
            $('.copy-btn').click(function(){
                // alert(copy_path);
                navigator.clipboard.writeText(copy_path);
            });
        });
    </script>
</body>

</html>
