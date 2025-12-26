
    <style>
        .custom-rating {
    width: 500px;
    max-width: 100%;
    height: 100px;
    border-radius: 8px;
    border: none;
    box-shadow: 0 0 25px rgba(0, 0, 0, .2)
}

.progress {
    width: 40px;
    height: 40px;
    line-height: 38px;
    background: none;
    margin: 0 auto;
    box-shadow: none;
    position: relative;
    display: inline-block;
    border: 0
}

.progress.lg {
    width: 50px;
    height: 50px;
    line-height: 50px
}

.progress:after {
    content: "";
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 4px solid #ededed;
    position: absolute;
    top: 0;
    left: 0
}

.progress>span {
    width: 50%;
    height: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    z-index: 1
}

.progress .progress-left {
    left: 0
}

.progress .progress-bar {
    width: 100%;
    height: 100%;
    background: none;
    border-width: 4px;
    border-style: solid;
    position: absolute;
    top: 0
}

.progress[data-percentage="20"] {
    border-color: #ffb43e
}

.progress .progress-left .progress-bar {
    left: 100%;
    border-top-right-radius: 75px;
    border-bottom-right-radius: 75px;
    border-left: 0;
    transform-origin: center left
}

.progress .progress-right {
    right: 0
}

.progress .progress-right .progress-bar {
    left: -100%;
    border-top-left-radius: 75px;
    border-bottom-left-radius: 75px;
    border-right: 0;
    transform-origin: center right
}

.progress .progress-value {
    display: flex;
    border-radius: 50%;
    font-size: 12px;
    text-align: center;
    line-height: 12px;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #000;
    font-weight: bold
}

.progress.bad .progress-right .progress-bar {
    -webkit-animation: loading-1 .75s linear forwards;
    animation: loading-1 .75s linear forwards;
    border-color: red
}

.progress.bad .progress-left .progress-bar {
    -webkit-animation: 0;
    animation: 0
}

.progress.average .progress-right .progress-bar {
    -webkit-animation: loading-2 .75s linear forwards;
    animation: loading-2 .75s linear forwards;
    border-color: #ffb43e
}

.progress.average .progress-left .progress-bar {
    -webkit-animation: 0;
    animation: 0
}

.progress.amberA .progress-right .progress-bar {
    -webkit-animation: loading-2-alt .75s linear forwards;
    animation: loading-2-alt .75s linear forwards;
    border-color: #ffb43e
}

.progress.amberA .progress-left .progress-bar {
    -webkit-animation: loading-2-amb-left 1.5s linear forwards .75s;
    animation: loading-2-amb-left 1.5s linear forwards .75s;
    border-color: #ffb43e
}

.progress.green .progress-right .progress-bar {
    -webkit-animation: loading-3lt .75s linear forwards;
    animation: loading-3lt .75s linear forwards;
    border-color: #60b636
}

.progress.green .progress-left .progress-bar {
    -webkit-animation: loading-green 1.5s linear forwards .75s;
    animation: loading-green 1.5s linear forwards .75s;
    border-color: #60b636
}

.progress.agrade .progress-right .progress-bar {
    -webkit-animation: loading-3lt .75s linear forwards;
    animation: loading-3lt .75s linear forwards;
    border-color: #60b636
}

.progress.agrade .progress-left .progress-bar {
    -webkit-animation: loading-3 1.5s linear forwards .75s;
    animation: loading-3 1.5s linear forwards .75s;
    border-color: #60b636
}

.progress.agradePlus .progress-left .progress-bar {
    -webkit-animation: loading-3lt 1.5s linear forwards .75s !important;
    animation: loading-3lt 1.5s linear forwards .75s !important;
    border-color: #60b636
}

.progress.agradeSimilar5 .progress-left .progress-bar {
    -webkit-animation: loading-4 1.5s linear forwards .75s;
    animation: loading-4 1.5s linear forwards .75s;
    border-color: #60b636
}

@-webkit-keyframes loading-1 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(72deg)
    }
}

@keyframes loading-1 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(72deg)
    }
}

