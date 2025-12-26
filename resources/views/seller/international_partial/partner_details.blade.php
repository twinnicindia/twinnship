    <input type="hidden" name="order_id" id="order_id_single" value="{{$order->id}}">
    @csrf
    <input type="hidden" id="shipping_charge_single" name="shipping_charge_single">
    <input type="hidden" id="cod_charge_single" name="cod_charge_single">
    <input type="hidden" id="early_cod_charge" name="early_cod_charge" value="">
    <input type="hidden" id="gst_charge" name="gst_charge" value="">
    <input type="hidden" id="rto_charge_single" name="rto_charge" value="">
    <input type="hidden" id="total_charge" name="total_charge" value="">
    <input type="hidden" id="order_zone" name="order_zone" value="A">

    <input type="hidden" id="session_rto_charge" value="{{Session()->get('MySeller')->rto_charge}}">
    <input type="hidden" id="session_early_cod" value="{{Session()->get('MySeller')->early_cod_charge}}">

    <div class="row" id="partners">
        @foreach($partners as $p)
        <div class="col-md-12">
            <div class="form-row pt-3">
                <div class="custom-control custom-radio col-sm-12">
                    <div class="card mb-2 p-4">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="radio" required="" id="partner_{{$p->id}}" name="partner" data-id="{{$p->id}}" class="ml-2 custom-control-input partner_select" value="{{$p->keyword}}" checked>
                                <label class="custom-control-label h6 mb-2" for="partner_{{$p->id}}">{{$p->title}}</label><br>
                                <img src="{{asset($p->image)}}" style="height: 100px;border-radius:5px;width: 100px">
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col">
                                        <div class="set-size charts-container">
                                            <div class="pie-wrapper progress-75 style-2">
                                                <span class="label">4.5</span>
                                                <div class="pie">
                                                    <div class="left-side half-circle"></div>
                                                    <div class="right-side half-circle"></div>
                                                </div>
                                                <div class="shadow">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="f-11 text-center">Pickup Performance</p>
                                    </div>
                                    <div class="col">
                                        <div class="set-size charts-container">
                                            <div class="pie-wrapper progress-75 style-2">
                                                <span class="label">4.1</span>
                                                <div class="pie">
                                                    <div class="left-side half-circle"></div>
                                                    <div class="right-side half-circle"></div>
                                                </div>
                                                <div class="shadow">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="f-11 text-center">Delivery Performance</p>
                                    </div>
                                    <div class="col">
                                        <div class="set-size charts-container">
                                            <div class="pie-wrapper progress-75 style-2">
                                                <span class="label">4.2</span>
                                                <div class="pie">
                                                    <div class="left-side half-circle"></div>
                                                    <div class="right-side half-circle"></div>
                                                </div>
                                                <div class="shadow">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="f-11 text-center">NDR Performance</p>
                                    </div>
                                    <div class="col">
                                        <div class="set-size charts-container">
                                            <div class="pie-wrapper progress-75 style-2">
                                                <span class="label">4.2</span>
                                                <div class="pie">
                                                    <div class="left-side half-circle"></div>
                                                    <div class="right-side half-circle"></div>
                                                </div>
                                                <div class="shadow">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="f-11 text-center">RTO Performance</p>
                                    </div>
                                    <div class="col">
                                        <div class="set-size charts-container">
                                            <div class="pie-wrapper progress-75 style-2">
                                                <span class="label">4.2</span>
                                                <div class="pie">
                                                    <div class="left-side half-circle"></div>
                                                    <div class="right-side half-circle"></div>
                                                </div>
                                                <div class="shadow">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="f-11 text-center">Overall Rating</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="grey-light">
                                    <p class="mb-0 h-6 font-weight-bold" style="font-size: 21px;">₹
                                        <span id="total_charge_800">{{$p->rate}}</span>
                                        <button type="button" data-id="{{$p->id}}" class="btn btn-info btn-sm float-right ShipOrderBtn" style="margin-top:-8px;">
                                            Ship
                                        </button>
                                    </p>
                                    <small>(Inclusive of all taxes)</small>

                                    <p class="mb-0 f-14">Freight Charges: <b>₹
                                            <span id="shipping_charge_800">{{$p->rate}}</span></b></p>
                                    <p class="mb-0 f-14">+ COD Charges: <b>₹
                                            <span id="cod_charge_0">0</span></b></p>
                                    <p class="mb-0 f-14">+ Early COD Charges: <b>₹ <span id="early_cod_charge_0">0</span></b>
                                    </p>
{{--                                    <p style="display:none;" class="mb-0 f-14">+ GST Charges(18%): <b>₹ <span id="gst_charge_0">0</span></b>--}}
{{--                                    </p>--}}
                                    <p class="mb-0 f-14">RTO Charges: <b>₹ <span id="rto_charge_795">{{$p->rate}}</span></b>
                                    </p>
<!--                                    --><?php //$object = (array) $p; ?>
                                    <p class="mb-0 f-14">EDD: <span style="font-weight: bold;">{{date('d-m-Y',strtotime("+ 8 days"))}}</span></b>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
