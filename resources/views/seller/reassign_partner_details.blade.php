    <input type="hidden" name="order_id" id="order_id_single">
    @csrf

    <div class="row" id="partners">
        <div class="col-md-12">
            <div class="form-row pt-3">
                <div class="custom-control custom-radio col-sm-12">
                    @foreach($partners as $partner)
                    <div class="card mb-2 p-4">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="radio" required="" id="partner_{{$partner->id}}" name="partner" data-id="{{$partner->id}}" class="ml-2 custom-control-input partner_select" value="{{$partner->keyword}}">
                                <label class="custom-control-label h6 mb-2" for="partner_{{$partner->id}}">{{$partner->title}}</label><br>
                                <img src="{{asset($partner->image)}}" style="height: 100px;border-radius:5px;">
                            </div>
                            <div class="col-md-8">
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
                            <div class="col-md-2">
                                <div class="grey-light">
                                    <p class="mb-0 h-6 font-weight-bold" style="font-size: 21px;">
                                        <button type="button" data-id="{{$partner->id}}" class="btn btn-info btn-sm float-right ShipOrderBtn" style="margin-top:-8px;">
                                            Ship
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