@-webkit-keyframes loading-2-alt {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(180deg)
    }
}

@keyframes loading-2-alt {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(180deg)
    }
}

@-webkit-keyframes loading-2-amb-left {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(36deg)
    }
}

@keyframes loading-2-amb-left {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(36deg)
    }
}

@-webkit-keyframes loading-2 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(144deg)
    }
}

@keyframes loading-2 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(144deg)
    }
}

@-webkit-keyframes loading-3lt {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(180deg)
    }
}

@keyframes loading-3lt {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(180deg)
    }
}

@-webkit-keyframes loading-3 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(150deg)
    }
}

@keyframes loading-3 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(150deg)
    }
}

@-webkit-keyframes loading-4 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(170deg)
    }
}

@keyframes loading-4 {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(170deg)
    }
}

@-webkit-keyframes loading-green {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(108deg)
    }
}

@keyframes loading-green {
    0% {
        transform: rotate(0)
    }

    to {
        transform: rotate(108deg)
    }
}

.progress-bar {
    border-width: 5px !important
}

.green-bar .progress-bar {
    border-color: #008e27
}

.yellow-bar .progress-bar {
    border-color: #deb400
}

.overall_rating:hover~.break_rating {
    display: flex;
    opacity: 1;
    visibility: visible
}

    </style>

    @if(Session()->get('MySeller')->id == 0)
        <input type="hidden" name="order_id" id="order_id_single">
        @csrf
        <input type="hidden" id="shipping_partner" name="partner">
        <input type="hidden" id="shipping_charge_single" name="shipping_charge_single">
        <input type="hidden" id="cod_charge_single" name="cod_charge_single">
        <input type="hidden" id="early_cod_charge" name="early_cod_charge" value="">
        <input type="hidden" id="gst_charge" name="gst_charge" value="">
        <input type="hidden" id="rto_charge_single" name="rto_charge" value="">
        <input type="hidden" id="total_charge" name="total_charge" value="">
        <input type="hidden" id="order_zone" name="order_zone" value="{{$zone}}">

        <input type="hidden" id="session_rto_charge" value="{{Session()->get('MySeller')->rto_charge}}">
        <input type="hidden" id="session_early_cod" value="{{Session()->get('MySeller')->early_cod_charge}}">
        <div class="row" id="partners">
            <div class="col-lg-12 col-md-6 mb-4">
                <div class="stats-box-new bg-white card border-0 rounded-10 mb-4 p-3">
                    @foreach($rates as $key=>$p)
                            <?php
                            $shipping_charge = $p->final_rate;
                            if(strtolower($o_type) == 'reverse'){
                                $shipping_charge  = ($shipping_charge * Session()->get('MySeller')->reverse_charge) / 100;
                                if($is_qc == 'y')
                                    $shipping_charge+= ($shipping_charge * $config->qc_charges ) / 100;
                            }
                            $shipping_charge += ($shipping_charge * 18) / 100;
                            $cod_maintenance = $p->cod_maintenance;

                            if (strtolower($order_type) == 'prepaid') {
                                $cod_charge = "0";
                                $early_cod = "0";
                            } else {
                                $invoiceAmount = !empty($orderData->collectable_amount) ? $orderData->collectable_amount : $orderData->invoice_amount;
                                $cod_charge = ($invoiceAmount * $cod_maintenance) / 100;
                                if ($cod_charge < $p->cod_charge)
                                    $cod_charge = $p->cod_charge;
                                $cod_charge += ($cod_charge * 18) / 100;
                                $early_cod = ($invoiceAmount * Session()->get('MySeller')->early_cod_charge) / 100;
                                $early_cod += ($early_cod * 18) / 100;
                            }
                            $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
                            $rto_charge = ($shipping_charge) * Session()->get('MySeller')->rto_charge / 100;
                            $total_charge = 0.0;
                            if(Session()->get('MySeller')->floating_value_flag == 'y')
                                $total_charge = $shipping_charge + $cod_charge + $early_cod;
                            else
                                $total_charge = round($shipping_charge + $cod_charge + $early_cod);
                            ?>
                        @if($key==0)
                            <script>
                                var shipping_charge = {{round($shipping_charge,2)}};
                                var cod_charge = {{round($cod_charge,2)}};
                                var early_cod_charge = {{round($early_cod,2)}};
                                var gst_charge = {{round($gst_charge,2)}};
                                var rto_charge = {{round($rto_charge,2)}};
                                var total_charge = {{$total_charge}};
                                $('#shipping_charge_single').val(shipping_charge);
                                $('#cod_charge_single').val(cod_charge);
                                $('#early_cod_charge').val(early_cod_charge);
                                $('#gst_charge').val(gst_charge);
                                $('#rto_charge_single').val(rto_charge);
                                $('#total_charge').val(total_charge);
                            </script>
                        @endif
                        <div class="row align-items-center">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="icon transition">
                                            <img class="rounded-circle mb-3" style="height: 100px; width: 100px;"
                                                src="{{asset($p->image)}}" alt="admin">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <p class="text-dark mb-3">{{$p->title}}</p>
                                            <p class="text-dark mb-3">RTO Charges: {{$rto_charge}}</p>
                                            <p class="text-dark mb-3">Delivering Excellence, Every Mile</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="d-flex flex-column">
                                        <p class="text-dark mb-3"> <b>₹ {{$total_charge}} </b><small>(include all taxes)</small>
                                        </p>
                                        <p class="text-dark mb-3">Freight Charges <small> <b>₹ {{round($shipping_charge,2)}} </b></small></p>
                                        <p class="text-dark mb-3">+ Cod Charge<small> <b>₹ {{round($cod_charge,2)}} </b></small></p>
                                        <p class="text-dark">+ Early Cod Charge<small> <b>₹ {{round($early_cod,2)}} </b></small></p>
                                        <p class="text-dark">+ GST Charges<small> <b>₹ {{round($gst_charge,2)}} </b></small></p>
                                        <p class="text-dark">RTO Charges<small> <b>₹ {{round($rto_charge,2)}} </b></small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-4 mb-3">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <button type="button"
                                                class="btn btn-primary text-white fw-semibold me-2 ShipOrderBtn" data-keyword="{{$p->keyword}}" data-id="{{$p->id}}">Ship Order</button>
                                        <br>
                                        <p class="text-dark">
                                            EDD: <span style="font-weight: bold;">{{date('d-m-Y',strtotime("+".($p->{"zone_".strtolower($zone)} ?? 7)." days"))}}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        <input type="hidden" name="order_id" id="order_id_single">
        @csrf
        <input type="hidden" id="shipping_partner" name="partner">
        <input type="hidden" id="shipping_charge_single" name="shipping_charge_single">
        <input type="hidden" id="cod_charge_single" name="cod_charge_single">
        <input type="hidden" id="early_cod_charge" name="early_cod_charge" value="">
        <input type="hidden" id="gst_charge" name="gst_charge" value="">
        <input type="hidden" id="rto_charge_single" name="rto_charge" value="">
        <input type="hidden" id="total_charge" name="total_charge" value="">
        <input type="hidden" id="order_zone" name="order_zone" value="{{$zone}}">

        <input type="hidden" id="session_rto_charge" value="{{Session()->get('MySeller')->rto_charge}}">
        <input type="hidden" id="session_early_cod" value="{{Session()->get('MySeller')->early_cod_charge}}">
        <div id="partners">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <table class="table">
                    <thead class="sticky-header">
                        <tr class="text-center rounded-10">
                            <th class="text-center">Courier Partner</th>
                            <th class="text-center">Rating</th>
                            <th class="text-center">RTO/COD Charges</th>
                            <th class="text-center">Estimated Delivery</th>
                            <!-- <th class="text-center">Chargeable Weight</th> -->
                            <th class="text-center">Charges</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $key=>$p)
                            <?php
                                $shipping_charge = $p->final_rate;
                                if(strtolower($o_type) == 'reverse'){
                                    $shipping_charge  = ($shipping_charge * Session()->get('MySeller')->reverse_charge) / 100;
                                    if($is_qc == 'y')
                                        $shipping_charge+= ($shipping_charge * $config->qc_charges ) / 100;
                                }
                                $shipping_charge += ($shipping_charge * 18) / 100;
                                $cod_maintenance = $p->cod_maintenance;

                                if (strtolower($order_type) == 'prepaid') {
                                    $cod_charge = "0";
                                    $early_cod = "0";
                                } else {
                                    $cod_charge = ($invoice_amount * $cod_maintenance) / 100;
                                    if ($cod_charge < $p->cod_charge)
                                        $cod_charge = $p->cod_charge;
                                    $cod_charge += ($cod_charge * 18) / 100;
                                    $early_cod = ($invoice_amount * Session()->get('MySeller')->early_cod_charge) / 100;
                                    $early_cod += ($early_cod * 18) / 100;
                                }
                                $gst_charge = ($shipping_charge + $cod_charge + $early_cod) * 18 / 100;
                                $rto_charge = ($shipping_charge) * Session()->get('MySeller')->rto_charge / 100;
                                $total_charge = 0.0;
                                if(Session()->get('MySeller')->floating_value_flag == 'y')
                                    $total_charge = $shipping_charge + $cod_charge + $early_cod;
                                else
                                    $total_charge = round($shipping_charge + $cod_charge + $early_cod);
                            ?>
                            @if($key==0)
                                <script>
                                    var shipping_charge = {{round($shipping_charge,2)}};
                                    var cod_charge = {{round($cod_charge,2)}};
                                    var early_cod_charge = {{round($early_cod,2)}};
                                    var gst_charge = {{round($gst_charge,2)}};
                                    var rto_charge = {{round($rto_charge,2)}};
                                    var total_charge = {{$total_charge}};
                                    $('#shipping_charge_single').val(shipping_charge);
                                    $('#cod_charge_single').val(cod_charge);
                                    $('#early_cod_charge').val(early_cod_charge);
                                    $('#gst_charge').val(gst_charge);
                                    $('#rto_charge_single').val(rto_charge);
                                    $('#total_charge').val(total_charge);
                                </script>
                            @endif
                            <tr class="text-center rounded-10 card-row">
                                <td>
                                    <div class="icon transition">
                                        <img class="rounded-circle mb-3" style="height: 50px; width: 50px;"
                                            src="{{asset($p->image)}}" alt="admin">
                                    </div>
                                    <br>
                                    <div class="cell-inside-box">
                                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$p->title}}</span></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress blue overall_rating agrade green amberA average" data-class="custom-rating tooltip-type-1">
                                        <span class="progress-left"><span class="progress-bar"></span></span>
                                        <span class="progress-right"><span class="progress-bar"></span></span>
                                        <div class="progress-value greyish-brown-four fs-12px">
                                            @php
                                                $integerPart = rand(3, 4);
                                                $decimalPart = rand(0, 9);
                                                $randomRating = $integerPart . '.' . $decimalPart;
                                            @endphp
                                            {{ $randomRating }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="cell-inside-box">
                                        <p class="text-dark">Cod Charge<small> <b>₹ {{round($cod_charge,2)}} </b></small></p>
                                        <p class="text-dark">Early Cod Charge<small> <b>₹ {{round($early_cod,2)}} </b></small></p>
                                        <p class="text-dark">GST Charges<small> <b>₹ {{round($gst_charge,2)}} </b></small></p>
                                        <p class="text-dark">RTO Charges<small> <b>₹ {{round($rto_charge,2)}} </b></small></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-inside-box">
                                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{date('M j, Y',strtotime("+".($p->{"zone_".strtolower($zone)} ?? 7)." days"))}}</span></p>
                                    </div>
                                </td>
                                <!-- <td>
                                    <div class="cell-inside-box">
                                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">0.5 Kg</span></p>
                                    </div>
                                </td> -->
                                <td>
                                    <div class="cell-inside-box">
                                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">₹{{$total_charge}}</span></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-primary text-white fw-semibold ShipOrderBtn" data-keyword="{{$p->keyword}}" data-id="{{$p->id}}" title="Ship Order">Ship Now</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
