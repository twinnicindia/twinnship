<div class="col-md-6">
    <div class="card card-shadow">
        <div class="card-body">
            <div class="card-title mb-md-4">
                <h6 class="title">Top RTO - Courier</h6>
            </div>
            <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>RTO Count</th>
                        <th>Percentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($top_courier as $c)
                        <tr>
                            <td>{{$PartnerName[$c->courier_partner]}}</td>
                            <td>{{$c->total_order}}</td>
                            <td>{{$total_order > 0 ? (round(($c->total_order / $total_order) *100,2)) : 0}} %</td>
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
                <h6 class="title">Top RTO - Customer</h6>
            </div>
            <div class="table-responsive h-200" style="min-height: 200px;max-height: 200px;white-space: nowrap;">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>RTO Count</th>
                        <th>Percentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($top_customer as $c)
                        <tr>
                            <td>{{$c->b_customer_name}}</td>
                            <td>{{$c->total_order}}</td>
                            <td>{{$total_order > 0 ? (round(($c->total_order / $total_order) *100,2)) : 0}} %</td>
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
