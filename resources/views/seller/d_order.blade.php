<div class="row mt-2">
    <div class="col-md-12">
        <div class="card card-shadow">
            <div class="card-body">
                <!-- <div class="card-title mb-md-4">
                                            <h6 class="title">Shipment Overview by Courier <span class="float-right text-muted">Last 30 Days</span></h6>
                                        </div> -->
                <div class="table-responsive" id="customOrderDate">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date
                                    <div class="filter">
                                        <a data-toggle="collapse" href="#NDRDateFilter" role="button" aria-expanded="false" aria-controls="NDRDateFilter"><i class="fa fa-calendar-alt fa-xs"></i></a>
                                        <div class="collapse filter-collapse" id="NDRDateFilter">
                                            <label>Filter by Date</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input type="date" class="form-control" name="custom_start_date" id="custom_start_date" value="{{date('Y-m-d',strtotime('-4 days'))}}" max="{{date('Y-m-d',strtotime('-1 days'))}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input type="date" class="form-control" name="custom_end_date" id="custom_end_date" value="{{date('Y-m-d',strtotime('-1 days'))}}" max="{{date('Y-m-d',strtotime('-1 days'))}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" data-modal="NDRDateFilter" class="custom_date_reset btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                                            <button type="button" id="get_report" data-modal="NDRDateFilter" class="btn btn-primary btm-sm mt-2 ml-0">Apply</button>
                                        </div>
                                    </div>
                                </th>
                                <th>Total Shipment</th>
                                <th>Yet to Pick</th>
                                <th>Picked</th>
                                <th>In-Transit</th>
                                <th>Delivered</th>
                                <th>NDR Pending</th>
                                <th>NDR Delivered</th>
                                <th>RTO</th>
                                <th>Lost/Damaged</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allDays as $p)
                                <tr>
                                    <td>{{$p ?? ""}}</td>
                                    <td>{{$partner_total[$p] ?? 0}}</td>
                                    <td>{{$partner_unscheduled[$p] ?? 0}}</td>
                                    <td>{{$partner_scheduled[$p] ?? 0}}</td>
                                    <td>{{$partner_intransit[$p] ?? 0}}</td>
                                    <td>{{$partner_delivered[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_pending[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_delivered[$p] ?? 0}}</td>
                                    <td>{{$partner_ndr_rto[$p] ?? 0}}</td>
                                    <td>{{$partner_damaged[$p] ?? 0}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h5 class="title">Prepaid vs. COD Orders</h5>
                </div>
                <div id="CodvsPrepaidChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h5 class="title">Buyer Demographics</h5>
                </div>
                <div id="BuyerDemographicsChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3 mb-md-4">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Popular Orders Location
                        <div class="form-group float-right">
                            <!-- <label>OrderBy </label> -->
                            <select id="locationFilter" style="font-size: inherit !important;">
                                <option value="orderCount" selected>Count</option>
                                <option value="revenue">Revenue</option>
                            </select>
                        </div>
                    </h6>
                </div>
                <div class="table-responsive h-300" id="locationorderbyCount" style="min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>States</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popular_location_order as $p)
                            <tr>
                                <td class="text-capitalize">{{$p->s_state}}</td>
                                <td>{{$p->total_order}}</td>
                            </tr>
                           @empty
                           <tr>
                                <td colspan="4">No Data Found</td>
                           </tr>
                           @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="table-responsive h-300" id="locationorderbyRevenue" style="display: none; min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>States</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popular_location_revenue as $p)
                            <tr>
                                <td class="text-capitalize">{{$p->s_state}}</td>
                                <td>₹ {{$p->total_amount}}</td>
                            </tr>
                           @empty
                           <tr>
                           <td colspan="4">No Data Found</td>
                           </tr>
                           @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top 10 Customers
                        <div class="form-group float-right">
                            <!-- <label>OrderBy </label> -->
                            <select id="customerFilter">
                                <option value="orderCount" selected>Count</option>
                                <option value="revenue">Revenue</option>
                            </select>
                        </div>
                    </h6>
                </div>
                <div class="table-responsive h-300" id="customerorderByCount"  style="min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Order Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_customer_order as $c)
                            <tr>
                                <td>{{$c->s_customer_name}}</td>
                                <td>{{$c->total_order}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">No Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive h-300" id="customerorderByRevanue" style="display: none; min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_customer_revenue as $c)
                            <tr>
                                <td>{{$c->s_customer_name}}</td>
                                <td>₹ {{$c->total_amount}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">No Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">Top 10 Products
                        <div class="form-group float-right">
                            <!-- <label>OrderBy </label> -->
                            <select id="productFilter">
                                <option value="orderCount" selected>Unit Sold</option>
                                <option value="revenue">Revenue</option>
                            </select>
                        </div>
                    </h6>
                </div>
                <div class="table-responsive h-300" id="productOrderByunitsold" style="min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Unit Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_product_order as $p)
                            <tr>
                                <td class="text-capitalize">{{$p->product_name}}</td>
                                <td>{{$p->unit_sold}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">No Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive h-300" id="productOrderByrevenue" style="display: none; min-height: 322px;white-space: nowrap;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_product_revenue as $p)
                            <tr>
                                <td class="text-capitalize">{{$p->product_name}}</td>
                                <td>₹  {{$p->total_revenue}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">No Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //CodvsPrepaidChart
    anychart.onDocumentReady(function() {
        var data = [{
                x: "Cash On Delivery",
                value: "{{$cod_order}}"
            },
            {
                x: "Prepaid",
                value: "{{$prepaid_order}}"
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("CodvsPrepaidChart");
        chart.draw();
    });

    //CodvsPrepaidChart
    anychart.onDocumentReady(function() {
        var data = [{
                x: "Male",
                value: 6
            },
            {
                x: "Female",
                value: 2
            },
            {
                x: "Undetermined",
                value: 1
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("BuyerDemographicsChart");
        chart.draw();
    });


    $('#locationFilter').on('change',function(){
        var value = $(this).val();
            if(value == 'revenue'){
                $('#locationorderbyRevenue').show();
                $('#locationorderbyCount').hide();
            }else{
                $('#locationorderbyRevenue').hide();
                $('#locationorderbyCount').show();
            }
    })

    $('#customerFilter').on('change',function(){
        var value = $(this).val();
            if(value == 'revenue'){
                $('#customerorderByRevanue').show();
                $('#customerorderByCount').hide();
            }else{
                $('#customerorderByRevanue').hide();
                $('#customerorderByCount').show();
            }
    })

    $('#productFilter').on('change',function(){
        var value = $(this).val();
            if(value == 'revenue'){
                $('#productOrderByrevenue').show();
                $('#productOrderByunitsold').hide();
            }else{
                $('#productOrderByrevenue').hide();
                $('#productOrderByunitsold').show();
            }
    })
</script>
