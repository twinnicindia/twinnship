<div class="card-body">
    <div class="card-title mb-md-4">
        <h5 class="title">COD Status</h5>
    </div><br>
    <div class="row">
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$cod_total ?? 0}}</div>
                <p class="mb-0 text-muted">COD</p>
            </div>
        </div>
        {{--                                                <div class="col-md-3 col-sm-6">--}}
        {{--                                                    <div class="mb-3 mb-md-4 text-center">--}}
        {{--                                                        <div class="count">{{$cod_available ?? 0}}</div>--}}
        {{--                                                        <p class="mb-0 text-muted">COD Available</p>--}}
        {{--                                                    </div>--}}
        {{--                                                </div>--}}
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$remitted_cod ?? 0}}</div>
                <p class="mb-0 text-muted">COD Remitted</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{($cod_total - $remitted_cod) ?? 0}}</div>
                <p class="mb-0 text-muted">COD Pending</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{isset($nextRemitDate) ? date("D, d M' y",strtotime($nextRemitDate)) : date("D, d M' y",strtotime("next Wednesday"))}}</div>
                <p class="mb-0 text-muted">Next Remit Date</p>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="mb-3 mb-md-4 text-center">
                <div class="count">{{$nextRemitCod ?? 0}}</div>
                <p class="mb-0 text-muted">Next Remit Amount</p>
            </div>
        </div>
    </div>
</div>
