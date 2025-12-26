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
                        <td>{{$c->b_customer_name}}</td>
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
                        <td>{{$c->b_customer_name}}</td>
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

<script>
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
