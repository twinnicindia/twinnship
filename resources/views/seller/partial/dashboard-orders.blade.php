<div class="row justify-content-center">
    <div class="col-xxl-12 mb-4">
        <div class="card bg-white border-0 rounded-10 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-20 mb-20">
                    <h4 class="fw-semibold fs-18 mb-0">Orders Count</h4>
                    <div class="action-opt">
                        <button class="btn bg-transparent p-0" type="button">
                            <span>Last 30 Days</span>
                        </button>
                    </div>
                </div>
                <div class="default-table-area project-list">
                    <div class="table-responsive  scroll-bar active">
                        <table class="table align-middle ">
                            <thead>
                            <tr>
                                <th scope="col" class="text-dark">Date</th>
                                <th scope="col" class="text-dark">Total Orders</th>
                                <th scope="col" class="text-dark">Pickup Scheduled</th>
                                <th scope="col" class="text-dark">Pickup Unscheduled</th>
                                <th scope="col" class="text-dark">In-Transit</th>
                                <th scope="col" class="text-dark">Delivered</th>
                                <th scope="col" class="text-dark">Undelivered</th>
                                <th scope="col" class="text-dark">RTO</th>
                                <th scope="col" class="text-dark">Lost/Damaged</th>
                                <th scope="col" class="text-dark">Total Shipment</th>
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

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
            <div style="display: flex; justify-content: space-between;">
                <p><b class="text-dark fw-semibold fs-18 mb-0">Prepaid vs. COD Orders</b></p>

            </div>
            <div class="d-flex justify-content-center">
                <div id="CodvsPrepaidChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
            <div style="display: flex; justify-content: space-between;">
                <p><b class="text-dark fw-semibold fs-18 mb-0">Buyer Demographics</b></p>

            </div>
            <div class="d-flex justify-content-center">
                <div id="BuyerDemographicsChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
            <div style="display: flex; justify-content: space-between;">
                <p><b class="text-dark fw-semibold fs-18 mb-0">Most Popular Orders Location</b></p>
                <div class="form-group float-right">
                    <!-- <label>OrderBy </label> -->
                    <select id="locationFilter" style="font-size: inherit !important;">
                        <option value="orderCount" selected>Count</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="" id="locationorderbyCount">
                <div class="table-responsive  scroll-bar active">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">States</th>
                            <th scope="col">Order Count</th>
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
                                <td colspan="2">No Data Found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="" id="locationorderbyRevenue" style="display: none">
                <div class="table-responsive  scroll-bar">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">States</th>
                            <th scope="col">Order Count</th>
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
                                <td colspan="2">No Data Found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 mb-4">
        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
            <div style="display: flex; justify-content: space-between;">
                <p class="text-dark fw-semibold fs-18 mb-0">Top 10 Customers</p>
                <div class="form-group float-right">
                    <!-- <label>OrderBy </label> -->
                    <select id="customerFilter">
                        <option value="orderCount" selected>Count</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="" id="customerorderByCount">
                <div class="table-responsive  scroll-bar active">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Order Count</th>
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
                                <td colspan="2">No Data Found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="" id="customerorderByRevanue" style="display: none">
                <div class="table-responsive  scroll-bar">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Revenue</th>
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
                                <td colspan="2">No Data Found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-6 col-md-6 mb-4">
        <div class="stats-box bg-white card border-0 rounded-10 mb-4 p-3">
            <div style="display: flex; justify-content: space-between;">
                <p class="text-dark fw-semibold fs-18 mb-0">Top 10 Products</p>
                <div class="form-group float-right">
                    <!-- <label>OrderBy </label> -->
                    <select id="productFilter">
                        <option value="orderCount" selected>Unit Sold</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="" id="productOrderByunitsold">
                <div class="table-responsive  scroll-bar">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Product Name</th>
                            <th scope="col">Unit Sold</th>
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
            </div>
            <div class="" id="productOrderByrevenue" style="display: none">
                <div class="table-responsive  scroll-bar">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Product Name</th>
                            <th scope="col">Revenue</th>
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
        if(value === 'revenue'){
            $('#locationorderbyRevenue').show();
            $('#locationorderbyCount').hide();
        }else{
            $('#locationorderbyRevenue').hide();
            $('#locationorderbyCount').show();
        }
    })

    $('#customerFilter').on('change',function(){
        var value = $(this).val();
        if(value === 'revenue'){
            $('#customerorderByRevanue').show();
            $('#customerorderByCount').hide();
        }else{
            $('#customerorderByRevanue').hide();
            $('#customerorderByCount').show();
        }
    })

    $('#productFilter').on('change',function(){
        var value = $(this).val();
        if(value === 'revenue'){
            $('#productOrderByrevenue').show();
            $('#productOrderByunitsold').hide();
        }else{
            $('#productOrderByrevenue').hide();
            $('#productOrderByunitsold').show();
        }
    })
</script>
