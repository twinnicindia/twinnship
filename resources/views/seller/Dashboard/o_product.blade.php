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
<script>
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
