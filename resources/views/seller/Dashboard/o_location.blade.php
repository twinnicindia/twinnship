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

<script>
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
</script>
